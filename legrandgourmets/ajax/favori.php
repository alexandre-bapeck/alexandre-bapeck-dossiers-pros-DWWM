<?php
/**
 * Endpoint AJAX — ajoute ou retire une recette des favoris.
 * Renvoie une réponse au format JSON.
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
header('Content-Type: application/json');

// Pas connecté : on renvoie une URL de redirection
if (!isLoggedIn()) {
    echo json_encode(['redirect' => url('connexion')]);
    exit;
}

// Cette action ne s'utilise qu'en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$recetteId = isset($_POST['recette_id']) ? (int) $_POST['recette_id'] : 0;
if (!$recetteId) {
    http_response_code(400);
    exit;
}

$status = basculerFavori($pdo, currentUser()['id'], $recetteId);
echo json_encode(['status' => $status]);
