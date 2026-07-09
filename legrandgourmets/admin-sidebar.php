<?php
/**
 * Menu latéral de la partie administration (inclus par chaque page admin).
 * On détecte la page admin courante via le nom du fichier PHP.
 */
$nomPageAdmin = basename($_SERVER['PHP_SELF']); // ex: "recettes.php"

/** Retourne "active" si on est sur cette page admin. */
function adminActive($page, $nomPage) {
    return $page === $nomPage ? 'active' : '';
}
?>
<aside class="admin-sidebar">
  <div class="admin-brand">🍽️ LE GRAND GOURMET<span class="admin-tag">Admin</span></div>
  <nav class="admin-nav">
    <a href="<?= url('admin') ?>"              class="<?= adminActive('index.php',        $nomPageAdmin) ?>">🏠 Tableau de bord</a>
    <a href="<?= url('admin/recettes') ?>"     class="<?= adminActive('recettes.php',     $nomPageAdmin) ?>">🍴 Recettes</a>
    <a href="<?= url('admin/categories') ?>"   class="<?= adminActive('categories.php',   $nomPageAdmin) ?>">🗂️ Catégories</a>
    <a href="<?= url('admin/utilisateurs') ?>" class="<?= adminActive('utilisateurs.php', $nomPageAdmin) ?>">👥 Utilisateurs</a>
    <a href="<?= url('admin/commentaires') ?>" class="<?= adminActive('commentaires.php', $nomPageAdmin) ?>">💬 Commentaires</a>
  </nav>
  <a href="<?= url('') ?>" class="admin-back-link">← Retour au site</a>
</aside>
