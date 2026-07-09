<?php
/**
 * Page permettant à un utilisateur connecté de proposer une recette.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
requireLogin();

$errors = [];

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
    $image        = trim(isset($_POST['image']) ? $_POST['image'] : '');

    // Upload d'image local
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fichierTmp = $_FILES['image_file']['tmp_name'];
        $nomFichier = $_FILES['image_file']['name'];
        $tailleFich = $_FILES['image_file']['size'];
        $morceaux  = explode('.', $nomFichier);
        $extension = strtolower(end($morceaux));
        $extensionsOK = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        $mimeType = $_FILES['image_file']['type'];
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fichierTmp);
        }

        if (!in_array($extension, $extensionsOK)) {
            $errors[] = 'Extension non autorisée.';
        } elseif ($tailleFich > 5 * 1024 * 1024) {
            $errors[] = 'Le fichier est trop volumineux (5 Mo max).';
        } else {
            $nouveauNom = slugify($titre) . '-' . uniqid('', false) . '.' . $extension;
            $dossier = __DIR__ . '/assets/uploads/';
            if (!is_dir($dossier)) { mkdir($dossier, 0755, true); }
            $destination = $dossier . $nouveauNom;
            if (move_uploaded_file($fichierTmp, $destination)) {
                $image = $nouveauNom;
            } else {
                $errors[] = 'Erreur lors de l\'enregistrement de l\'image.';
            }
        }
    }

    if (strlen($titre) < 3)         $errors[] = 'Titre trop court.';
    if (strlen($instructions) < 10) $errors[] = 'Instructions trop courtes.';
    if (!$catId)                    $errors[] = 'Catégorie requise.';

    if (empty($errors)) {
        $slug = slugify($titre);
        if (slugRecetteExiste($pdo, $slug)) {
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
            'est_publiee'       => 1, // Auto-publiée
            'categorie_id'      => $catId,
            'auteur_id'         => currentUser()['id'],
        ];

        $newId = creerRecette($pdo, $data);
        
        $noms = isset($_POST['ing_nom']) ? $_POST['ing_nom'] : [];
        $qtes = isset($_POST['ing_qty']) ? $_POST['ing_qty'] : [];
        enregistrerIngredients($pdo, $newId, $noms, $qtes);

        setFlash('success', 'Votre recette a été publiée avec succès !');
        header('Location: ' . url('recette?slug=' . $slug));
        exit;
    }
}

$categories = getCategories($pdo);
$pageTitle = 'Ajouter une recette';
include __DIR__ . '/header.php';
?>

<div class="container" style="max-width: 800px; padding-top: 3rem; padding-bottom: 5rem;">
  <div class="page-header" style="text-align: center; border-bottom: none;">
    <h1>Ajouter une recette</h1>
    <p>Partagez votre chef-d'œuvre culinaire avec la communauté</p>
  </div>

  <div class="admin-form-card" style="max-width: 100%; margin: 0 auto;">
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $err): ?><p class="mb-0"><?= e($err) ?></p><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div class="form-row">
        <div class="form-group flex-2">
          <label>Titre de la recette *</label>
          <input type="text" name="titre" class="form-control" required value="<?= e(isset($_POST['titre']) ? $_POST['titre'] : '') ?>">
        </div>
        <div class="form-group">
          <label>Catégorie *</label>
          <select name="categorie_id" class="form-control" required>
            <option value="">Sélectionner</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
              <?= e($cat['icone'] . ' ' . $cat['nom']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Description courte</label>
        <textarea name="description" class="form-control" rows="2" placeholder="Une petite phrase pour donner envie..."><?= e(isset($_POST['description']) ? $_POST['description'] : '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Instructions * (une étape par ligne)</label>
        <textarea name="instructions" class="form-control" rows="6" required placeholder="1. Préchauffez le four à 180°C..."><?= e(isset($_POST['instructions']) ? $_POST['instructions'] : '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Préparation (min)</label>
          <input type="number" name="temps_preparation" class="form-control" min="0" value="<?= (int)(isset($_POST['temps_preparation']) ? $_POST['temps_preparation'] : 0) ?>">
        </div>
        <div class="form-group">
          <label>Cuisson (min)</label>
          <input type="number" name="temps_cuisson" class="form-control" min="0" value="<?= (int)(isset($_POST['temps_cuisson']) ? $_POST['temps_cuisson'] : 0) ?>">
        </div>
        <div class="form-group">
          <label>Nb personnes</label>
          <input type="number" name="nb_personnes" class="form-control" min="1" value="<?= (int)(isset($_POST['nb_personnes']) ? $_POST['nb_personnes'] : 4) ?>">
        </div>
        <div class="form-group">
          <label>Difficulté</label>
          <select name="difficulte" class="form-control">
            <?php foreach (['Facile', 'Moyen', 'Difficile'] as $d): ?>
            <option <?= (isset($_POST['difficulte']) && $_POST['difficulte'] === $d) ? 'selected' : '' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row image-upload-section">
        <div class="form-group flex-2">
          <label>Photo de la recette</label>
          <div class="upload-box" style="position: relative;">
            <input type="file" name="image_file" id="image_file" class="file-input-hidden" accept="image/*" style="display: none;">
            <label for="image_file" class="file-input-label" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; border: 2px dashed var(--border); border-radius: var(--radius); cursor: pointer; text-align: center; background: var(--off-white); transition: all 0.2s;">
              <span class="upload-icon" style="font-size: 1.8rem;">📸</span>
              <span class="upload-text" id="upload-label-text" style="font-size: 0.9rem; font-weight: 600; color: var(--black2); margin-top: 0.5rem;">Glissez ou cliquez pour uploader une photo</span>
              <span class="upload-limits" style="font-size: 0.75rem; color: var(--gray); margin-top: 0.2rem;">JPG, PNG, WEBP, GIF (Max. 5 Mo)</span>
            </label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Ingrédients</label>
        <div id="ingredients-list">
          <div class="ing-row">
            <input type="text" name="ing_qty[]" class="form-control ing-qty" placeholder="Quantité (ex: 200g)">
            <input type="text" name="ing_nom[]" class="form-control" placeholder="Ingrédient (ex: Farine)">
            <button type="button" class="btn btn-sm btn-danger btn-remove-ing">−</button>
          </div>
          <div class="ing-row">
            <input type="text" name="ing_qty[]" class="form-control ing-qty" placeholder="Quantité">
            <input type="text" name="ing_nom[]" class="form-control" placeholder="Ingrédient">
            <button type="button" class="btn btn-sm btn-danger btn-remove-ing">−</button>
          </div>
        </div>
        <button type="button" id="btn-add-ing" class="btn btn-outline btn-sm mt-2">+ Ajouter un ingrédient</button>
      </div>

      <div class="form-actions" style="margin-top: 2rem;">
        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">Publier ma recette</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('image_file');
  const labelText = document.getElementById('upload-label-text');
  if (fileInput && labelText) {
    fileInput.addEventListener('change', function(e) {
      if (this.files && this.files.length > 0) {
        labelText.textContent = '🟢 Sélectionné : ' + this.files[0].name;
        labelText.style.color = 'var(--emerald)';
      } else {
        labelText.textContent = 'Glissez ou cliquez pour uploader une photo';
        labelText.style.color = '';
      }
    });
  }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
