<?php
// public/index.php : READ (card responsif) + bonus search, filter kategori, pagination
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$pageTitle = 'Daftar Menu';

// ---- input GET (search, filter, halaman) ----
$q        = trim((string) ($_GET['q'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$page     = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$page     = max(1, $page);
$perPage  = 8;

// ---- bangun WHERE dengan parameter (bukan digabung ke string) ----
// Catatan: karena EMULATE_PREPARES = false, nama placeholder tidak boleh dipakai dua kali.
$where  = [];
$params = [];
if ($q !== '') {
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $where[] = '(name LIKE :q1 OR category LIKE :q2)';
    $params['q1'] = $like;
    $params['q2'] = $like;
}
if ($category !== '') {
    $where[] = 'category = :category';
    $params['category'] = $category;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ---- hitung total untuk pagination ----
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereSql");
$stmt->execute($params);
$total      = (int) $stmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// ---- ambil data halaman ini ----
$stmt = $pdo->prepare(
    "SELECT id, name, category, price, stock
       FROM products $whereSql
   ORDER BY id DESC
      LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $val) {
    $stmt->bindValue(':' . $key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// ---- ringkasan (query statis) ----
$summary = $pdo->query(
    'SELECT COUNT(*) AS items, COALESCE(SUM(stock),0) AS units,
            COALESCE(SUM(price*stock),0) AS worth FROM products'
)->fetch();
$categories = get_categories($pdo);

// ---- pesan sukses dari PRG (whitelist) ----
$messages = [
    'created'  => ['ok',  'Menu berhasil disimpan.'],
    'updated'  => ['ok',  'Menu berhasil diperbarui.'],
    'deleted'  => ['ok',  'Menu berhasil dihapus.'],
    'notfound' => ['bad', 'Menu tidak ditemukan.'],
    'invalid'  => ['bad', 'Permintaan tidak valid.'],
];
$flash = $messages[$_GET['status'] ?? ''] ?? null;

require __DIR__ . '/../includes/header.php';
?>

<?php if ($flash): ?>
  <div class="alert alert--<?= $flash[0] ?>"><?= e($flash[1]) ?></div>
<?php endif; ?>

<section class="stats">
  <div class="stat"><span class="stat__num"><?= (int) $summary['items'] ?></span><span class="stat__label">Jenis menu</span></div>
  <div class="stat"><span class="stat__num"><?= number_format((int) $summary['units'], 0, ',', '.') ?></span><span class="stat__label">Total stok</span></div>
  <div class="stat"><span class="stat__num"><?= e(rupiah($summary['worth'])) ?></span><span class="stat__label">Nilai persediaan</span></div>
</section>

<form class="toolbar" method="GET" action="index.php">
  <input type="search" name="q" placeholder="Cari nama menu..." value="<?= e($q) ?>">
  <select name="category">
    <option value="">Semua kategori</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= e($c) ?>" <?= $c === $category ? 'selected' : '' ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn--primary">Cari</button>
  <?php if ($q !== '' || $category !== ''): ?>
    <a class="btn btn--ghost" href="index.php">Reset</a>
  <?php endif; ?>
</form>

<?php if (!$products): ?>
  <div class="card empty">
    <h2>Belum ada menu</h2>
    <p>Tidak ada data yang cocok. Coba kata kunci lain atau tambahkan menu baru.</p>
  </div>
<?php else: ?>
  <section class="products">
    <?php foreach ($products as $p): ?>
      <article class="card product">
        <span class="badge badge--<?= e(strtolower($p["category"])) ?>"><?= e($p["category"]) ?></span>
        <h3><?= e($p['name']) ?></h3>
        <p class="price"><?= e(rupiah($p['price'])) ?></p>
        <p class="stock <?= (int) $p['stock'] < 5 ? 'stock--low' : '' ?>">
          Stok: <strong><?= (int) $p['stock'] ?></strong>
          <?= (int) $p['stock'] < 5 ? ' &middot; menipis' : '' ?>
        </p>
        <div class="actions">
          <a class="btn btn--ghost" href="edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
          <form method="POST" action="delete.php" onsubmit="return confirm('Yakin hapus menu ini?');">
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <?= csrf_field() ?>
            <button class="btn btn--danger">Hapus</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Halaman">
      <?php if ($page > 1): ?>
        <a href="<?= e(index_url(['q' => $q, 'category' => $category, 'page' => $page - 1])) ?>">&laquo; Sebelumnya</a>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $page ? 'is-current' : '' ?>"
           href="<?= e(index_url(['q' => $q, 'category' => $category, 'page' => $i])) ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="<?= e(index_url(['q' => $q, 'category' => $category, 'page' => $page + 1])) ?>">Berikutnya &raquo;</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
