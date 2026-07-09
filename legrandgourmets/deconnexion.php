<?php
/**
 * Déconnexion — vide la session et redirige vers la page de connexion.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fonctions.php';

// On vide les variables de session
$_SESSION = [];

// On supprime aussi le cookie de session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: ' . url('connexion'));
exit;
