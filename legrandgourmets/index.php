<?php
/**
 * Page d'accueil — recette du jour, catégories, dernières recettes.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

$recetteJour = getRecetteDuJour($pdo);
$categories  = getCategories($pdo);
$recettes    = getRecettesFeatured($pdo, 6);

$pageTitle = 'Accueil';
include __DIR__ . '/header.php';
?>

<!-- HERO -->
<?php if ($recetteJour): ?>
<section class="hero" style="background-image: url('<?= recipeImage($recetteJour['image']) ?>')">
  <div class="hero-overlay">
    <div class="hero-content">
      <span class="hero-badge">🌟 Recette du jour</span>
      <h1><?= e($recetteJour['titre']) ?></h1>
      <p><?= e($recetteJour['description']) ?></p>
      <div class="hero-meta">
        <span>⏱ <?= (int)$recetteJour['temps_preparation'] + (int)$recetteJour['temps_cuisson'] ?> min</span>
        <span>👤 <?= e($recetteJour['auteur']) ?></span>
        <span>⭐ <?= number_format($recetteJour['note_moy'], 1) ?></span>
      </div>
      <a href="<?= url('recette?slug=' . e($recetteJour['slug'])) ?>" class="btn btn-hero">Voir la recette</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- RECHERCHE -->
<section class="search-section">
  <div class="container">
    <form action="<?= url('recettes') ?>" method="GET" class="search-form anim anim-up">
      <input type="text" name="q" class="search-input" placeholder="Rechercher une recette, un ingrédient…"
             value="<?= e(isset($_GET['q']) ? $_GET['q'] : '') ?>">
      <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
    </form>
  </div>
</section>

<!-- CATÉGORIES -->
<section class="section">
  <div class="container">
    <h2 class="section-title anim anim-up">Parcourir par catégorie</h2>
    <div class="categories-grid">
      <?php foreach ($categories as $cat): ?>
      <a href="<?= url('recettes?cat=' . e($cat['slug'])) ?>" class="category-card"
         style="--cat-color: <?= e($cat['couleur']) ?>">
        <span class="category-icon"><?= e($cat['icone']) ?></span>
        <span class="category-name"><?= e($cat['nom']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- DERNIÈRES RECETTES -->
<section class="section section-light">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title anim anim-left">Dernières recettes</h2>
      <a href="<?= url('recettes') ?>" class="btn btn-outline">Voir tout</a>
    </div>
    <div class="recipes-grid">
      <?php foreach ($recettes as $r): ?>
      <article class="recipe-card">
        <a href="<?= url('recette?slug=' . e($r['slug'])) ?>" class="recipe-card-img-link">
          <div class="recipe-card-img" style="background-image: url('<?= recipeImage($r['image']) ?>')">
            <span class="recipe-badge"><?= e($r['cat_icone']) ?> <?= e($r['cat_nom']) ?></span>
            <?php if (isLoggedIn()): ?>
            <button class="btn-favori" data-id="<?= (int)$r['id'] ?>" title="Ajouter aux favoris">❤</button>
            <?php endif; ?>
          </div>
        </a>
        <div class="recipe-card-body">
          <h3><a href="<?= url('recette?slug=' . e($r['slug'])) ?>"><?= e($r['titre']) ?></a></h3>
          <p class="recipe-desc"><?= e($r['description']) ?></p>
          <div class="recipe-meta">
            <span>⏱ <?= (int)$r['temps_preparation'] + (int)$r['temps_cuisson'] ?> min</span>
            <span class="difficulty difficulty-<?= strtolower($r['difficulte']) ?>"><?= e($r['difficulte']) ?></span>
            <span>⭐ <?= number_format($r['note_moy'], 1) ?> (<?= (int)$r['nb_notes'] ?>)</span>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<?php if (!isLoggedIn()): ?>
<section class="cta-section">
  <div class="container cta-content">
    <h2>Rejoignez la communauté LE GRAND GOURMET !</h2>
    <p>Sauvegardez vos recettes préférées, laissez des avis et partagez vos coups de cœur.</p>
    <a href="<?= url('inscription') ?>" class="btn btn-primary btn-lg">Créer un compte gratuit</a>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
