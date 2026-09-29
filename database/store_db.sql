-- database/store_db.sql  (Kedai Rasa : Minuman & Makanan)
-- Import lewat phpMyAdmin (tab Import) atau MySQL CLI:
--   mysql -u root < database/store_db.sql

CREATE DATABASE IF NOT EXISTS store_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE store_db;

-- Hapus tabel lama supaya import ulang selalu bersih
DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100)  NOT NULL UNIQUE,
  category   VARCHAR(50)   NOT NULL DEFAULT 'Minuman',
  price      DECIMAL(12,2) NOT NULL,
  stock      INT           NOT NULL DEFAULT 0,
  created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- Data contoh (boleh dihapus)
INSERT INTO products (name, category, price, stock) VALUES
  ('Kopi Susu Gula Aren',   'Minuman', 18000, 40),
  ('Es Teh Manis',          'Minuman',  6000, 80),
  ('Teh Tarik Hangat',      'Minuman', 12000, 35),
  ('Jus Alpukat',           'Minuman', 16000, 20),
  ('Matcha Latte',          'Minuman', 22000,  4),
  ('Lemon Tea Dingin',      'Minuman', 10000, 50),
  ('Nasi Goreng Spesial',   'Makanan', 25000, 30),
  ('Mie Goreng Telur',      'Makanan', 18000, 28),
  ('Roti Bakar Cokelat',    'Makanan', 15000, 22),
  ('Pisang Goreng Keju',    'Makanan', 14000,  3),
  ('Kentang Goreng',        'Makanan', 13000, 45),
  ('Ayam Geprek Sambal',    'Makanan', 23000, 18);
