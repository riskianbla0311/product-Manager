<?php
// config/db.php : koneksi database memakai PDO
// Sesuaikan user/password bila MySQL kamu memakai password.

$dbHost = 'localhost';
$dbName = 'store_db';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL menyala dan database/store_db.sql sudah di-import.');
}
