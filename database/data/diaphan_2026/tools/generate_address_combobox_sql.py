from __future__ import annotations

import struct
from datetime import datetime
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DATA_DIR = ROOT / "GDB_SHP"
OUTPUT = ROOT / "mysql_address_combobox_2026.sql"


def read_dbf(path: Path) -> list[dict[str, str]]:
    with path.open("rb") as f:
        header = f.read(32)
        record_count = struct.unpack("<I", header[4:8])[0]
        header_len = struct.unpack("<H", header[8:10])[0]
        record_len = struct.unpack("<H", header[10:12])[0]

        fields: list[tuple[str, str, int, int]] = []
        while True:
            field = f.read(32)
            if not field or field[0] == 0x0D:
                break
            name = field[:11].split(b"\0", 1)[0].decode("ascii", "replace")
            fields.append((name, chr(field[11]), field[16], field[17]))

        rows: list[dict[str, str]] = []
        for index in range(record_count):
            f.seek(header_len + index * record_len)
            record = f.read(record_len)
            if not record or record[0:1] == b"*":
                continue

            row: dict[str, str] = {}
            pos = 1
            for name, _field_type, length, _decimals in fields:
                raw = record[pos : pos + length]
                pos += length
                row[name] = raw.decode("utf-8", "replace").strip()
            rows.append(row)

    return rows


def short_name(full_name: str) -> str:
    for prefix in ("Thành phố ", "Tỉnh ", "Phường ", "Xã ", "Đặc khu ", "Thị trấn "):
        if full_name.startswith(prefix):
            return full_name[len(prefix) :]
    return full_name


def unit_type(full_name: str) -> str:
    for prefix in ("Thành phố", "Tỉnh", "Phường", "Xã", "Đặc khu", "Thị trấn"):
        if full_name.startswith(prefix + " "):
            return prefix
    return ""


def sql_str(value: str | None) -> str:
    if value is None or value == "":
        return "NULL"
    return "'" + str(value).replace("\\", "\\\\").replace("'", "''") + "'"


def sql_num(value: str | None, decimal: bool = False) -> str:
    if value is None or str(value).strip() == "":
        return "NULL"
    try:
        cleaned = str(value).replace(",", ".")
        return str(float(cleaned)) if decimal else str(int(float(cleaned)))
    except ValueError:
        return "NULL"


def build_dataset() -> tuple[dict[str, dict[str, str]], dict[str, dict[str, str]]]:
    province_rows = read_dbf(DATA_DIR / "DiaPhan_Tinh_2026.dbf")
    ward_rows = read_dbf(DATA_DIR / "DiaPhan_Xa_2026.dbf")

    provinces: dict[str, dict[str, str]] = {}
    for row in province_rows:
        code = row.get("maTinh_BNV") or row.get("maTinh")
        if not code:
            continue
        full_name = row.get("tenTinh", "")
        provinces[code] = {
            "code": code,
            "legacy_code": row.get("maTinh") or "",
            "name": short_name(full_name),
            "full_name": full_name,
            "unit_type": unit_type(full_name),
            "population": row.get("danSo") or "",
            "area_km2": row.get("dienTich") or "",
            "center": row.get("trungtamhc") or "",
        }

    for row in ward_rows:
        code = row.get("maTinh_BNV") or row.get("maTinh")
        if code and code not in provinces:
            full_name = row.get("tenTinh", "")
            provinces[code] = {
                "code": code,
                "legacy_code": row.get("maTinh") or "",
                "name": short_name(full_name),
                "full_name": full_name,
                "unit_type": unit_type(full_name),
                "population": "",
                "area_km2": "",
                "center": "",
            }

    wards: dict[str, dict[str, str]] = {}
    for row in ward_rows:
        code = row.get("maXa_BNV") or row.get("maXa")
        province_code = row.get("maTinh_BNV") or row.get("maTinh")
        if not code or not province_code:
            continue
        full_name = row.get("tenXa", "")
        wards[code] = {
            "code": code,
            "legacy_code": row.get("maXa") or "",
            "province_code": province_code,
            "name": short_name(full_name),
            "full_name": full_name,
            "unit_type": unit_type(full_name),
            "population": row.get("danSo") or "",
            "area_km2": row.get("dienTich") or "",
            "resolution": row.get("nghiQuyet") or "",
            "note": row.get("ghiChu") or "",
        }

    return provinces, wards


