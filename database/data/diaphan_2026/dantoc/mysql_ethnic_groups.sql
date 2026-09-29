-- MySQL data for Vietnamese ethnic groups
-- Generated from dantoc/dantoc.docx
-- Generated at: 2026-06-21T14:15:05
-- Records: 54

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS vn_ethnic_groups (
  id TINYINT UNSIGNED NOT NULL COMMENT 'Order number from source list',
  code CHAR(2) NOT NULL COMMENT 'Two-digit display code, e.g. 01, 02',
  name VARCHAR(100) NOT NULL COMMENT 'Ethnic group name',
  other_names TEXT NULL COMMENT 'Other names from source document',
  subgroups TEXT NULL COMMENT 'Small groups / local groups from source document',
  residence_area TEXT NULL COMMENT 'Residence areas from source document',
  is_majority TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 for Kinh/Viet, 0 for others',
  sort_order TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vn_ethnic_groups_code (code),
  KEY idx_vn_ethnic_groups_name (name),
  KEY idx_vn_ethnic_groups_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM vn_ethnic_groups;

INSERT INTO vn_ethnic_groups (id, code, name, other_names, subgroups, residence_area, is_majority, sort_order) VALUES
  (1, '01', 'Kinh (Việt)', 'Việt', NULL, 'Trong cả nước', 1, 1),
  (2, '02', 'Tày', 'Thổ', 'Ngạn, Phán, Thu lao, Pa dí', 'Hà giang, Tuyên Quang, Lào cai, Yên Bái, Cao Bằng, Lai Châu, Bắc Thái, Hà Bắc.', 0, 2),
  (3, '03', 'Thái', 'Táy', 'Táy Khao (Thái Trắng), Táy Đăm (Thái Đen), Táy Chiềng hay Táy Mương (Hàng Tổng), Táy Thanh (Man Thanh), Táy Mười, Pu Thay, Thổ Đà Bắc, Táy Mộc Châu (Táy Đeng)', 'Sơn La, Lai Châu, Nghệ An, Thanh Hoá, Lao Cai, Yên Bái, Hoà Bình, Lâm Đồng...', 0, 3),
  (4, '04', 'Mường', 'Mol, Mual, Mọi', 'Mọi Bi, Ao Tá (ÂuTá)', 'Hoà Bình, Thanh Hoá, Vĩnh Phú, Yên Bái, Sơn La, Ninh Bình', 0, 4),
  (5, '05', 'Hoa (Hán)', 'Khách, Tàu, Hán', 'Triền Châu, Phúc Kiến, Quảng Đông, Quảng Tây, Hải Nam, Xạ Phang, Thoòng Nhằn, Hẹ', 'Kiên Giang, Hải Phòng, Vĩnh Long, Trà Vinh, Quảng Ninh,Đồng Nai, Hậu Giang, Minh Hải, tp Hồ Chí Minh', 0, 5),
  (6, '06', 'Khơ-me', NULL, 'Miên, Cur, Cul, Thổ, Việt gốc Khơ-me, Khơ-me Krôm', 'Hậu Giang, Vĩnh Long, Trà Vinh, Kiên Giang, Minh Hải, Tây Ninh, tp Hồ Chí Minh, Sông Bé, An Giang', 0, 6),
  (7, '07', 'Nùng', NULL, 'Nùng Xuồng, Nùng Giang, Nùng An, Nùng Phàn Sình, Nùng Lòi, Nùng Tùng Slìn, Nùng Cháo, Nùng Quý Rỵn, Nùng Khèn Lài, Nùng Dýn, Nùng Inh...', 'Cao Bằng, Lạng Sơn, Bắc Thái, Hà Giang, Tuyên Quang, Hà Bắc, Quảng Ninh, tp Hồ Chí Minh, Lâm Đồng, Đắc Lắc, Lào Cai', 0, 7),
  (8, '08', 'HMông (Mèo)', 'Mèo, Mẹo, Mán, Miêu Tộc', 'Mèo Hoa, Mèo Xanh, Mèo Đỏ, Mèo Đen, Ná Miẻo, Mèo Trắng', 'Hà Giang, Yên Bái, Lào Cai, Lai Châu, Sơn La, Cao Bằng, Lạng Sơn, Nghệ An, Thanh Hóa, Hoà Bình, Bắc Thái', 0, 8),
  (9, '09', 'Dao', 'Mán, Động, Trại, Dìu, Miền, Kiềm, Kìm Mùn', 'Dao Đại Bản, Dao Đỏ, Dao Cóc Ngáng, Dao Cóc Mùn, Dao Lô Gang, Dao Quần Chẹt, Dao Tam Đảo, Dao Tiền, Dao Quần Trắng, Dao Làn Tiẻn, Dao áo Dài', 'Hà Giang, Tuyên Quang, Lào Cai, Yên Bái, Cao Bằng, Lạng Sơn, Bắc Thái, Lai Châu, Sơn La, Vĩnh Phú, Hà Bắc, Thanh Hoá, Quảng Ninh, Hoà Bình, Hà Tây', 0, 9),
  (10, '10', 'Gia-rai', 'Mọi, Chơ-rai', 'Chỏ, Hđrung, Aráp, Mdhur, Tbuăn', 'Gia Lai, Kon Tum, Đắc Lắc', 0, 10),
  (11, '11', 'Ê-đê', 'Đe, Mọi', 'Ra-đê, Rha-đê, Êđê-Êga, Anăk Êđê, Kpă, Ađham, Krung, Ktul Dliê, Ruê, Blô, Êpan, Mđhur, Bih, Kđrao, Dong Kay, Dong Măk, Êning, Arul, Hning, Kmun,Ktlê', 'Đắc Lắc, Phú Yên, Khánh Hoà', 0, 11),
  (12, '12', 'Ba-na', 'Bơ-nâm, Roh, Kon Kde, Ala Công, Kpang Công', 'Tơ-lô, Gơ-lar, Rơ-ngao, Krem, Giơ-lơng(Y-lơng)', 'Kon Tum, Bình Định, Phú Yên', 0, 12),
  (13, '13', 'Sán Chay (Cao lan - Sán chỉ)', 'Mán, Cao Lan-Sán Chỉ, Hờn Bạn, Hờn Chùng, Sơn Tử', 'Cao Lan, Sán Chỉ', 'Bắc Thái, Tuyên Quang, Quảng Ninh, Hà Bắc, Lạng Sơn, Vĩnh Phú, Yên Bái', 0, 13),
  (14, '14', 'Chăm (chàm)', 'Chiêm Thành, Chăm Pa, Hời, Chàm', 'Chăm Hroi, Chàm Châu Đốc, Chà Và Ku, Chăm Pôông', 'Ninh Thuận, Bình Thuận, An Giang, tp Hồ Chí Minh, Bình Định, Phú Yên, Châu Đốc, Khánh Hoà', 0, 14),
  (15, '15', 'Xơ-đăng', 'Kmrâng, Hđang, Con-lan, Brila', 'Xơ-teng,Tơ-đrá, Mơ-nâm, Hà-lăng, Ca-dong, Châu, Ta Trẽ(Tà Trĩ)', 'Kon Tum, Quảng Nam-Đà Nẵng, Quảng Ngãi', 0, 15),
  (16, '16', 'Sán Dìu', 'Trại, Trại Đát, Sán Dợo, Mán quần Cộc, Mán Váy Xẻ', NULL, 'Quảng Ninh, Hà Bắc, Hải Hưng, Bắc Thái, Vỹnh Phú, Tuyên Quang', 0, 16),
  (17, '17', 'Hrê', 'Mọi Đá Vách, Chăm-rê, Mọi Luỹ, Thạch Bých, Mọi Sơn Phòng', NULL, 'Quảng Ngãi, Bình Định', 0, 17),
  (18, '18', 'Cơ-ho', NULL, 'Xrê, Nốp (Tu Nốp), Cơ-don, Chil, Lát (Lách), Tơ-ring', 'Lâm Đồng, Ninh Thuận, Bình Thuận, Khánh Hoà', 0, 18),
  (19, '19', 'Ra-glai', 'O-rang, Glai, Rô-glai, Radlai, Mọi', 'Ra-clay (Rai), Noong (La-oang)', 'Ninh Thuận, Bình Thuận, Khánh Hoà, Lâm Đồng', 0, 19),
  (20, '20', 'Mnông', NULL, 'Gar, Chil, Rlâm, Preh, Kuênh, Nông, Bu-Đâng, Prâng, Đip, Biêt, Si Tô, Bu Đêh', 'Đắc Lăc, Lâm Đồng', 0, 20),
  (21, '21', 'Thổ', NULL, 'Kủo, Mọn, Cuối, Họ, Đan Lai-Ly Hà, Tày Poọng (Con Kha, Xá La Vàng)', 'Nghệ An, Thanh Hoá', 0, 21),
  (22, '22', 'Xtiêng', 'Xa-điêng, Mọi, Tà-mun', NULL, 'Sông Bé, Tây Ninh, Lâm Đồng, Đắc Lắc', 0, 22),
  (23, '23', 'Khơmú', 'Xá Cốu, Pu Thênh, Tày Hạy, Việt Cang, Khá Klậu, Tềnh', 'Quảng Lâm', 'Sơn La, Lai Châu, Nghệ An, Yên Bái', 0, 23),
  (24, '24', 'Bru-Vân Kiều', NULL, 'Vân Kiều, Măng Coong, Trì, Khùa, Bru', 'Quảng Bình, Quảng Trị', 0, 24),
  (25, '25', 'Giáy', 'Nhắng, Giẳng, Sa Nhân, Pầu Thỉn, Chủng Chá, Pu Nắm', 'Pu Nà (Cùi Chu hoặc Quý Châu)', 'Lào Cai, Hà Giang, Lai Châu', 0, 25),
  (26, '26', 'Cơ-tu', 'Ca-tu, Ca-tang, Mọi, Cao, Hạ', 'Phương, Kan-tua', 'Quảng Nam-Đà Nẵng, Thừa Thiên-Huế', 0, 26),
  (27, '27', 'Gié-Triêng', 'Giang Rẫy, Brila, Cà-tang, Mọi, Doãn', 'Gié (Dgieh, Tareh), Triêng (Treng, Tơ-riêng), Ve (La-ve), Pa-noong (Bơ Noong)', 'Quảng Nam-Đà Nẵng, Kon Tum', 0, 27),
  (28, '28', 'Ta-ôi', 'Tôi-ôi, Ta-hoi, Ta-ôih, Tà-uất (Atuất)', 'Pa-cô, Ba-hi, Can-tua', 'Quảng Trị, Thừa Thiên-Huế', 0, 28),
  (29, '29', 'Mạ', NULL, 'Châu Mạ, Chô Mạ, Mọi', 'Lâm Đồng, Đồng Nai', 0, 29),
  (30, '30', 'Co', 'Trầu, Cùa, Mọi, Col, Cor, Khùa', NULL, 'Quảng Ngãi, Quảng Nam-Đà Nẵng', 0, 30),
  (31, '31', 'Chơ-ro', 'Châu-ro, Dơ-ro, Mọi', NULL, 'Đồng Nai', 0, 31),
  (32, '32', 'Hà Nhì', 'U Ní, Xá U Ní, Hà Nhì Già', 'Hà Nhì Cồ Chồ, Hà Nhì La Mí, Hà Nhì Đen', 'Lai Châu, Lào Cai', 0, 32),
  (33, '33', 'Xinh-mun', 'Puộc, Pụa, Xá', 'Dạ, Nghẹt', 'Sơn La, Lai Châu', 0, 33),
  (34, '34', 'Chu-ru', 'Chơ-ru, Kru, Mọi', NULL, 'Lâm Đồng, Ninh Thuận, Bình Thuận', 0, 34),
  (35, '35', 'Lào', 'Lào Bốc, Lào Nọi', NULL, 'Lai Châu, Sơn La', 0, 35),
  (36, '36', 'La-chí', 'Thổ Đen, Cù Tê, Xá, La ti, Mán Chí', NULL, 'Hà Giang', 0, 36),
  (37, '37', 'Phù Lá', NULL, 'Bồ Khô Pạ (Xá Phó), Mun Di Pạ, Phù Lá Đen, Phù Lá Hoa, Phù Lá Trắng, Phù Lá Hán, Chù Lá Phù Lá', 'Lao Cai, Lai Châu, Sơn La, Hà Giang', 0, 37),
  (38, '38', 'La Hủ', NULL, 'Khù Sung (Cò Sung), Khạ Quy (Xá Quỷ), Xá Toong Lương (Xá Lá Vàng), Xá Pươi', 'Lai Châu', 0, 38),
  (39, '39', 'Kháng', 'Xá Khao, Xá Đón, Xá Tú Lăng', 'Kháng Xúa, Kháng Đón, Kháng Dống, Kháng Hốc, Kháng ái, Kháng Bung, Kháng Quảng Lâm', 'Lai Châu, Sơn La', 0, 39),
  (40, '40', 'Lự', 'Lừ, Duôn, Nhuồn', NULL, 'Lai Châu', 0, 40),
  (41, '41', 'Pà Thẻn', 'Pà Hưng, Mán Pa Teng, Tống', 'Tống, Mèo Lài', 'Hà Giang, Tuyên Quang', 0, 41),
  (42, '42', 'LôLô', 'Mùn Di, Ô Man, Lu Lọc Màn, Di, Qua La, La La, Ma Di', 'Lô Lô Đen, Lô Lô Hoa', 'Hà Giang, Cao Bằng, Lao Cai', 0, 42),
  (43, '43', 'Chứt', 'Xá La Vàng, Chà Củi (Tắc Củi), Tu Vang, Pa Leng', 'Sách, Mày, Rục, Mã Liềng, Arem, Xơ-lang, Umo', 'Quảng Bình', 0, 43),
  (44, '44', 'Mảng', 'Mảng Ư, Xá Lá Vàng, Niễng O, Xa Mãng, Xá Cang Lai', 'Mảng Hệ, Mảng Gứng', 'Lai Châu', 0, 44),
  (45, '45', 'Cờ lao', NULL, 'Cờ Lao Trắng, Cờ Lao Xanh, Cờ Lao Đỏ', 'Hà Giang', 0, 45),
  (46, '46', 'Bố Y', 'Chủng Chá, Trung Gia, Pầu Y, Pủ Dí', 'Bố Y, Tu Dí', 'Hà Giang, Lào Cai', 0, 46),
  (47, '47', 'La Ha', 'Xá Khao, Xá Cha, Xá La Nga', 'Khlá Phlạo, La Ha ủng', 'Yên Bái, Sơn La', 0, 47),
  (48, '48', 'Cống', NULL, 'Xám Khống, Xá Xeng, Xa, Xá Côống', 'Lai Châu', 0, 48),
  (49, '49', 'Ngái', 'Sán Ngái', 'Xín, Lê, Đản, Khánh Gia, Hắc Cá (Xéc)', 'Quảng Ninh, tp Hồ Chí Minh, Hải Phòng', 0, 49),
  (50, '50', 'Si La', 'Cú Đề Xừ', NULL, 'Lai Châu', 0, 50),
  (51, '51', 'Pu Péo', 'Ka Bẻo, Pen Ti Lô Lô, La Quả, Mán', NULL, 'Hà Giang', 0, 51),
  (52, '52', 'Brâu', 'Brao', NULL, 'Kon Tum', 0, 52),
  (53, '53', 'Rơ-măm', NULL, NULL, 'Kon Tum', 0, 53),
  (54, '54', 'Ơ-đu', 'Tày Hạt', NULL, 'Nghệ An', 0, 54);

-- Example: ethnic group combobox
-- SELECT id, name FROM vn_ethnic_groups ORDER BY sort_order;

-- Example: search by name / other names
-- SELECT id, name, other_names FROM vn_ethnic_groups
-- WHERE name LIKE CONCAT('%', ?, '%') OR other_names LIKE CONCAT('%', ?, '%')
-- ORDER BY sort_order;
