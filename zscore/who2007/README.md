# WHO Reference 2007 — dữ liệu LMS cho 5–19 tuổi

Nguồn gốc (official WHO): repo `WorldHealthOrganization/anthroplus` — chính là gói R sinh ra
phần mềm WHO AnthroPlus, cùng họ với `WorldHealthOrganization/anthro` mà dự án dùng cho 0–5 tuổi.

```
https://raw.githubusercontent.com/WorldHealthOrganization/anthroplus/main/data-raw/growthstandards/
```

| File | Chỉ số | Dải tuổi (tháng) | Dòng dữ liệu | Bytes |
|---|---|---|---|---|
| `hfawho2007.txt` | Height-for-age | 60–228 (+229 lặp) | 340 | 8.775 |
| `bfawho2007.txt` | BMI-for-age | 60–228 (+229 lặp) | 340 | 10.475 |
| `wfawho2007.txt` | Weight-for-age | 60–120 (+121 lặp) | 124 | 3.779 |

Định dạng: tab-separated, header `sex  age  l  m  s`

- `sex`: **1 = boys → `M`**, **2 = girls → `F`**
- `age`: tuổi tròn tháng
- `l, m, s`: tham số LMS của WHO

Dòng cuối mỗi giới (229 hoặc 121 tháng) là **bản lặp của dòng trước**, WHO thêm vào chỉ để
phép nội suy tuyến tính không bị thiếu biên trên. Không nhập dòng này vào DB.

## Tải lại dữ liệu

```bash
php zscore/who2007/download.php
```

## Lưu ý về who.int

`who.int` và `cdn.who.int` chặn mọi request tự động (HTTP 403 — bot protection), nên các file
`.xlsx` / `.pdf` (expanded tables, simplified field tables) phải **tải thủ công bằng trình duyệt**
từ https://www.who.int/tools/growth-reference-data-for-5to19-years/indicators

Các file .xlsx đó chỉ dùng để **đối chiếu nghiệm thu** — dữ liệu chạy thật lấy từ LMS ở đây,
vì chính LMS là nguồn sinh ra các bảng đó.
