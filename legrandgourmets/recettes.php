<?php
/**
 * Liste des recettes — avec filtres (catégorie, difficulté), recherche et pagination.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

// Récupération des paramètres GET
$q       = trim(isset($_GET['q'])    ? $_GET['q']    : '');
$catSlug = trim(isset($_GET['cat'])  ? $_GET['cat']  : '');
$diff    = trim(isset($_GET['diff']) ? $_GET['diff'] : '');
$tri     = isset($_GET['tri'])  ? $_GET['tri']  : 'recent';
$page    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$perPage = 9;
$offset  = ($page - 1) * $perPage;

// On accepte seulement les 3 valeurs valides pour la difficulté
if (!in_array($diff, ['Facile', 'Moyen', 'Difficile'])) {
    $diff = '';
}

// Construction du tableau de filtres (sans les valeurs vides)
$filtres = array_filter([
    'q'    => $q,
    'cat'  => $catSlug,
    'diff' => $diff,
    'tri'  => $tri,
]);

// Récupération des données
$total      = countRecettes($pdo, $filtres);
$totalPages = (int) ceil($total / $perPage);
if ($totalPages < 1) $totalPages = 1;

$recettes   = getRecettes($pdo, $filtres, $perPage, $offset);
$categories = getCategories($pdo);

$pageTitle = 'Recettes';
include __DIR__ . '/header.php';
?>

<div class="container">
  <div class="page-header">
    <h1>Toutes les recettes
      <?php if ($q): ?><small>— recherche : "<?= e($q) ?>"</small><?php endif; ?>
    </h1>
    <p><?= $total ?> recette<?= $total > 1 ? 's' : '' ?> trouvée<?= $total > 1 ? 's' : '' ?></p>
  </div>

  <!-- FILTRES -->
  <form class="filters-bar" method="GET" action="<?= url('recettes') ?>">
    <?php if ($q): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
    <div class="filter-group">
      <label>Catégorie</label>
      <select name="cat" onchange="this.form.submit()">
        <option value="">Toutes</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat['slug']) ?>" <?= $catSlug === $cat['slug'] ? 'selected' : '' ?>>
          <?= e($cat['icone']) ?> <?= e($cat['nom']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="filter-group">
      <label>Difficulté</label>
      <select name="diff" onchange="this.form.submit()">
        <option value="">Toutes</option>
        <option value="Facile"    <?= $diff === 'Facile'    ? 'selected' : '' ?>>Facile</option>
        <option value="Moyen"     <?= $diff === 'Moyen'     ? 'selected' : '' ?>>Moyen</option>
        <option value="Difficile" <?= $diff === 'Difficile' ? 'selected' : '' ?>>Difficile</option>
      </select>
    </div>
    <div class="filter-group">
      <label>Trier par</label>
      <select name="tri" onchange="this.form.submit()">
        <option value="recent" <?= $tri === 'recent' ? 'selected' : '' ?>>Plus récentes</option>
        <option value="note"   <?= $tri === 'note'   ? 'selected' : '' ?>>Mieux notées</option>
        <option value="rapide" <?= $tri === 'rapide' ? 'selected' : '' ?>>Plus rapides</option>
      </select>
    </div>
    <?php if ($q || $catSlug || $diff || $tri !== 'recent'): ?>
    <a href="<?= url('recettes') ?>" class="btn btn-outline btn-sm">✕ Réinitialiser</a>
    <?php endif; ?>
  </form>

  <!-- GRILLE -->
  <?php if (empty($recettes)): ?>
  <div class="empty-state">
    <p>😕 Aucune recette ne correspond à votre recherche.</p>
    <a href="<?= url('recettes') ?>" class="btn btn-primary">Voir toutes les recettes</a>
  </div>
  <?php else: ?>
  <div class="recipes-grid">
    <?php foreach ($recettes as $r): ?>
    <article class="recipe-card">
      <a href="<?= url('recette?slug=' . e($r['slug'])) ?>" class="recipe-card-img-link">
        <div class="recipe-card-img" style="background-image: url('<?= recipeImage($r['image']) ?>')">
          <span class="recipe-badge"><?= e($r['cat_icone']) ?> <?= e($r['cat_nom']) ?></span>
          <?php if (isLoggedIn()): ?>
          <button class="btn-favori" data-id="<?= (int)$r['id'] ?>" title="Favoris">❤</button>
          <?php endif; ?>
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

  <!-- PAGINATION -->
  <?php if ($totalPages > 1): ?>
  <?php
  // Construction de la query string pour conserver les filtres dans les liens de pagination
  $qs = http_build_query(array_filter(['q' => $q, 'cat' => $catSlug, 'diff' => $diff, 'tri' => $tri]));
  $qs = $qs ? '&' . $qs : '';
  ?>
  <nav class="pagination">
    <?php if ($page > 1): ?>
    <a href="<?= url('recettes?page=' . ($page - 1) . $qs) ?>" class="page-btn">← Précédent</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="<?= url('recettes?page=' . $i . $qs) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
    <a href="<?= url('recettes?page=' . ($page + 1) . $qs) ?>" class="page-btn">Suivant →</a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
