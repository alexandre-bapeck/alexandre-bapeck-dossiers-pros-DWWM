<?php
/**
 * Détail d'une recette + traitement des formulaires (note, favori, commentaire).
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

// Récupération du slug dans l'URL
$slug = trim(isset($_GET['slug']) ? $_GET['slug'] : '');
if (!$slug) {
    header('Location: ' . url('recettes'));
    exit;
}

// Chargement de la recette
$r = getRecetteBySlug($pdo, $slug);
if (!$r) {
    header('Location: ' . url('recettes'));
    exit;
}

// On incrémente le compteur de vues
incrementerVues($pdo, $r['id']);

// Si l'utilisateur est connecté, on récupère sa note et son favori
$maNote    = null;
$estFavori = false;
if (isLoggedIn()) {
    $userId    = currentUser()['id'];
    $maNote    = getNoteUtilisateur($pdo, $userId, $r['id']);
    $estFavori = estDejaFavori($pdo, $userId, $r['id']);
}

// Traitement des formulaires POST (note, favori, commentaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrfToken();
    requireLogin();

    $userId = currentUser()['id'];
    $action = $_POST['action'];

    if ($action === 'noter') {
        $val = (int) $_POST['note'];
        if ($val >= 1 && $val <= 5) {
            enregistrerNote($pdo, $userId, $r['id'], $val);
        }
    }

    if ($action === 'favori') {
        basculerFavori($pdo, $userId, $r['id']);
    }

    if ($action === 'commenter') {
        $contenu = trim(isset($_POST['contenu']) ? $_POST['contenu'] : '');
        if (strlen($contenu) >= 3) {
            creerCommentaire($pdo, $userId, $r['id'], $contenu);
        }
    }

    if ($action === 'supprimer') {
        // Seuls l'auteur de la recette ou un admin peuvent la supprimer
        if ($userId == $r['auteur_id'] || isAdmin()) {
            supprimerRecette($pdo, $r['id']);
            setFlash('success', 'Recette supprimée.');
            header('Location: ' . url('recettes'));
            exit;
        }
    }

    header('Location: ' . url('recette?slug=' . urlencode($slug)));
    exit;
}

// On peut supprimer la recette si on en est l'auteur ou si on est admin
$peutSupprimer = isLoggedIn() && (currentUser()['id'] == $r['auteur_id'] || isAdmin());

// Chargement des données pour l'affichage
$ingredients  = getIngredientsRecette($pdo, $r['id']);
$noteData     = getMoyenneNote($pdo, $r['id']);
$commentaires = getCommentairesRecette($pdo, $r['id']);

$pageTitle = $r['titre'];
include __DIR__ . '/header.php';
?>

<div class="container recipe-detail">
  <!-- BREADCRUMB -->
  <nav class="breadcrumb">
    <a href="<?= url('') ?>">Accueil</a> /
    <a href="<?= url('recettes?cat=' . e($r['cat_slug'])) ?>"><?= e($r['cat_icone']) ?> <?= e($r['cat_nom']) ?></a> /
    <span><?= e($r['titre']) ?></span>
  </nav>

  <div class="recipe-detail-grid">
    <!-- COLONNE PRINCIPALE -->
    <div class="recipe-main">
      <div class="recipe-hero-img" style="background-image: url('<?= recipeImage($r['image']) ?>')">
        <?php if (isLoggedIn()): ?>
        <form method="POST" action="<?= url('recette?slug=' . e($r['slug'])) ?>" class="inline">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="favori">
          <button type="submit" class="btn-favori-lg <?= $estFavori ? 'active' : '' ?>">
            <?= $estFavori ? '❤️ Sauvegardé' : '🤍 Sauvegarder' ?>
          </button>
        </form>
        <?php endif; ?>
      </div>

      <h1 class="recipe-detail-title"><?= e($r['titre']) ?></h1>
      <p class="recipe-detail-desc"><?= e($r['description']) ?></p>

      <!-- BOUTON SUPPRIMER (uniquement pour l'auteur ou un admin) -->
      <?php if ($peutSupprimer): ?>
      <form method="POST" action="<?= url('recette?slug=' . e($r['slug'])) ?>" class="inline"
            onsubmit="return confirm('Supprimer définitivement cette recette ?');"
            style="margin-bottom: 1rem;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="supprimer">
        <button type="submit" class="btn btn-danger btn-sm">🗑️ Supprimer ma recette</button>
      </form>
      <?php endif; ?>

      <!-- INFOS RAPIDES -->
      <div class="recipe-quick-info">
        <div class="quick-info-item">
          <span class="qi-icon">🔪</span>
          <span class="qi-label">Préparation</span>
          <span class="qi-value"><?= (int)$r['temps_preparation'] ?> min</span>
        </div>
        <div class="quick-info-item">
          <span class="qi-icon">🔥</span>
          <span class="qi-label">Cuisson</span>
          <span class="qi-value"><?= (int)$r['temps_cuisson'] ?> min</span>
        </div>
        <div class="quick-info-item">
          <span class="qi-icon">👥</span>
          <span class="qi-label">Personnes</span>
          <span class="qi-value"><?= (int)$r['nb_personnes'] ?></span>
        </div>
        <div class="quick-info-item">
          <span class="qi-icon">📊</span>
          <span class="qi-label">Difficulté</span>
          <span class="qi-value difficulty-<?= strtolower($r['difficulte']) ?>"><?= e($r['difficulte']) ?></span>
        </div>
        <div class="quick-info-item">
          <span class="qi-icon">⭐</span>
          <span class="qi-label">Note</span>
          <span class="qi-value"><?= $noteData['nb'] > 0 ? number_format($noteData['moy'], 1) . ' / 5' : 'N/A' ?></span>
        </div>
      </div>

      <!-- INSTRUCTIONS -->
      <div class="recipe-section">
        <h2>📋 Instructions</h2>
        <div class="recipe-instructions">
          <?php
          $etapes = array_filter(explode("\n", $r['instructions']));
          foreach ($etapes as $i => $etape):
          ?>
          <div class="instruction-step">
            <span class="step-number"><?= $i + 1 ?></span>
            <p><?= e(trim($etape)) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- NOTER -->
      <?php if (isLoggedIn()): ?>
      <div class="recipe-section">
        <h2>⭐ Votre note</h2>
        <form method="POST" action="<?= url('recette?slug=' . e($r['slug'])) ?>" class="rating-form">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="noter">
          <div class="stars-input">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>"
                   <?= (int)$maNote === $i ? 'checked' : '' ?>>
            <label for="star<?= $i ?>" title="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>">★</label>
            <?php endfor; ?>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">Enregistrer ma note</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- COMMENTAIRES -->
      <div class="recipe-section">
        <h2>💬 Commentaires (<?= count($commentaires) ?>)</h2>
        <?php if (isLoggedIn()): ?>
        <form method="POST" action="<?= url('recette?slug=' . e($r['slug'])) ?>" class="comment-form">
          <?= csrfField() ?>
          <input type="hidden" name="action" value="commenter">
          <textarea name="contenu" class="form-control" rows="3"
                    placeholder="Partagez votre avis sur cette recette…" required></textarea>
          <button type="submit" class="btn btn-primary mt-2">Publier</button>
        </form>
        <?php else: ?>
        <p><a href="<?= url('connexion') ?>">Connectez-vous</a> pour laisser un commentaire.</p>
        <?php endif; ?>

        <div class="comments-list mt-3">
          <?php foreach ($commentaires as $c): ?>
          <div class="comment">
            <div class="comment-header">
              <strong><?= e($c['pseudo']) ?></strong>
              <time><?= date('d/m/Y à H:i', strtotime($c['date_creation'])) ?></time>
            </div>
            <p><?= e($c['contenu']) ?></p>
          </div>
          <?php endforeach; ?>
          <?php if (empty($commentaires)): ?>
          <p class="text-muted">Soyez le premier à commenter cette recette !</p>
          <?php endif; ?>
        </div>
      </div>
    </div><!-- /.recipe-main -->

    <!-- SIDEBAR INGRÉDIENTS -->
    <aside class="recipe-sidebar">
      <div class="ingredients-card">
        <h2>🛒 Ingrédients</h2>
        <p class="for-persons">Pour <?= (int)$r['nb_personnes'] ?> personnes</p>
        <ul class="ingredients-list">
          <?php foreach ($ingredients as $ing): ?>
          <li>
            <span class="ing-qty"><?= e($ing['quantite']) ?></span>
            <span class="ing-name"><?= e($ing['nom']) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <button class="btn btn-outline btn-block" onclick="window.print()">🖨️ Imprimer</button>
      </div>
      <div class="author-card">
        <p>Recette par <strong><?= e($r['auteur']) ?></strong></p>
        <p class="text-muted">Publiée le <?= date('d/m/Y', strtotime($r['date_creation'])) ?></p>
      </div>
    </aside>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
