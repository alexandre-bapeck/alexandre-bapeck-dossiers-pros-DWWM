<?php
/**
 * En-tête commun à toutes les pages (balises HTML, CSS, navbar, message flash).
 * Doit être inclus APRÈS database.php et fonctions.php (a besoin de currentUser, getFlash, etc.)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — LE GRAND GOURMET' : 'LE GRAND GOURMET' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animations.css">
  <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>

<!-- PAGE LOADER -->
<div id="page-loader">
  <div class="loader-logo">🍽️ LE GRAND GOURMET</div>
  <div class="loader-bar"><div class="loader-bar-inner"></div></div>
</div>

<!-- BARRE DE PROGRESSION SCROLL -->
<div id="progress-bar"></div>

<?php include __DIR__ . '/navbar.php'; ?>

<!-- BOUTON SCROLL TO TOP -->
<button id="scroll-top" title="Haut de page">↑</button>

<main class="main-content">
<?php
$flash = getFlash();
if ($flash):
?>
<div class="container">
  <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible">
    <?= e($flash['msg']) ?>
    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
  </div>
</div>
<?php endif; ?>
