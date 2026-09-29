<?php /* includes/header.php : butuh variabel $pageTitle */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Produk') ?> | <?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="container topbar__inner">
    <a class="brand" href="index.php">
      <span class="brand__mark"><?= e(APP_INITIAL) ?></span>
      <span class="brand__text"><?= e(APP_NAME) ?><small><?= e(APP_TAGLINE) ?></small></span>
    </a>
    <a class="btn btn--accent" href="create.php">+ Tambah Menu</a>
  </div>
</header>
<main class="container">
