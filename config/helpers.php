<?php
// config/helpers.php : fungsi bantu (escape, CSRF, validasi)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- identitas brand (ubah di sini, otomatis berlaku di semua halaman) ----
const APP_NAME    = 'Kedai Rasa';
const APP_TAGLINE = 'Minuman & Makanan Pilihan';
const APP_INITIAL = 'KR';

// ---- kategori yang diizinkan ----
const CATEGORIES = ['Minuman', 'Makanan'];

/** Escape output HTML (cegah XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Format rupiah, contoh: Rp 65.000 */
function rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

/** Token CSRF disimpan di session. */
function csrf_token(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Bandingkan token form dengan token session (aman terhadap timing attack). */
function csrf_valid(): bool
{
    return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit; // hentikan eksekusi setelah redirect
}

/** Bangun URL index.php dengan query string (untuk pagination/filter). */
function index_url(array $params): string
{
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}

/**
 * Normalisasi + validasi input produk dari $_POST.
 * Aturan: nama >= 3 karakter & unik, harga > 0, stok >= 0.
 *
 * @return array [$data, $errors, $old]
 *   $data   nilai bersih (dipakai untuk query)
 *   $errors pesan error per field
 *   $old    nilai mentah untuk mengisi ulang form
 */
function validate_product(PDO $pdo, int $ignoreId = 0): array
{
    // 1) ambil input dengan nilai default + 2) normalisasi
    $name     = trim((string) ($_POST['name'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // 3) validasi aturan bisnis
    $errors = [];

    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama minimal 3 karakter.';
    } elseif (mb_strlen($name) > 100) {
        $errors['name'] = 'Nama maksimal 100 karakter.';
    }

    if (!in_array($category, CATEGORIES, true)) {
        $errors['category'] = 'Pilih kategori Minuman atau Makanan.';
    }

    if ($price === false || $price === null || $price <= 0) {
        $errors['price'] = 'Harga harus berupa angka > 0.';
    } elseif ($price > 9999999999.99) {
        $errors['price'] = 'Harga terlalu besar.';
    }

    if ($stock === false || $stock === null || $stock < 0) {
        $errors['stock'] = 'Stok harus bilangan bulat dan tidak boleh negatif.';
    } elseif ($stock > 1000000) {
        $errors['stock'] = 'Stok terlalu besar.';
    }

    // nama harus unik (abaikan baris yang sedang diedit)
    if (!isset($errors['name'])) {
        $stmt = $pdo->prepare(
            'SELECT id FROM products WHERE name = :name AND id <> :id LIMIT 1'
        );
        $stmt->execute(['name' => $name, 'id' => $ignoreId]);
        if ($stmt->fetch()) {
            $errors['name'] = 'Nama produk sudah dipakai.';
        }
    }

    $data = [
        'name'     => $name,
        'category' => $category,
        'price'    => $price,
        'stock'    => $stock,
    ];
    $old = [
        'name'     => (string) ($_POST['name'] ?? ''),
        'category' => (string) ($_POST['category'] ?? ''),
        'price'    => (string) ($_POST['price'] ?? ''),
        'stock'    => (string) ($_POST['stock'] ?? ''),
    ];

    return [$data, $errors, $old];
}

/** Daftar kategori (hanya Minuman dan Makanan). */
function get_categories(?PDO $pdo = null): array
{
    return CATEGORIES;
}
