<?php
/**
 * Page de connexion.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

// Si l'utilisateur est déjà connecté, on le renvoie sur l'accueil
if (isLoggedIn()) {
    header('Location: ' . url(''));
    exit;
}

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $mdp   = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
    $old   = ['email' => $email];

    if (empty($email) || empty($mdp)) {
        $errors[] = 'Veuillez remplir tous les champs.';
    } else {
        $user = getUserByEmail($pdo, $email);

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            // Connexion réussie : on enregistre l'utilisateur dans la session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo']  = $user['pseudo'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['avatar']  = isset($user['avatar']) ? $user['avatar'] : null;

            setFlash('success', 'Bon retour, ' . $user['pseudo'] . ' ! 👋');

            // Les admins vont sur le tableau de bord, les users sur l'accueil
            if ($user['role'] === 'admin') {
                header('Location: ' . url('admin'));
            } else {
                header('Location: ' . url(''));
            }
            exit;
        } else {
            $errors[] = 'E-mail ou mot de passe incorrect.';
        }
    }
}

$pageTitle = 'Connexion';
include __DIR__ . '/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">🍽️ <span>LE GRAND GOURMET</span></div>
    <h1 class="auth-title">Se connecter</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $err): ?>
      <p class="mb-0"><?= e($err) ?></p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('connexion') ?>" novalidate>
      <div class="form-group">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" class="form-control"
               value="<?= e(isset($old['email']) ? $old['email'] : '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>

    <p class="auth-link">Pas encore de compte ? <a href="<?= url('inscription') ?>">S'inscrire</a></p>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
