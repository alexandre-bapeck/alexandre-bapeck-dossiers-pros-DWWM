<?php
/**
 * Recette aléatoire — redirige vers une recette tirée au hasard.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

$slug = getSlugRecetteAleatoire($pdo);

if ($slug) {
    header('Location: ' . url('recette?slug=' . urlencode($slug)));
} else {
    header('Location: ' . url('recettes'));
}
exit;