def main() -> None:
    provinces, wards = build_dataset()

    lines: list[str] = [
        "-- MySQL data for Vietnam address combobox: province -> ward",
        "-- Generated from local DiaPhan_Tinh_2026 / DiaPhan_Xa_2026 shapefile attributes",
        "-- Intended use: user selects province and ward; house number and street name are entered by user",
        f"-- Generated at: {datetime.now().isoformat(timespec='seconds')}",
        f"-- Provinces: {len(provinces)}",
        f"-- Wards: {len(wards)}",
        "",
        "SET NAMES utf8mb4;",
        "SET FOREIGN_KEY_CHECKS = 0;",
        "",
        "CREATE TABLE IF NOT EXISTS vn_provinces (",
        "  code VARCHAR(20) NOT NULL COMMENT 'Official province code, BNV/GSO',",
        "  legacy_code VARCHAR(20) NULL COMMENT 'Code from source shapefile maTinh',",
        "  name VARCHAR(255) NOT NULL COMMENT 'Short display name, without unit prefix',",
        "  full_name VARCHAR(255) NOT NULL COMMENT 'Full display name, e.g. Tinh/Thanh pho',",
        "  unit_type VARCHAR(50) NULL COMMENT 'Tinh or Thanh pho',",
        "  population INT NULL,",
        "  area_km2 DECIMAL(12,2) NULL,",
        "  administrative_center VARCHAR(255) NULL,",
        "  sort_order INT NOT NULL DEFAULT 0,",
        "  PRIMARY KEY (code),",
        "  KEY idx_vn_provinces_name (name),",
        "  KEY idx_vn_provinces_full_name (full_name)",
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        "",
        "CREATE TABLE IF NOT EXISTS vn_wards (",
        "  code VARCHAR(20) NOT NULL COMMENT 'Official ward/commune code, BNV/GSO',",
        "  legacy_code VARCHAR(20) NULL COMMENT 'Code from source shapefile maXa',",
        "  province_code VARCHAR(20) NOT NULL,",
        "  name VARCHAR(255) NOT NULL COMMENT 'Short display name, without unit prefix',",
        "  full_name VARCHAR(255) NOT NULL COMMENT 'Full display name, e.g. Xa/Phuong',",
        "  unit_type VARCHAR(50) NULL COMMENT 'Xa, Phuong, Dac khu',",
        "  population INT NULL,",
        "  area_km2 DECIMAL(12,2) NULL,",
        "  resolution VARCHAR(255) NULL,",
        "  note TEXT NULL COMMENT 'Old units or merge note from source',",
        "  sort_order INT NOT NULL DEFAULT 0,",
        "  PRIMARY KEY (code),",
        "  KEY idx_vn_wards_province (province_code),",
        "  KEY idx_vn_wards_name (name),",
        "  KEY idx_vn_wards_full_name (full_name),",
        "  CONSTRAINT fk_vn_wards_province FOREIGN KEY (province_code) REFERENCES vn_provinces(code)",
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        "",
        "-- Optional table for addresses entered by users. Keep or remove depending on your application.",
        "CREATE TABLE IF NOT EXISTS user_addresses (",
        "  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,",
        "  province_code VARCHAR(20) NOT NULL,",
        "  ward_code VARCHAR(20) NOT NULL,",
        "  street_name VARCHAR(255) NULL COMMENT 'Entered by user',",
        "  house_number VARCHAR(100) NULL COMMENT 'Entered by user',",
        "  address_detail VARCHAR(500) NULL COMMENT 'Optional extra address text',",
        "  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,",
        "  PRIMARY KEY (id),",
        "  KEY idx_user_addresses_province (province_code),",
        "  KEY idx_user_addresses_ward (ward_code),",
        "  CONSTRAINT fk_user_addresses_province FOREIGN KEY (province_code) REFERENCES vn_provinces(code),",
        "  CONSTRAINT fk_user_addresses_ward FOREIGN KEY (ward_code) REFERENCES vn_wards(code)",
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        "",
        "DELETE FROM vn_wards;",
        "DELETE FROM vn_provinces;",
        "",
        "INSERT INTO vn_provinces (code, legacy_code, name, full_name, unit_type, population, area_km2, administrative_center, sort_order) VALUES",
    ]

    province_values = []
    for index, province in enumerate(
        sorted(provinces.values(), key=lambda item: (item["full_name"], item["code"])), 1
    ):
        province_values.append(
            "  ("
            + ", ".join(
                [
                    sql_str(province["code"]),
                    sql_str(province["legacy_code"]),
                    sql_str(province["name"]),
                    sql_str(province["full_name"]),
                    sql_str(province["unit_type"]),
                    sql_num(province["population"]),
                    sql_num(province["area_km2"], decimal=True),
                    sql_str(province["center"]),
                    str(index),
                ]
            )
            + ")"
        )
    lines.append(",\n".join(province_values) + ";")
    lines.append("")
    lines.append(
        "INSERT INTO vn_wards (code, legacy_code, province_code, name, full_name, unit_type, population, area_km2, resolution, note, sort_order) VALUES"
    )

    ward_values = []
    for index, ward in enumerate(
        sorted(
            wards.values(),
            key=lambda item: (
                provinces.get(item["province_code"], {}).get("full_name", ""),
                item["full_name"],
                item["code"],
            ),
        ),
        1,
    ):
        ward_values.append(
            "  ("
            + ", ".join(
                [
                    sql_str(ward["code"]),
                    sql_str(ward["legacy_code"]),
                    sql_str(ward["province_code"]),
                    sql_str(ward["name"]),
                    sql_str(ward["full_name"]),
                    sql_str(ward["unit_type"]),
                    sql_num(ward["population"]),
                    sql_num(ward["area_km2"], decimal=True),
                    sql_str(ward["resolution"]),
                    sql_str(ward["note"]),
                    str(index),
                ]
            )
            + ")"
        )
    lines.append(",\n".join(ward_values) + ";")
    lines.extend(
        [
            "",
            "SET FOREIGN_KEY_CHECKS = 1;",
            "",
            "-- Example: province combobox",
            "-- SELECT code, full_name FROM vn_provinces ORDER BY sort_order, full_name;",
            "",
            "-- Example: ward combobox after user selects a province",
            "-- SELECT code, full_name FROM vn_wards WHERE province_code = ? ORDER BY sort_order, full_name;",
            "",
            "-- Example: display full user-entered address",
            "-- SELECT CONCAT_WS(', ', NULLIF(a.house_number, ''), NULLIF(a.street_name, ''), w.full_name, p.full_name) AS full_address",
            "-- FROM user_addresses a",
            "-- JOIN vn_wards w ON w.code = a.ward_code",
            "-- JOIN vn_provinces p ON p.code = a.province_code;",
            "",
        ]
    )

    OUTPUT.write_text("\n".join(lines), encoding="utf-8")
    print(f"created={OUTPUT}")
    print(f"provinces={len(provinces)} wards={len(wards)} bytes={OUTPUT.stat().st_size}")


if __name__ == "__main__":
    main()
