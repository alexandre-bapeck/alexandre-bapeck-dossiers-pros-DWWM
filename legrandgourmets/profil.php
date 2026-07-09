<?php
/**
 * Page "Mon profil" — l'utilisateur peut modifier ses informations et son mot de passe.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
requireLogin();

$userId = currentUser()['id'];
$errors = [];

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    $pseudo = trim(isset($_POST['pseudo'])  ? $_POST['pseudo']  : '');
    $email  = trim(isset($_POST['email'])   ? $_POST['email']   : '');
    $mdpOld = isset($_POST['mdp_old']) ? $_POST['mdp_old'] : '';
    $mdpNew = isset($_POST['mdp_new']) ? $_POST['mdp_new'] : '';

    // Validations de base
    if (strlen($pseudo) < 3) {
        $errors[] = 'Pseudo trop court.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail invalide.';
    }
    if (pseudoOuEmailExiste($pdo, $pseudo, $email, $userId)) {
        $errors[] = 'Pseudo ou e-mail déjà utilisé.';
    }

    // Si l'utilisateur veut changer son mot de passe
    if (empty($errors)) {
        $user = getUserById($pdo, $userId);

        if ($mdpNew !== '') {
            if (!password_verify($mdpOld, $user['mot_de_passe'])) {
                $errors[] = 'Mot de passe actuel incorrect.';
            } elseif (strlen($mdpNew) < 8) {
                $errors[] = 'Nouveau mot de passe : 8 caractères minimum.';
            }
        }

        // Si toujours pas d'erreur : on met à jour
        if (empty($errors)) {
            $hash = $mdpNew !== '' ? password_hash($mdpNew, PASSWORD_BCRYPT) : null;
            modifierUtilisateur($pdo, $userId, $pseudo, $email, $hash);
            $_SESSION['pseudo'] = $pseudo;

            setFlash('success', 'Profil mis à jour.');
            header('Location: ' . url('profil'));
            exit;
        }
    }
}

// Chargement des données pour l'affichage
$profil         = getUserById($pdo, $userId);
$nbFavoris      = compterFavorisUtilisateur($pdo, $userId);
$nbCommentaires = compterCommentairesUtilisateur($pdo, $userId);

$pageTitle = 'Mon profil';
include __DIR__ . '/header.php';
?>

<div class="container">
  <div class="profil-grid">
    <div class="profil-stats-card">
      <div class="profil-avatar">
        <?= !empty($profil['avatar']) ? '<img src="' . e($profil['avatar']) . '" alt="avatar">' : '<span class="avatar-placeholder">👤</span>' ?>
      </div>
      <h2><?= e($profil['pseudo']) ?></h2>
      <p class="text-muted">Membre depuis le <?= date('d/m/Y', strtotime($profil['date_inscription'])) ?></p>
      <div class="stats-row">
        <div class="stat"><span class="stat-val"><?= $nbFavoris ?></span><span class="stat-label">Favoris</span></div>
        <div class="stat"><span class="stat-val"><?= $nbCommentaires ?></span><span class="stat-label">Avis</span></div>
      </div>
    </div>

    <div class="profil-form-card">
      <h2>Modifier mes informations</h2>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><p class="mb-0"><?= e($err) ?></p><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="<?= url('profil') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label>Pseudo</label>
          <input type="text" name="pseudo" class="form-control" value="<?= e($profil['pseudo']) ?>" required>
        </div>
        <div class="form-group">
          <label>E-mail</label>
          <input type="email" name="email" class="form-control" value="<?= e($profil['email']) ?>" required>
        </div>
        <hr>
        <p class="text-muted">Laisser vide pour ne pas changer le mot de passe.</p>
        <div class="form-group">
          <label>Mot de passe actuel</label>
          <input type="password" name="mdp_old" class="form-control">
        </div>
        <div class="form-group">
          <label>Nouveau mot de passe</label>
          <input type="password" name="mdp_new" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
