-- ============================================================
-- IMPORT BẢNG HISTORY VỚI UTF-8 ENCODING ĐÚNG
-- Tạo ngày: 2025-10-27
-- Fix lỗi: Tiếng Việt bị lỗi font khi import
-- ============================================================

-- QUAN TRỌNG: Thiết lập encoding UTF-8
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Tắt kiểm tra để import nhanh hơn
SET FOREIGN_KEY_CHECKS=0;
SET AUTOCOMMIT=0;
SET UNIQUE_CHECKS=0;
SET sql_mode = '';

-- ==== BƯỚC QUAN TRỌNG ====
-- 1. Mở file history_data_utf8.sql
-- 2. Copy TẤT CẢ các câu lệnh REPLACE INTO từ file đó
-- 3. Paste vào ngay phía dưới dòng này
-- ==========================

