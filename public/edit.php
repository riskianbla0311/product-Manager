<?php
// public/edit.php : READ satu data by ID + UPDATE
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$pageTitle = 'Edit Menu';

// id boleh dari POST (form) atau GET (link Edit); wajib bilangan bulat
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
   ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('index.php?status=notfound');
}

// cek data ada (prepared statement)
$stmt = $pdo->prepare('SELECT id, name, category, price, stock FROM products WHERE id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();
if (!$product) {
    redirect('index.php?status=notfound');
}

$errors = [];
$old = [
    'name'     => $product['name'],
    'category' => $product['category'],
    'price'    => (string) (float) $product['price'],
    'stock'    => (string) $product['stock'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        http_response_code(403);
        exit('Token tidak valid');
    }

    [$data, $errors, $old] = validate_product($pdo, $id);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE products
                    SET name = :name, category = :category, price = :price, stock = :stock
                  WHERE id = :id'
            );
            $stmt->execute($data + ['id' => $id]);
            redirect('index.php?status=updated'); // PRG
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $errors['name'] = 'Nama produk sudah dipakai.';
            } else {
                throw $ex;
            }
        }
    }
}

$categories = get_categories($pdo);
$action = 'edit.php?id=' . $id;
$submitLabel = 'Simpan perubahan';
$productId = $id;

require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>Edit menu</h1><p>Ubah data menu #<?= (int) $id ?>.</p></div>
<?php require __DIR__ . '/../includes/product_form.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
