<?php
// public/delete.php : DELETE lewat POST + token CSRF + PRG
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

// hanya boleh POST (akses lewat URL/GET ditolak)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!csrf_valid()) {
    http_response_code(403);
    exit('Token tidak valid');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('index.php?status=invalid');
}

// pastikan data ada
$stmt = $pdo->prepare('SELECT id FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
if (!$stmt->fetch()) {
    redirect('index.php?status=notfound');
}

$pdo->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
redirect('index.php?status=deleted');
