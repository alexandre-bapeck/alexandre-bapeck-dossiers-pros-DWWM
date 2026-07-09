<?php
/**
 * Admin — gestion des utilisateurs.
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id     = isset($_GET['id'])     ? (int) $_GET['id'] : 0;
$errors = [];
$meId   = currentUser()['id'];

// Suppression d'un utilisateur (sauf soi-même)
if ($action === 'delete' && $id) {
    if ($id === $meId) {
        setFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
    } else {
        supprimerUtilisateur($pdo, $id);
        setFlash('success', 'Utilisateur supprimé.');
    }
    header('Location: ' . url('admin/utilisateurs'));
    exit;
}

// Formulaire de modification d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $pseudo = trim(isset($_POST['pseudo']) ? $_POST['pseudo'] : '');
    $email  = trim(isset($_POST['email'])  ? $_POST['email']  : '');
    $editId = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;
    $mdpNew = isset($_POST['mdp_new']) ? $_POST['mdp_new'] : '';

    // Rôle : on accepte seulement 'user' ou 'admin'
    $roleSoumis = isset($_POST['role']) ? $_POST['role'] : '';
    $role = in_array($roleSoumis, ['user', 'admin']) ? $roleSoumis : 'user';

    if (strlen($pseudo) < 3) {
        $errors[] = 'Pseudo trop court.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail invalide.';
    }

    if (empty($errors)) {
        $hash = $mdpNew !== '' ? password_hash($mdpNew, PASSWORD_BCRYPT) : null;
        modifierUtilisateur($pdo, $editId, $pseudo, $email, $hash);
        modifierRoleUtilisateur($pdo, $editId, $role);

        setFlash('success', 'Utilisateur mis à jour.');
        header('Location: ' . url('admin/utilisateurs'));
        exit;
    }
}

// Si on est en mode édition, on récupère l'utilisateur à modifier
$utilisateur = null;
if ($action === 'edit' && $id) {
    $utilisateur = getUserById($pdo, $id);
}

$utilisateurs = getUtilisateursAvecStats($pdo);

$pageTitle = 'Admin – Utilisateurs';
include __DIR__ . '/../header.php';
?>
<div class="admin-layout">
<?php include __DIR__ . '/../admin-sidebar.php'; ?>
<div class="admin-content">

  <div class="admin-header">
    <h1>👥 Gestion des utilisateurs</h1>
  </div>

  <?php if ($action === 'edit' && $utilisateur): ?>
  <div class="admin-form-card">
    <h2>Modifier l'utilisateur — <?= e($utilisateur['pseudo']) ?></h2>
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?= e($errors[0]) ?></div>
    <?php endif; ?>
    <form method="POST">
      <?= csrfField() ?>
      <input type="hidden" name="edit_id" value="<?= $id ?>">
      <div class="form-row">
        <div class="form-group">
          <label>Pseudo</label>
          <input type="text" name="pseudo" class="form-control" value="<?= e($utilisateur['pseudo']) ?>" required>
        </div>
        <div class="form-group flex-2">
          <label>E-mail</label>
          <input type="email" name="email" class="form-control" value="<?= e($utilisateur['email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Rôle</label>
          <select name="role" class="form-control">
            <option value="user"  <?= $utilisateur['role'] === 'user'  ? 'selected' : '' ?>>Utilisateur</option>
            <option value="admin" <?= $utilisateur['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Nouveau mot de passe <small>(laisser vide pour ne pas changer)</small></label>
        <input type="password" name="mdp_new" class="form-control">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= url('admin/utilisateurs') ?>" class="btn btn-outline">Annuler</a>
      </div>
    </form>
  </div>
  <?php else: ?>
  <table class="admin-table">
    <thead><tr><th>Pseudo</th><th>E-mail</th><th>Rôle</th><th>Recettes</th><th>Avis</th><th>Inscription</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($utilisateurs as $u): ?>
      <tr>
        <td><?= e($u['pseudo']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-danger' : 'badge-info' ?>"><?= e($u['role']) ?></span></td>
        <td><?= (int)(isset($u['nb_recettes']) ? $u['nb_recettes'] : 0) ?></td>
        <td><?= (int)(isset($u['nb_commentaires']) ? $u['nb_commentaires'] : 0) ?></td>
        <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
        <td class="actions">
          <a href="?action=edit&id=<?= $u['id'] ?>" class="btn-icon">✏️</a>
          <?php if ($u['id'] !== $meId): ?>
          <a href="?action=delete&id=<?= $u['id'] ?>" class="btn-icon text-danger"
             onclick="return confirm('Supprimer cet utilisateur et toutes ses données ?')">🗑️</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>


