"""Dựng bảng ánh xạ mã xã CŨ (bảng `wards` 3 cấp của dinhduong) -> mã xã MỚI 2026 (`vn_wards`).

Đầu vào:
  GDB_SHP/DiaPhan_Xa_2026.dbf   — xã mới, cột `ghiChu` liệt kê các xã cũ đã gộp vào
  old_wards.tsv                 — danh sách xã cũ xuất từ DB dinhduong (xem docs/phien-dia-phuong-cu-moi.md)

Đầu ra:
  ward_mapping_2026.sql  — bảng vn_ward_mappings (nạp vào MySQL)
  ward_mapping_2026.tsv  — cùng dữ liệu, để rà soát bằng Excel

Chạy:  python -X utf8 database/data/diaphan_2026/tools/build_ward_mapping.py
"""
from __future__ import annotations

import collections
import csv
import re
import unicodedata
from datetime import datetime
from pathlib import Path

from generate_address_combobox_sql import read_dbf, sql_str

ROOT = Path(__file__).resolve().parents[1]
DBF = ROOT / "GDB_SHP" / "DiaPhan_Xa_2026.dbf"
OLD_TSV = ROOT / "old_wards.tsv"
OUT_SQL = ROOT / "ward_mapping_2026.sql"
OUT_TSV = ROOT / "ward_mapping_2026.tsv"

UNIT = r"(thi tran|thi xa|phuong|xa|dac khu)"
ENTRY = re.compile(rf"^{UNIT}\s+(.+?)\s*(?:\((.*)\))?$")


def norm(s: str) -> str:
    """Thường hoá + bỏ dấu tiếng Việt + gộp khoảng trắng."""
    s = unicodedata.normalize("NFD", (s or "").lower()).replace("đ", "d")
    s = "".join(c for c in s if unicodedata.category(c) != "Mn")
    return re.sub(r"\s+", " ", s).strip()


def compact(name: str) -> str:
    """Bỏ mọi ký tự không phải chữ/số: "Đạ M' Rong" == "Đạ M'Rông" == "da mrong"."""
    return re.sub(r"[^a-z0-9]", "", name)


def old_key(full_name: str) -> str:
    m = re.match(rf"^{UNIT}\s+(.+)$", norm(full_name))
    return f"{m.group(1)} {compact(m.group(2))}" if m else norm(full_name)


def split_top(s: str) -> list[str]:
    """Tách theo dấu phẩy/chấm phẩy nằm ngoài ngoặc."""
    out, depth, cur = [], 0, ""
    for ch in s:
        if ch == "(":
            depth += 1
        elif ch == ")":
            depth = max(0, depth - 1)
        if ch in ",;" and depth == 0:
            out.append(cur)
            cur = ""
        else:
            cur += ch
    out.append(cur)
    return [x.strip() for x in out if x.strip()]


def build_index(new_wards: list[dict]):
    """Trả (idx, own):
    idx: key (loại + tên cũ rút gọn) -> [(xã mới, chú thích trong ngoặc)] lấy từ ghiChu
    own: key tên CHÍNH xã mới -> [(xã mới, "")] — dự phòng cho xã "Không sáp nhập"/giữ tên."""
    idx: dict[str, list[tuple[dict, str]]] = collections.defaultdict(list)
    own: dict[str, list[tuple[dict, str]]] = collections.defaultdict(list)
    for w in new_wards:
        own[old_key(w["full"])].append((w, ""))
        for e in split_top(w["note"]):
            m = ENTRY.match(norm(e))
            if m:
                idx[f"{m.group(1)} {compact(m.group(2))}"].append((w, m.group(3) or ""))
    return idx, own


