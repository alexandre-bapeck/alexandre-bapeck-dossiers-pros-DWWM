<?php
/**
 * Admin — gestion des recettes (ajout, modification, suppression, upload d'image).
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id     = isset($_GET['id'])     ? (int) $_GET['id'] : 0;
$errors = [];

// Suppression d'une recette
if ($action === 'delete' && $id) {
    supprimerRecette($pdo, $id);
    setFlash('success', 'Recette supprimée.');
    header('Location: ' . url('admin/recettes'));
    exit;
}

// Bascule publié / brouillon
if ($action === 'toggle' && $id) {
    basculerPublication($pdo, $id);
    header('Location: ' . url('admin/recettes'));
    exit;
}

// Formulaire d'ajout ou de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $titre        = trim(isset($_POST['titre'])        ? $_POST['titre']        : '');
    $description  = trim(isset($_POST['description'])  ? $_POST['description']  : '');
    $instructions = trim(isset($_POST['instructions']) ? $_POST['instructions'] : '');
    $tPrep        = isset($_POST['temps_preparation']) ? (int) $_POST['temps_preparation'] : 0;
    $tCuis        = isset($_POST['temps_cuisson'])     ? (int) $_POST['temps_cuisson']     : 0;
    $nbPerso      = isset($_POST['nb_personnes'])      ? (int) $_POST['nb_personnes']      : 4;
    $difficulte   = isset($_POST['difficulte'])        ? $_POST['difficulte']              : 'Moyen';
    $catId        = isset($_POST['categorie_id'])      ? (int) $_POST['categorie_id']      : 0;
    $estPubliee   = isset($_POST['est_publiee']) ? 1 : 0;
    $image        = trim(isset($_POST['image']) ? $_POST['image'] : '');
    $editId       = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;

    /* ── Traitement de l'envoi d'une image depuis l'ordinateur ── */
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fichierTmp = $_FILES['image_file']['tmp_name'];
        $nomFichier = $_FILES['image_file']['name'];
        $tailleFich = $_FILES['image_file']['size'];

        // Récupération de l'extension
        $morceaux  = explode('.', $nomFichier);
        $extension = strtolower(end($morceaux));

        $extensionsOK = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $mimesOK      = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        // Vérification du vrai type MIME (plus sûr que se fier au navigateur)
        $mimeType = $_FILES['image_file']['type'];
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fichierTmp);
        }

        // Vérifications
        if (!in_array($extension, $extensionsOK)) {
            $errors[] = 'Extension non autorisée. Autorisé : ' . implode(', ', $extensionsOK);
        }
        if (!in_array($mimeType, $mimesOK)) {
            $errors[] = 'Type de fichier non autorisé.';
        }
        if ($tailleFich > 5 * 1024 * 1024) { // 5 Mo maximum
            $errors[] = 'Le fichier est trop volumineux (5 Mo maximum).';
        }

        if (empty($errors)) {
            // Création d'un nom de fichier unique
            $nouveauNom = slugify($titre) . '-' . uniqid('', false) . '.' . $extension;

            // Dossier de destination (créé s'il n'existe pas)
            $dossier = FRONTEND_PATH . '/assets/uploads/';
            if (!is_dir($dossier)) {
                mkdir($dossier, 0755, true);
            }

            // Déplacement du fichier
            $destination = $dossier . $nouveauNom;
            if (move_uploaded_file($fichierTmp, $destination)) {
                $image = $nouveauNom;
            } else {
                $errors[] = 'Erreur lors de l\'enregistrement de l\'image.';
            }
        }
    }

    /* ── Validations de base ── */
    if (strlen($titre) < 3)         $errors[] = 'Titre trop court.';
    if (strlen($instructions) < 10) $errors[] = 'Instructions trop courtes.';
    if (!$catId)                    $errors[] = 'Catégorie requise.';

    /* ── Enregistrement ── */
    if (empty($errors)) {
        // Génération du slug (avec un timestamp si déjà pris)
        $slug = slugify($titre);
        if (slugRecetteExiste($pdo, $slug, $editId)) {
            $slug .= '-' . time();
        }

        $data = [
            'titre'             => $titre,
            'slug'              => $slug,
            'description'       => $description,
            'instructions'      => $instructions,
            'temps_preparation' => $tPrep,
            'temps_cuisson'     => $tCuis,
            'nb_personnes'      => $nbPerso,
            'difficulte'        => $difficulte,
            'image'             => $image,
            'est_publiee'       => $estPubliee,
            'categorie_id'      => $catId,
            'auteur_id'         => currentUser()['id'],
        ];

        if ($editId) {
            modifierRecette($pdo, $editId, $data);
            $newId = $editId;
            setFlash('success', 'Recette mise à jour.');
        } else {
            $newId = creerRecette($pdo, $data);
            setFlash('success', 'Recette ajoutée.');
        }

        // Enregistrement des ingrédients (depuis les champs ing_nom[] et ing_qty[])
        $noms = isset($_POST['ing_nom']) ? $_POST['ing_nom'] : [];
        $qtes = isset($_POST['ing_qty']) ? $_POST['ing_qty'] : [];
        enregistrerIngredients($pdo, $newId, $noms, $qtes);

        header('Location: ' . url('admin/recettes'));
        exit;
    }
}

