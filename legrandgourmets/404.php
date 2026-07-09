<?php
/**
 * Page d'erreur 404 — affichée quand une URL n'existe pas.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';
http_response_code(404);

$pageTitle = 'Page introuvable';
include __DIR__ . '/header.php';
?>

<div class="container">
  <div class="empty-state" style="padding: 5rem 1rem;">
    <h1 style="font-size: 3rem; margin-bottom: .5rem;">404</h1>
    <p>😕 Cette page n'existe pas ou a été déplacée.</p>
    <a href="<?= url('') ?>" class="btn btn-primary">Retour à l'accueil</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