def main() -> None:
    new_wards = [
        {"code": r["maXa_BNV"], "prov": r["maTinh_BNV"], "full": r["tenXa"], "note": r["ghiChu"]}
        for r in read_dbf(DBF)
    ]
    idx, own = build_index(new_wards)
    old = list(csv.DictReader(OLD_TSV.open(encoding="utf-8"), delimiter="\t"))

    # Bước 1 — tỉnh cũ -> tỉnh mới: mỗi xã cũ khớp được "bỏ phiếu" cho tỉnh mới chứa nó.
    votes: dict[str, collections.Counter] = collections.defaultdict(collections.Counter)
    for o in old:
        for w, _ in idx.get(old_key(o["full_name"]), []):
            votes[o["province_code"]][w["prov"]] += 1
    prov_map = {p: c.most_common(1)[0][0] for p, c in votes.items()}

    # Bước 2 — xã cũ -> xã mới trong tỉnh mới tương ứng.
    rows, stat = [], collections.Counter()
    for o in old:
        new_prov = prov_map.get(o["province_code"], "")
        key = old_key(o["full_name"])
        cands = [(w, q) for w, q in idx.get(key, []) if w["prov"] == new_prov]
        if not cands:  # dự phòng: xã giữ nguyên tên (ghiChu "Không sáp nhập" / trống)
            cands = [(w, q) for w, q in own.get(key, []) if w["prov"] == new_prov]
        if len(cands) > 1:  # trùng tên -> lọc theo huyện cũ ghi trong ngoặc, vd "(huyện Nhơn Trạch)"
            dname = norm(re.sub(r"^(Quận|Huyện|Thị xã|Thành phố)\s+", "", o.get("dfull") or ""))
            by_district = [(w, q) for w, q in cands if dname and dname in q]
            if by_district:
                cands = by_district
        uniq = {w["code"]: (w, q) for w, q in cands}
        partial = any("phan" in q for _, q in uniq.values())
        if not uniq:
            status = "not_found"
        elif len(uniq) == 1:
            status = "partial" if partial else "exact"
        else:
            status = "split"
        stat[status] += 1
        new_code = next(iter(uniq)) if len(uniq) == 1 else ""
        rows.append({
            "old_ward_code": o["code"], "old_ward_name": o["full_name"],
            "old_district_name": o.get("dfull") or "", "old_province_code": o["province_code"],
            "old_province_name": o.get("pfull") or "", "new_province_code": new_prov,
            "new_ward_code": new_code, "status": status,
            "candidates": "|".join(f"{c}:{w['full']}" for c, (w, _) in uniq.items()),
        })

    cols = list(rows[0].keys())
    with OUT_TSV.open("w", encoding="utf-8-sig", newline="") as f:
        wr = csv.DictWriter(f, fieldnames=cols, delimiter="\t")
        wr.writeheader()
        wr.writerows(rows)

    lines = [
        "-- Ánh xạ xã CŨ (bảng wards) -> xã MỚI 2026 (vn_wards), sinh bởi tools/build_ward_mapping.py",
        f"-- Generated at: {datetime.now().isoformat(timespec='seconds')}",
        "-- " + ", ".join(f"{k}={v}" for k, v in sorted(stat.items())) + f", total={len(rows)}",
        "-- status: exact = chắc chắn | partial = 1 ứng viên nhưng ghi chú 'một phần/phần còn lại'",
        "--         split = xã cũ bị tách vào nhiều xã mới (new_ward_code NULL, xem candidates)",
        "--         not_found = không tìm thấy trong ghiChu (ghi chú bị cắt 254 byte / xã đã sáp nhập trước 2025)",
        "",
        "SET NAMES utf8mb4;",
        "CREATE TABLE IF NOT EXISTS vn_ward_mappings (",
        "  old_ward_code VARCHAR(20) NOT NULL,",
        "  old_province_code VARCHAR(20) NULL,",
        "  new_province_code VARCHAR(20) NULL,",
        "  new_ward_code VARCHAR(20) NULL,",
        "  status VARCHAR(20) NOT NULL,",
        "  candidates TEXT NULL COMMENT 'code:Tên|code:Tên khi split',",
        "  verified_by VARCHAR(100) NULL COMMENT 'Người rà soát thủ công (nếu có)',",
        "  PRIMARY KEY (old_ward_code),",
        "  KEY idx_vn_ward_mappings_new (new_ward_code),",
        "  KEY idx_vn_ward_mappings_status (status)",
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        "",
        "-- Chỉ ghi đè dòng CHƯA rà soát thủ công",
        "DELETE FROM vn_ward_mappings WHERE verified_by IS NULL;",
        "",
    ]
    values = [
        "  (" + ", ".join(sql_str(r[k]) for k in
                          ("old_ward_code", "old_province_code", "new_province_code",
                           "new_ward_code", "status", "candidates")) + ")"
        for r in rows
    ]
    for i in range(0, len(values), 1000):
        lines.append("INSERT IGNORE INTO vn_ward_mappings "
                     "(old_ward_code, old_province_code, new_province_code, new_ward_code, status, candidates) VALUES")
        lines.append(",\n".join(values[i:i + 1000]) + ";")
        lines.append("")
    OUT_SQL.write_text("\n".join(lines), encoding="utf-8")

    print(f"tinh_cu_da_anh_xa={len(prov_map)}/63")
    print(dict(stat), f"total={len(rows)}")
    print(f"created={OUT_SQL}\ncreated={OUT_TSV}")


if __name__ == "__main__":
    main()