/* ── Données pour le formulaire d'édition ── */
$recette     = null;
$ingredients = [];
if ($action === 'edit' && $id) {
    $recette = getRecetteById($pdo, $id);
    if (!$recette) {
        header('Location: ' . url('admin/recettes'));
        exit;
    }
    $ingredients = getIngredientsRecette($pdo, $id);
}

$categories = getCategories($pdo);
$recettes   = getRecettesAdmin($pdo);

$pageTitle = 'Admin – Recettes';
include __DIR__ . '/../header.php';
?>
<div class="admin-layout">
<?php include __DIR__ . '/../admin-sidebar.php'; ?>
<div class="admin-content">

  <div class="admin-header">
    <h1>🍴 Gestion des recettes</h1>
    <?php if ($action === 'list'): ?>
    <a href="?action=add" class="btn btn-primary">+ Nouvelle recette</a>
    <?php endif; ?>
  </div>

  <?php if ($action === 'add' || $action === 'edit'): ?>
  <!-- FORMULAIRE -->
  <div class="admin-form-card">
    <h2><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> une recette</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $err): ?><p class="mb-0"><?= e($err) ?></p><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <input type="hidden" name="edit_id" value="<?= $action === 'edit' ? $id : 0 ?>">
      <div class="form-row">
        <div class="form-group flex-2">
          <label>Titre *</label>
          <input type="text" name="titre" class="form-control" required
                 value="<?= e(isset($recette['titre']) ? $recette['titre'] : '') ?>">
        </div>
        <div class="form-group">
          <label>Catégorie *</label>
          <select name="categorie_id" class="form-control" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (isset($recette['categorie_id']) ? $recette['categorie_id'] : 0) == $cat['id'] ? 'selected' : '' ?>>
              <?= e($cat['icone'] . ' ' . $cat['nom']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Description courte</label>
        <textarea name="description" class="form-control" rows="2"><?= e(isset($recette['description']) ? $recette['description'] : '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Instructions * (une étape par ligne)</label>
        <textarea name="instructions" class="form-control" rows="8" required><?= e(isset($recette['instructions']) ? $recette['instructions'] : '') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Préparation (min)</label>
          <input type="number" name="temps_preparation" class="form-control" min="0"
                 value="<?= (int)(isset($recette['temps_preparation']) ? $recette['temps_preparation'] : 0) ?>">
        </div>
        <div class="form-group">
          <label>Cuisson (min)</label>
          <input type="number" name="temps_cuisson" class="form-control" min="0"
                 value="<?= (int)(isset($recette['temps_cuisson']) ? $recette['temps_cuisson'] : 0) ?>">
        </div>
        <div class="form-group">
          <label>Nb personnes</label>
          <input type="number" name="nb_personnes" class="form-control" min="1"
                 value="<?= (int)(isset($recette['nb_personnes']) ? $recette['nb_personnes'] : 4) ?>">
        </div>
        <div class="form-group">
          <label>Difficulté</label>
          <select name="difficulte" class="form-control">
            <?php foreach (['Facile', 'Moyen', 'Difficile'] as $d): ?>
            <option <?= (isset($recette['difficulte']) ? $recette['difficulte'] : 'Moyen') === $d ? 'selected' : '' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row image-upload-section">
        <div class="form-group">
          <label>Image de la recette (Upload local)</label>

          <?php if (!empty($recette['image'])): ?>
            <div class="current-image-preview mb-2" style="display: flex; align-items: center; gap: 0.8rem; margin-bottom: 0.8rem;">
              <span class="preview-label" style="font-size: 0.8rem; font-weight: 600; color: var(--gray-dark);">Actuelle :</span>
              <div class="preview-thumb" style="width: 50px; height: 50px; border-radius: var(--radius-sm); background-size: cover; background-position: center; border: 1px solid var(--border); background-image: url('<?= recipeImage($recette['image']) ?>')"></div>
            </div>
          <?php endif; ?>

          <div class="upload-box" style="position: relative;">
            <input type="file" name="image_file" id="image_file" class="file-input-hidden" accept="image/*" style="display: none;">
            <label for="image_file" class="file-input-label" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; border: 2px dashed var(--border); border-radius: var(--radius); cursor: pointer; text-align: center; background: var(--off-white); transition: all 0.2s;">
              <span class="upload-icon" style="font-size: 1.4rem;">📁</span>
              <span class="upload-text" id="upload-label-text" style="font-size: 0.8rem; font-weight: 600; color: var(--black2);">Glissez ou cliquez pour uploader</span>
              <span class="upload-limits" style="font-size: 0.68rem; color: var(--gray); margin-top: 0.2rem;">JPG, PNG, WEBP, GIF (Max. 5 Mo)</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label>Ou URL d'image distante</label>
          <input type="text" name="image" id="image_url" class="form-control"
                 placeholder="https://images.unsplash.com/..."
                 value="<?= e(isset($recette['image']) ? $recette['image'] : '') ?>">
          <small class="text-muted" style="display: block; margin-top: 0.3rem; font-size: 0.75rem;">Le fichier uploadé localement sera prioritaire.</small>
        </div>
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="est_publiee" <?= (isset($recette['est_publiee']) ? $recette['est_publiee'] : 1) ? 'checked' : '' ?>> Publier la recette</label>
      </div>

      <!-- Ingrédients dynamiques -->
      <div class="form-group">
        <label>Ingrédients</label>
        <div id="ingredients-list">
          <?php if (!empty($ingredients)): ?>
          <?php foreach ($ingredients as $ing): ?>
          <div class="ing-row">
            <input type="text" name="ing_qty[]" class="form-control ing-qty" placeholder="Quantité" value="<?= e($ing['quantite']) ?>">
            <input type="text" name="ing_nom[]" class="form-control" placeholder="Ingrédient" value="<?= e($ing['nom']) ?>">
            <button type="button" class="btn btn-sm btn-danger btn-remove-ing">−</button>
          </div>
          <?php endforeach; ?>
          <?php else: ?>
          <div class="ing-row">
            <input type="text" name="ing_qty[]" class="form-control ing-qty" placeholder="Quantité">
            <input type="text" name="ing_nom[]" class="form-control" placeholder="Ingrédient">
            <button type="button" class="btn btn-sm btn-danger btn-remove-ing">−</button>
          </div>
          <?php endif; ?>
        </div>
        <button type="button" id="btn-add-ing" class="btn btn-outline btn-sm mt-2">+ Ajouter un ingrédient</button>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= url('admin/recettes') ?>" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <!-- LISTE -->
  <table class="admin-table">
    <thead><tr><th>Titre</th><th>Catégorie</th><th>Auteur</th><th>Difficulté</th><th>Vues</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($recettes as $rec): ?>
      <tr>
        <td><?= e($rec['titre']) ?></td>
        <td><?= e($rec['cat_nom']) ?></td>
        <td><?= e($rec['auteur']) ?></td>
        <td><span class="difficulty difficulty-<?= strtolower($rec['difficulte']) ?>"><?= e($rec['difficulte']) ?></span></td>
        <td><?= (int)$rec['vues'] ?></td>
        <td>
          <a href="<?= url('admin/recettes?action=toggle&id=' . $rec['id']) ?>" class="badge <?= $rec['est_publiee'] ? 'badge-success' : 'badge-warning' ?>">
            <?= $rec['est_publiee'] ? 'Publiée' : 'Brouillon' ?>
          </a>
        </td>
        <td class="actions">
          <a href="<?= url('admin/recettes?action=edit&id=' . $rec['id']) ?>" class="btn-icon">✏️</a>
          <a href="<?= url('recette?slug=' . e($rec['slug'])) ?>" class="btn-icon" target="_blank">👁️</a>
          <a href="<?= url('admin/recettes?action=delete&id=' . $rec['id']) ?>" class="btn-icon text-danger"
             onclick="return confirm('Supprimer « <?= e(addslashes($rec['titre'])) ?> » ?')">🗑️</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('image_file');
      const labelText = document.getElementById('upload-label-text');
      if (fileInput && labelText) {
          fileInput.addEventListener('change', function(e) {
              if (this.files && this.files.length > 0) {
                  const fileName = this.files[0].name;
                  labelText.textContent = '🟢 Sélectionné : ' + fileName;
                  labelText.style.color = 'var(--emerald)';
              } else {
                  labelText.textContent = 'Glissez ou cliquez pour uploader';
                  labelText.style.color = '';
              }
          });
      }
  });
  </script>
  <?php endif; ?>

</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>


