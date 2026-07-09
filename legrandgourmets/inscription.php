<?php
/**
 * Page d'inscription.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/email.php';

// Si l'utilisateur est déjà connecté, on le renvoie sur l'accueil
if (isLoggedIn()) {
    header('Location: ' . url(''));
    exit;
}

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim(isset($_POST['pseudo']) ? $_POST['pseudo'] : '');
    $email  = trim(isset($_POST['email'])  ? $_POST['email']  : '');
    $mdp    = isset($_POST['mot_de_passe'])         ? $_POST['mot_de_passe']         : '';
    $mdp2   = isset($_POST['mot_de_passe_confirm']) ? $_POST['mot_de_passe_confirm'] : '';
    $old    = ['pseudo' => $pseudo, 'email' => $email];

    // Validations
    if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
        $errors[] = 'Le pseudo doit contenir entre 3 et 50 caractères.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse e-mail invalide.';
    }
    if (strlen($mdp) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($mdp !== $mdp2) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    // Si tout est OK, on crée l'utilisateur
    if (empty($errors)) {
        if (pseudoOuEmailExiste($pdo, $pseudo, $email)) {
            $errors[] = 'Ce pseudo ou cet e-mail est déjà utilisé.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $id   = creerUtilisateur($pdo, $pseudo, $email, $hash);

            // Envoi du mail de bienvenue (Mission 3)
            envoyerMailBienvenue($email, $pseudo);

            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['pseudo']  = $pseudo;
            $_SESSION['role']    = 'user';
            $_SESSION['avatar']  = null;

            setFlash('success', 'Bienvenue sur LE GRAND GOURMET, ' . $pseudo . ' ! 🎉');
            header('Location: ' . url(''));
            exit;
        }
    }
}

$pageTitle = 'Inscription';
include __DIR__ . '/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">🍽️ <span>LE GRAND GOURMET</span></div>
    <h1 class="auth-title">Créer un compte</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('inscription') ?>" novalidate>
      <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" id="pseudo" name="pseudo" class="form-control"
               value="<?= e(isset($old['pseudo']) ? $old['pseudo'] : '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" class="form-control"
               value="<?= e(isset($old['email']) ? $old['email'] : '') ?>" required>
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Mot de passe <small>(min. 8 caractères)</small></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="mot_de_passe_confirm">Confirmer le mot de passe</label>
        <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
    </form>

    <p class="auth-link">Déjà un compte ? <a href="<?= url('connexion') ?>">Se connecter</a></p>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
