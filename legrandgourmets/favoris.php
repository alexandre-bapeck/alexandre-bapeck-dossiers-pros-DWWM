<?php
/**
 * Page "Mes favoris" — liste les recettes favorites de l'utilisateur connecté.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
requireLogin();

$userId = currentUser()['id'];

// Si l'utilisateur clique sur "retirer" un favori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer'])) {
    retirerFavori($pdo, $userId, (int) $_POST['retirer']);
    setFlash('success', 'Recette retirée de vos favoris.');
    header('Location: ' . url('favoris'));
    exit;
}

$favoris = getFavorisUtilisateur($pdo, $userId);

$pageTitle = 'Mes favoris';
include __DIR__ . '/header.php';
?>

<div class="container">
  <div class="page-header">
    <h1>❤️ Mes favoris</h1>
    <p><?= count($favoris) ?> recette<?= count($favoris) > 1 ? 's' : '' ?> sauvegardée<?= count($favoris) > 1 ? 's' : '' ?></p>
  </div>

  <?php if (empty($favoris)): ?>
  <div class="empty-state">
    <p>😔 Vous n'avez pas encore de recettes en favoris.</p>
    <a href="<?= url('recettes') ?>" class="btn btn-primary">Découvrir les recettes</a>
  </div>
  <?php else: ?>
  <div class="recipes-grid">
    <?php foreach ($favoris as $r): ?>
    <article class="recipe-card">
      <a href="<?= url('recette?slug=' . e($r['slug'])) ?>" class="recipe-card-img-link">
        <div class="recipe-card-img" style="background-image: url('<?= recipeImage($r['image']) ?>')">
          <span class="recipe-badge"><?= e($r['cat_icone']) ?> <?= e($r['cat_nom']) ?></span>
          <form method="POST" action="<?= url('favoris') ?>" class="inline">
            <input type="hidden" name="retirer" value="<?= (int)$r['id'] ?>">
            <button type="submit" class="btn-favori active" title="Retirer des favoris"
                    onclick="return confirm('Retirer cette recette ?')">❤</button>
          </form>
        </div>
      </a>
      <div class="recipe-card-body">
        <h3><a href="<?= url('recette?slug=' . e($r['slug'])) ?>"><?= e($r['titre']) ?></a></h3>
        <p class="recipe-desc"><?= e($r['description']) ?></p>
        <div class="recipe-meta">
          <span>⏱ <?= (int)$r['duree_totale'] ?> min</span>
          <span class="difficulty difficulty-<?= strtolower($r['difficulte']) ?>"><?= e($r['difficulte']) ?></span>
          <span>⭐ <?= number_format($r['note_moy'], 1) ?></span>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
