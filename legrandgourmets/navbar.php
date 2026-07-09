<?php
/**
 * Barre de navigation principale (incluse par header.php).
 * On détecte la page courante via le nom du fichier PHP appelé.
 */
$nomPage = basename($_SERVER['PHP_SELF']); // ex: "recettes.php"
$user = currentUser();

/** Retourne "active" si le nom de la page contient $page. */
function navActive($page, $nomPage) {
    return strpos($nomPage, $page) !== false ? 'active' : '';
}
?>
<nav class="navbar">
  <div class="navbar-container">
    <a href="<?= url('') ?>" class="navbar-brand">🍽️ LE GRAND GOURMET</a>

    <button class="navbar-toggle" id="navToggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>

    <ul class="navbar-nav" id="navMenu">
      <li><a href="<?= url('') ?>" class="<?= $nomPage === 'index.php' && strpos($_SERVER['PHP_SELF'], '/admin/') === false ? 'active' : '' ?>">Accueil</a></li>
      <li><a href="<?= url('recettes') ?>" class="<?= navActive('recettes', $nomPage) ?>">Recettes</a></li>
      <li><a href="<?= url('top-recettes') ?>" class="<?= navActive('top-recettes', $nomPage) ?>">🏆 Top</a></li>
      <li><a href="<?= url('aleatoire') ?>" title="Recette aléatoire">🎲</a></li>

      <?php if (isLoggedIn()): ?>
      <li><a href="<?= url('ajouter-recette') ?>" class="btn-nav <?= navActive('ajouter-recette', $nomPage) ?>">➕ Publier</a></li>
      <li><a href="<?= url('favoris') ?>" class="<?= navActive('favoris', $nomPage) ?>">❤️ Favoris</a></li>
      <li class="nav-dropdown">
        <a href="#" class="nav-dropdown-toggle">
          <?= $user['avatar'] ? '<img src="' . e($user['avatar']) . '" class="nav-avatar">' : '👤' ?>
          <?= e($user['pseudo']) ?>
        </a>
        <ul class="nav-dropdown-menu">
          <li><a href="<?= url('profil') ?>">Mon profil</a></li>
          <?php if (isAdmin()): ?>
          <li><a href="<?= url('admin') ?>">⚙️ Administration</a></li>
          <?php endif; ?>
          <li class="divider"></li>
          <li><a href="<?= url('deconnexion') ?>" class="text-danger">Se déconnecter</a></li>
        </ul>
      </li>
      <?php else: ?>
      <li><a href="<?= url('connexion') ?>" class="btn-nav">Connexion</a></li>
      <li><a href="<?= url('inscription') ?>" class="btn-nav btn-nav-outline">Inscription</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
