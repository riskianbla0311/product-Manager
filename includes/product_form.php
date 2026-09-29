<?php
/*
 * includes/product_form.php : form yang dipakai create.php & edit.php
 * Variabel: $action, $submitLabel, $old, $errors, $categories, $productId (opsional)
 */
?>
<form class="card form" action="<?= e($action) ?>" method="POST">
  <?= csrf_field() ?>
  <?php if (!empty($productId)): ?>
    <input type="hidden" name="id" value="<?= (int) $productId ?>">
  <?php endif; ?>

  <div class="field">
    <label for="name">Nama menu</label>
    <input id="name" name="name" minlength="3" maxlength="100" required
           value="<?= e($old['name']) ?>"
           class="<?= isset($errors['name']) ? 'is-invalid' : '' ?>">
    <?php if (isset($errors['name'])): ?><p class="error"><?= e($errors['name']) ?></p><?php endif; ?>
  </div>

  <div class="field">
    <label for="category">Kategori</label>
    <select id="category" name="category" required
            class="<?= isset($errors['category']) ? 'is-invalid' : '' ?>">
      <option value="">-- Pilih kategori --</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= e($c) ?>" <?= $old['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['category'])): ?><p class="error"><?= e($errors['category']) ?></p><?php endif; ?>
  </div>

  <div class="row">
    <div class="field">
      <label for="price">Harga (Rp)</label>
      <input id="price" name="price" type="number" min="1" step="any" required
             value="<?= e($old['price']) ?>"
             class="<?= isset($errors['price']) ? 'is-invalid' : '' ?>">
      <?php if (isset($errors['price'])): ?><p class="error"><?= e($errors['price']) ?></p><?php endif; ?>
    </div>
    <div class="field">
      <label for="stock">Stok</label>
      <input id="stock" name="stock" type="number" min="0" step="1" required
             value="<?= e($old['stock']) ?>"
             class="<?= isset($errors['stock']) ? 'is-invalid' : '' ?>">
      <?php if (isset($errors['stock'])): ?><p class="error"><?= e($errors['stock']) ?></p><?php endif; ?>
    </div>
  </div>

  <div class="actions">
    <button type="submit" class="btn btn--primary"><?= e($submitLabel) ?></button>
    <a class="btn btn--ghost" href="index.php">Batal</a>
  </div>
</form>
