<?php
/**
 * Top des recettes — classement des recettes les mieux notées.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

$recettes = getTopRecettes($pdo, 10);

$pageTitle = 'Top Recettes';
include __DIR__ . '/header.php';
?>

<div class="container">
  <div class="page-header">
    <h1>🏆 Top Recettes</h1>
    <p>Les meilleures recettes notées par la communauté</p>
  </div>

  <?php if (empty($recettes)): ?>
  <div class="empty-state">
    <p>😕 Pas encore de recettes notées. Soyez le premier !</p>
    <a href="<?= url('recettes') ?>" class="btn btn-primary">Voir les recettes</a>
  </div>
  <?php else: ?>
  <div class="top-recettes-list">
    <?php foreach ($recettes as $i => $r): ?>
    <div class="top-card anim anim-up anim-delay-<?= min($i + 1, 6) ?>">
      <div class="top-rank"><?= $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#' . ($i + 1))) ?></div>
      <div class="top-img" style="background-image: url('<?= recipeImage($r['image']) ?>')"></div>
      <div class="top-info">
        <span class="top-cat"><?= e($r['cat_icone']) ?> <?= e($r['cat_nom']) ?></span>
        <h3><a href="<?= url('recette?slug=' . e($r['slug'])) ?>"><?= e($r['titre']) ?></a></h3>
        <p><?= e($r['description']) ?></p>
        <div class="top-meta">
          <span class="top-stars">
            <?php for ($s = 1; $s <= 5; $s++): ?>
            <span style="color: <?= $s <= round($r['note_moy']) ? '#E8B84B' : '#ddd' ?>">★</span>
            <?php endfor; ?>
            <strong><?= number_format($r['note_moy'], 1) ?></strong>
            <small>(<?= (int)$r['nb_notes'] ?> avis)</small>
          </span>
          <span>⏱ <?= (int)$r['duree_totale'] ?> min</span>
          <span class="difficulty difficulty-<?= strtolower($r['difficulte']) ?>"><?= e($r['difficulte']) ?></span>
        </div>
      </div>
      <a href="<?= url('recette?slug=' . e($r['slug'])) ?>" class="btn btn-primary btn-sm">Voir →</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<style>
.top-recettes-list { display: flex; flex-direction: column; gap: 1rem; padding: 1rem 0 3rem; }
.top-card {
  display: flex; align-items: center; gap: 1.2rem;
  background: #fff; border: 1px solid var(--border);
  border-radius: var(--radius); padding: 1rem 1.3rem;
  box-shadow: var(--shadow-card); transition: all .25s ease;
}
.top-card:hover { transform: translateX(6px); box-shadow: var(--shadow-md); border-color: var(--gold); }
.top-rank { font-size: 1.6rem; min-width: 44px; text-align: center; font-weight: 700; color: var(--gold-dark); }
.top-img { width: 80px; height: 80px; border-radius: var(--radius-sm); background-size: cover; background-position: center; background-color: var(--gray-light); flex-shrink: 0; }
.top-info { flex: 1; }
.top-cat { font-size: .75rem; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
.top-info h3 { font-size: 1rem; margin: .2rem 0 .3rem; }
.top-info h3 a { color: var(--black); }
.top-info h3 a:hover { color: var(--gold-dark); }
.top-info p { font-size: .82rem; color: var(--gray); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.top-meta { display: flex; gap: 1rem; margin-top: .4rem; font-size: .8rem; color: var(--gray); align-items: center; flex-wrap: wrap; }
.top-stars { display: flex; align-items: center; gap: .2rem; font-size: .9rem; }
.top-stars strong { color: var(--black); font-size: .85rem; }
@media (max-width: 600px) {
  .top-img { width: 60px; height: 60px; }
  .top-rank { min-width: 32px; font-size: 1.2rem; }
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
