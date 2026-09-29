<?php
// public/create.php : CREATE + validasi server-side + pola PRG
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$pageTitle = 'Tambah Menu';
$errors = [];
$old = ['name' => '', 'category' => 'Minuman', 'price' => '', 'stock' => '0'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        http_response_code(403);
        exit('Token tidak valid');
    }

    [$data, $errors, $old] = validate_product($pdo);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, category, price, stock)
                 VALUES (:name, :category, :price, :stock)'
            );
            $stmt->execute($data);
            redirect('index.php?status=created'); // PRG
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') { // duplikat (UNIQUE) pada kondisi balapan
                $errors['name'] = 'Nama produk sudah dipakai.';
            } else {
                throw $ex;
            }
        }
    }
}

$categories = get_categories($pdo);
$action = 'create.php';
$submitLabel = 'Simpan menu';

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>Tambah menu</h1><p>Isi data minuman atau makanan. Semua input divalidasi ulang di server.</p></div>
<?php require __DIR__ . '/../includes/product_form.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
