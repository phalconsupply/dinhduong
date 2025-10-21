import csv
import mysql.connector
from datetime import datetime

# Kết nối database
conn = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="dinhduong",
    charset='utf8mb4',
    collation='utf8mb4_unicode_ci'
)
cursor = conn.cursor()

# Xóa dữ liệu cũ
print("Đang xóa dữ liệu cũ trong bảng weight_for_height...")
cursor.execute("DELETE FROM weight_for_height")
conn.commit()
print(f"Đã xóa {cursor.rowcount} bản ghi cũ")

# Reset AUTO_INCREMENT
cursor.execute("ALTER TABLE weight_for_height AUTO_INCREMENT = 1")
conn.commit()

# Hàm đọc và import CSV
def import_csv_to_db(file_path, gender):
    """
    Import CSV vào database
    gender: 1 = Nam (boy), 2 = Nữ (girl)
    """
    print(f"\nĐang import file: {file_path} (Gender={gender})...")
    
    with open(file_path, 'r', encoding='utf-8') as file:
        csv_reader = csv.reader(file)
        
        # Bỏ qua 2 dòng header
        next(csv_reader)
        next(csv_reader)
        
        # Đọc dòng tiêu đề cột
        header = next(csv_reader)
        
        count = 0
        for row in csv_reader:
            if len(row) < 8:  # Kiểm tra đủ cột
                continue
                
            cm = float(row[0])
            sd_minus_3 = float(row[1])
            sd_minus_2 = float(row[2])
            sd_minus_1 = float(row[3])
            median = float(row[4])
            sd_1 = float(row[5])
            sd_2 = float(row[6])
            sd_3 = float(row[7])
            
            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            
            # Insert vào database
            sql = """
            INSERT INTO weight_for_height 
            (gender, fromAge, toAge, cm, `-3SD`, `-2SD`, `-1SD`, `Median`, `1SD`, `2SD`, `3SD`, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            
            values = (gender, None, None, cm, sd_minus_3, sd_minus_2, sd_minus_1, median, sd_1, sd_2, sd_3, now, now)
            
            cursor.execute(sql, values)
            count += 1
        
        conn.commit()
        print(f"Đã import {count} bản ghi cho gender={gender}")

# Import file Boy (gender = 1)
import_csv_to_db('zscore/WFL-Zscore - boy.csv', 1)

# Import file Girl (gender = 2)
import_csv_to_db('zscore/WFL-Zscore - Girl.csv', 2)

# Kiểm tra tổng số bản ghi
cursor.execute("SELECT COUNT(*) FROM weight_for_height")
total = cursor.fetchone()[0]
print(f"\n✅ Hoàn thành! Tổng số bản ghi trong bảng: {total}")

# Hiển thị một số bản ghi mẫu
print("\n📊 Dữ liệu mẫu:")
cursor.execute("SELECT id, gender, cm, `-3SD`, `-2SD`, `Median`, `2SD`, `3SD` FROM weight_for_height LIMIT 5")
rows = cursor.fetchall()
print("ID | Gender | CM   | -3SD | -2SD | Median | 2SD | 3SD")
print("-" * 60)
for row in rows:
    print(f"{row[0]:2} | {row[1]:6} | {row[2]:4.1f} | {row[3]:4.1f} | {row[4]:4.1f} | {row[5]:6.1f} | {row[6]:4.1f} | {row[7]:4.1f}")

# Đóng kết nối
cursor.close()
conn.close()

print("\n🎉 Import hoàn tất!")
