<?php
/**
 * Admin — gestion des catégories (ajout, modification, suppression).
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id     = isset($_GET['id'])     ? (int) $_GET['id'] : 0;
$errors = [];

// Suppression d'une catégorie
if ($action === 'delete' && $id) {
    if (compterRecettesCategorie($pdo, $id) > 0) {
        setFlash('danger', 'Impossible de supprimer : des recettes sont liées à cette catégorie.');
    } else {
        supprimerCategorie($pdo, $id);
        setFlash('success', 'Catégorie supprimée.');
    }
    header('Location: ' . url('admin/categories'));
    exit;
}

// Formulaire d'ajout ou de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $nom     = trim(isset($_POST['nom'])     ? $_POST['nom']     : '');
    $icone   = trim(isset($_POST['icone'])   ? $_POST['icone']   : '🍽️');
    $couleur = trim(isset($_POST['couleur']) ? $_POST['couleur'] : '#C8603A');
    $editId  = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;

    if (strlen($nom) < 2) {
        $errors[] = 'Nom trop court.';
    }

    if (empty($errors)) {
        $slug = slugify($nom);
        if ($editId) {
            modifierCategorie($pdo, $editId, $nom, $slug, $icone, $couleur);
            setFlash('success', 'Catégorie mise à jour.');
        } else {
            creerCategorie($pdo, $nom, $slug, $icone, $couleur);
            setFlash('success', 'Catégorie ajoutée.');
        }
        header('Location: ' . url('admin/categories'));
        exit;
    }
}

// Si on est en mode édition, on récupère la catégorie à modifier
$categorie = null;
if ($action === 'edit' && $id) {
    $categorie = getCategorieById($pdo, $id);
}

$categories = getCategoriesAvecCompte($pdo);

$pageTitle = 'Admin – Catégories';
include __DIR__ . '/../header.php';
?>
<div class="admin-layout">
<?php include __DIR__ . '/../admin-sidebar.php'; ?>
<div class="admin-content">

  <div class="admin-header">
    <h1>🗂️ Gestion des catégories</h1>
    <?php if ($action === 'list'): ?>
    <a href="?action=add" class="btn btn-primary">+ Nouvelle catégorie</a>
    <?php endif; ?>
  </div>

  <?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="admin-form-card">
    <h2><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> une catégorie</h2>
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?= e($errors[0]) ?></div>
    <?php endif; ?>
    <form method="POST">
      <?= csrfField() ?>
      <input type="hidden" name="edit_id" value="<?= $action === 'edit' ? $id : 0 ?>">
      <div class="form-row">
        <div class="form-group flex-2">
          <label>Nom *</label>
          <input type="text" name="nom" class="form-control" required
                 value="<?= e(isset($categorie['nom']) ? $categorie['nom'] : '') ?>">
        </div>
        <div class="form-group">
          <label>Icône (emoji)</label>
          <input type="text" name="icone" class="form-control" maxlength="5"
                 value="<?= e(isset($categorie['icone']) ? $categorie['icone'] : '🍽️') ?>">
        </div>
        <div class="form-group">
          <label>Couleur</label>
          <input type="color" name="couleur" class="form-control"
                 value="<?= e(isset($categorie['couleur']) ? $categorie['couleur'] : '#C8603A') ?>">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= url('admin/categories') ?>" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
  <?php else: ?>
  <table class="admin-table">
    <thead><tr><th>Icône</th><th>Nom</th><th>Slug</th><th>Couleur</th><th>Recettes</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
      <tr>
        <td><?= e($cat['icone']) ?></td>
        <td><?= e($cat['nom']) ?></td>
        <td><code><?= e($cat['slug']) ?></code></td>
        <td><span class="color-swatch" style="background:<?= e($cat['couleur']) ?>"></span></td>
        <td><?= (int)$cat['nb_recettes'] ?></td>
        <td class="actions">
          <a href="?action=edit&id=<?= $cat['id'] ?>" class="btn-icon">✏️</a>
          <a href="?action=delete&id=<?= $cat['id'] ?>" class="btn-icon text-danger"
             onclick="return confirm('Supprimer « <?= e(addslashes($cat['nom'])) ?> » ?')">🗑️</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>


