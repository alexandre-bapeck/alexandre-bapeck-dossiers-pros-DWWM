<?php
/**
 * Fonctions utilisées dans tout le site :
 *   1. URL de base + utilitaires (url, asset, e, slugify, recipeImage)
 *   2. Session, connexion, droits d'admin, messages flash, protection CSRF
 *   3. Fonctions de base de données, regroupées par table
 *
 * Toutes les fonctions de base de données reçoivent $pdo en premier paramètre.
 */

/* ============================================================
 *   1. CONFIGURATION DE BASE + UTILITAIRES
 * ============================================================ */

// Démarrage de la session (une seule fois)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL de base du site (détectée automatiquement).
// On calcule le chemin web du dossier de l'application à partir de sa position
// réelle sous la racine du serveur. basename(__DIR__) ne suffirait pas : il ne
// garde que le dernier dossier et perdrait les dossiers parents.
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];

$racine  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$courant = str_replace('\\', '/', __DIR__);

if ($racine !== '' && strpos($courant, $racine) === 0) {
    // ex: "/legrandgourmet/legrandgourmets"
    $dossier = rtrim(substr($courant, strlen($racine)), '/');
} else {
    // Repli si le dossier est hors DOCUMENT_ROOT (alias Apache, lien symbolique...)
    $dossier = '/' . basename(__DIR__);
}

define('BASE_URL', $protocol . '://' . $host . $dossier);

/** Construit une URL interne (ex: url('recettes') => /legrandgourmet/recettes.php). */
function url($page = '')
{
    if ($page === '') {
        return BASE_URL . '/';
    }
    if ($page === 'admin') {
        return BASE_URL . '/admin/';
    }
    // Si l'URL contient un "?" (paramètres GET), on ajoute .php avant le ?
    $pos = strpos($page, '?');
    if ($pos === false) {
        return BASE_URL . '/' . $page . '.php';
    }
    $nom    = substr($page, 0, $pos);
    $params = substr($page, $pos);
    return BASE_URL . '/' . $nom . '.php' . $params;
}

/** Construit l'URL d'un fichier statique (css, js, image). */
function asset($path)
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/** Retourne l'URL de l'image d'une recette (URL externe ou fichier uploadé). */
function recipeImage($path)
{
    if (empty($path)) {
        return asset('img/placeholder.jpg');
    }
    if (substr($path, 0, 4) === 'http') {
        return $path;
    }
    return asset('uploads/' . ltrim($path, '/'));
}

/** Échappe un texte pour l'afficher dans du HTML (sécurité XSS). */
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Transforme un texte en slug (ex: "Pâtes carbonara" => "pates-carbonara"). */
function slugify($text)
{
    // Remplacement manuel des accents et caractères spéciaux par leur équivalent ASCII
    $accents = [
        'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae',
        'ç'=>'c',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
        'ñ'=>'n',
        'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o','œ'=>'oe',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ý'=>'y','ÿ'=>'y',
        'À'=>'a','Á'=>'a','Â'=>'a','Ã'=>'a','Ä'=>'a','Å'=>'a','Æ'=>'ae',
        'Ç'=>'c',
        'È'=>'e','É'=>'e','Ê'=>'e','Ë'=>'e',
        'Ì'=>'i','Í'=>'i','Î'=>'i','Ï'=>'i',
        'Ñ'=>'n',
        'Ò'=>'o','Ó'=>'o','Ô'=>'o','Õ'=>'o','Ö'=>'o','Ø'=>'o','Œ'=>'oe',
        'Ù'=>'u','Ú'=>'u','Û'=>'u','Ü'=>'u',
        'Ý'=>'y',
    ];
    $text = strtr($text, $accents);
    $text = strtolower(trim($text));
    // Remplace tout ce qui n'est pas lettre/chiffre par un tiret
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* ============================================================
 *   2. SESSION, CONNEXION, MESSAGES FLASH, CSRF
 * ============================================================ */

/** Vrai si l'utilisateur est connecté. */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/** Vrai si l'utilisateur est administrateur. */
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/** Bloque l'accès si l'utilisateur n'est pas connecté. */
function requireLogin()
{
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Veuillez vous connecter.'];
        header('Location: ' . url('connexion'));
        exit;
    }
}

/** Bloque l'accès si l'utilisateur n'est pas admin. */
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Accès réservé aux administrateurs.'];
        header('Location: ' . url(''));
        exit;
    }
}

/** Retourne les infos de l'utilisateur connecté, ou null. */
function currentUser()
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'     => $_SESSION['user_id'],
        'pseudo' => $_SESSION['pseudo'],
        'role'   => $_SESSION['role'],
        'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : null,
    ];
}

/** Enregistre un message flash (affiché une seule fois). */
function setFlash($type, $msg)
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

/** Récupère puis efface le message flash. */
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Crée le jeton CSRF s'il n'existe pas et le retourne. */
function getCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Génère un champ caché contenant le jeton CSRF pour les formulaires. */
function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . e(getCsrfToken()) . '">';
}

/** Vérifie le jeton CSRF d'un formulaire POST. */
function verifyCsrfToken()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        $valid = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
        if (!$token || !hash_equals($valid, $token)) {
            setFlash('danger', 'Erreur de sécurité : Jeton CSRF invalide.');
            $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('');
            header('Location: ' . $back);
            exit;
        }
    }
}

/* ============================================================
 *   3. FONCTIONS DE BASE DE DONNÉES
 * ============================================================ */

/* ── Table RECETTES ───────────────────────────────────────── */

/** Retourne les recettes filtrées et paginées. */
function getRecettes($pdo, $filtres = [], $limit = 9, $offset = 0)
{
    $where  = ['r.est_publiee = 1'];
    $params = [];
    if (!empty($filtres['cat'])) {
        $where[]  = 'c.slug = ?';
        $params[] = $filtres['cat'];
    }
    if (!empty($filtres['diff'])) {
        $where[]  = 'r.difficulte = ?';
        $params[] = $filtres['diff'];
    }
    if (!empty($filtres['q'])) {
        $where[]  = '(r.titre LIKE ? OR r.description LIKE ?)';
        $params[] = '%' . $filtres['q'] . '%';
        $params[] = '%' . $filtres['q'] . '%';
    }
    $whereSQL = implode(' AND ', $where);

    // Tri
    $tri = isset($filtres['tri']) ? $filtres['tri'] : 'recent';
    if ($tri === 'note') {
        $orderSQL = 'note_moy DESC';
    } elseif ($tri === 'rapide') {
        $orderSQL = 'duree_totale ASC';
    } else {
        $orderSQL = 'r.date_creation DESC';
    }

    $params[] = $limit;
    $params[] = $offset;

    $sql = "SELECT r.*, c.nom AS cat_nom, c.slug AS cat_slug, c.icone AS cat_icone,
                   u.pseudo AS auteur,
                   COALESCE(AVG(n.valeur), 0) AS note_moy,
                   COUNT(DISTINCT n.utilisateur_id) AS nb_notes,
                   (r.temps_preparation + r.temps_cuisson) AS duree_totale
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            LEFT JOIN notes   n ON n.recette_id = r.id
            WHERE $whereSQL
            GROUP BY r.id
            ORDER BY $orderSQL
            LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Compte le nombre de recettes pour les filtres donnés. */
function countRecettes($pdo, $filtres = [])
{
    $where  = ['r.est_publiee = 1'];
    $params = [];
    if (!empty($filtres['cat'])) {
        $where[]  = 'c.slug = ?';
        $params[] = $filtres['cat'];
    }
    if (!empty($filtres['diff'])) {
        $where[]  = 'r.difficulte = ?';
        $params[] = $filtres['diff'];
    }
    if (!empty($filtres['q'])) {
        $where[]  = '(r.titre LIKE ? OR r.description LIKE ?)';
        $params[] = '%' . $filtres['q'] . '%';
        $params[] = '%' . $filtres['q'] . '%';
    }
    $whereSQL = implode(' AND ', $where);

    $sql = "SELECT COUNT(DISTINCT r.id)
            FROM recettes r
            JOIN categories c ON c.id = r.categorie_id
            WHERE $whereSQL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

/** Retourne une recette à partir de son slug, ou null. */
function getRecetteBySlug($pdo, $slug)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, c.slug AS cat_slug, c.icone AS cat_icone,
                   u.pseudo AS auteur
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            WHERE r.slug = ? AND r.est_publiee = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug]);
    $r = $stmt->fetch();
    return $r ? $r : null;
}

/** Retourne la liste des ingrédients d'une recette. */
function getIngredientsRecette($pdo, $id)
{
    $sql = "SELECT i.nom, ri.quantite
            FROM recette_ingredient ri
            JOIN ingredients i ON i.id = ri.ingredient_id
            WHERE ri.recette_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

/** Dernières recettes mises en avant sur la page d'accueil. */
function getRecettesFeatured($pdo, $limit = 6)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, c.slug AS cat_slug, c.icone AS cat_icone,
                   u.pseudo AS auteur,
                   COALESCE(AVG(n.valeur), 0) AS note_moy,
                   COUNT(DISTINCT n.utilisateur_id) AS nb_notes,
                   (r.temps_preparation + r.temps_cuisson) AS duree_totale
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            LEFT JOIN notes   n ON n.recette_id = r.id
            WHERE r.est_publiee = 1
            GROUP BY r.id
            ORDER BY r.date_creation DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/** Recettes les mieux notées. */
function getTopRecettes($pdo, $limit = 10)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, c.slug AS cat_slug, c.icone AS cat_icone,
                   u.pseudo AS auteur,
                   COALESCE(AVG(n.valeur), 0) AS note_moy,
                   COUNT(DISTINCT n.utilisateur_id) AS nb_notes,
                   (r.temps_preparation + r.temps_cuisson) AS duree_totale
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            LEFT JOIN notes   n ON n.recette_id = r.id
            WHERE r.est_publiee = 1
            GROUP BY r.id
            HAVING nb_notes > 0
            ORDER BY note_moy DESC, nb_notes DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/** Recette du jour (la mieux notée). */
function getRecetteDuJour($pdo)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, c.icone AS cat_icone, u.pseudo AS auteur,
                   COALESCE(AVG(n.valeur), 0) AS note_moy
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            LEFT JOIN notes   n ON n.recette_id = r.id
            WHERE r.est_publiee = 1
            GROUP BY r.id
            ORDER BY note_moy DESC, r.date_creation DESC
            LIMIT 1";
    $r = $pdo->query($sql)->fetch();
    return $r ? $r : null;
}

/** Slug d'une recette tirée au hasard. */
function getSlugRecetteAleatoire($pdo)
{
    $sql = 'SELECT slug FROM recettes WHERE est_publiee = 1 ORDER BY RAND() LIMIT 1';
    $slug = $pdo->query($sql)->fetchColumn();
    return $slug ? $slug : null;
}

/** Incrémente le compteur de vues d'une recette. */
function incrementerVues($pdo, $id)
{
    $stmt = $pdo->prepare('UPDATE recettes SET vues = vues + 1 WHERE id = ?');
    $stmt->execute([$id]);
}

/** Liste de toutes les recettes (publiées ou non) pour l'admin. */
function getRecettesAdmin($pdo)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, u.pseudo AS auteur
            FROM recettes r
            JOIN categories  c ON c.id = r.categorie_id
            JOIN utilisateurs u ON u.id = r.auteur_id
            ORDER BY r.date_creation DESC";
    return $pdo->query($sql)->fetchAll();
}

/** Recette par son id. */
function getRecetteById($pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM recettes WHERE id = ?');
    $stmt->execute([$id]);
    $r = $stmt->fetch();
    return $r ? $r : null;
}

/** Crée une recette et retourne son id. */
function creerRecette($pdo, $d)
{
    $sql = "INSERT INTO recettes
              (titre, slug, description, instructions, temps_preparation, temps_cuisson,
               nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $d['titre'], $d['slug'], $d['description'], $d['instructions'],
        $d['temps_preparation'], $d['temps_cuisson'], $d['nb_personnes'],
        $d['difficulte'], $d['image'], $d['est_publiee'],
        $d['categorie_id'], $d['auteur_id'],
    ]);
    return (int) $pdo->lastInsertId();
}

/** Met à jour une recette. */
function modifierRecette($pdo, $id, $d)
{
    $sql = "UPDATE recettes SET
              titre = ?, slug = ?, description = ?, instructions = ?,
              temps_preparation = ?, temps_cuisson = ?, nb_personnes = ?,
              difficulte = ?, image = ?, est_publiee = ?, categorie_id = ?
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $d['titre'], $d['slug'], $d['description'], $d['instructions'],
        $d['temps_preparation'], $d['temps_cuisson'], $d['nb_personnes'],
        $d['difficulte'], $d['image'], $d['est_publiee'], $d['categorie_id'], $id,
    ]);
}

/** Supprime une recette. */
function supprimerRecette($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM recettes WHERE id = ?');
    $stmt->execute([$id]);
}

/** Inverse l'état "publiée / brouillon" d'une recette. */
function basculerPublication($pdo, $id)
{
    $stmt = $pdo->prepare('UPDATE recettes SET est_publiee = NOT est_publiee WHERE id = ?');
    $stmt->execute([$id]);
}

/** Vérifie qu'un slug n'est pas déjà utilisé (sauf par la recette en cours d'édition). */
function slugRecetteExiste($pdo, $slug, $excludeId = 0)
{
    $stmt = $pdo->prepare('SELECT id FROM recettes WHERE slug = ? AND id != ?');
    $stmt->execute([$slug, $excludeId]);
    return (bool) $stmt->fetch();
}

/** Nombre total de recettes. */
function compterRecettes($pdo)
{
    return (int) $pdo->query('SELECT COUNT(*) FROM recettes')->fetchColumn();
}

/** Enregistre les ingrédients d'une recette (supprime les anciens puis insère les nouveaux). */
function enregistrerIngredients($pdo, $recetteId, $noms, $quantites)
{
    // 1. On supprime les anciens liens
    $stmt = $pdo->prepare('DELETE FROM recette_ingredient WHERE recette_id = ?');
    $stmt->execute([$recetteId]);

    // 2. Préparation des requêtes (réutilisées dans la boucle)
    $sqlAddIng = $pdo->prepare('INSERT IGNORE INTO ingredients (nom) VALUES (?)');
    $sqlGetId  = $pdo->prepare('SELECT id FROM ingredients WHERE nom = ?');
    $sqlLink   = $pdo->prepare(
        'INSERT INTO recette_ingredient (recette_id, ingredient_id, quantite) VALUES (?,?,?)'
    );

    // 3. Pour chaque ingrédient saisi
    foreach ($noms as $i => $nom) {
        $nom = trim($nom);
        $qty = trim(isset($quantites[$i]) ? $quantites[$i] : '');

        if ($nom === '' || $qty === '') {
            continue;
        }

        $sqlAddIng->execute([$nom]);
        $sqlGetId->execute([$nom]);
        $ingId = $sqlGetId->fetchColumn();
        $sqlLink->execute([$recetteId, $ingId, $qty]);
    }
}

/* ── Table UTILISATEURS ───────────────────────────────────── */

/** Utilisateur par son id. */
function getUserById($pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    return $u ? $u : null;
}

/** Utilisateur par son e-mail. */
function getUserByEmail($pdo, $email)
{
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    return $u ? $u : null;
}

/** Vérifie si un pseudo ou un e-mail est déjà pris. */
function pseudoOuEmailExiste($pdo, $pseudo, $email, $excludeId = 0)
{
    $sql = 'SELECT id FROM utilisateurs WHERE (pseudo = ? OR email = ?) AND id != ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pseudo, $email, $excludeId]);
    return (bool) $stmt->fetch();
}

/** Crée un nouvel utilisateur et retourne son id. */
function creerUtilisateur($pdo, $pseudo, $email, $hash)
{
    $stmt = $pdo->prepare(
        'INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?,?,?)'
    );
    $stmt->execute([$pseudo, $email, $hash]);
    return (int) $pdo->lastInsertId();
}

/** Modifie un utilisateur (avec ou sans nouveau mot de passe). */
function modifierUtilisateur($pdo, $id, $pseudo, $email, $hash = null)
{
    if ($hash !== null) {
        $sql = 'UPDATE utilisateurs SET pseudo = ?, email = ?, mot_de_passe = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pseudo, $email, $hash, $id]);
    } else {
        $sql = 'UPDATE utilisateurs SET pseudo = ?, email = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$pseudo, $email, $id]);
    }
}

/** Change le rôle d'un utilisateur ('user' ou 'admin'). */
function modifierRoleUtilisateur($pdo, $id, $role)
{
    $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
    $stmt->execute([$role, $id]);
}

/** Supprime un utilisateur. */
function supprimerUtilisateur($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
    $stmt->execute([$id]);
}

/** Liste des utilisateurs avec leur nombre de recettes et commentaires. */
function getUtilisateursAvecStats($pdo)
{
    $sql = "SELECT u.*,
                   COUNT(DISTINCT r.id) AS nb_recettes,
                   COUNT(DISTINCT c.id) AS nb_commentaires
            FROM utilisateurs u
            LEFT JOIN recettes     r ON r.auteur_id = u.id
            LEFT JOIN commentaires c ON c.auteur_id = u.id
            GROUP BY u.id
            ORDER BY u.date_inscription DESC";
    return $pdo->query($sql)->fetchAll();
}

/** Les N derniers utilisateurs inscrits. */
function getDerniersUtilisateurs($pdo, $limit = 5)
{
    $stmt = $pdo->prepare(
        'SELECT * FROM utilisateurs ORDER BY date_inscription DESC LIMIT ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/** Nombre total d'utilisateurs. */
function compterUtilisateurs($pdo)
{
    return (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
}

/* ── Table CATEGORIES ─────────────────────────────────────── */

/** Toutes les catégories, triées par nom. */
function getCategories($pdo)
{
    return $pdo->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
}

/** Toutes les catégories avec le nombre de recettes associées. */
function getCategoriesAvecCompte($pdo)
{
    $sql = "SELECT c.*, COUNT(r.id) AS nb_recettes
            FROM categories c
            LEFT JOIN recettes r ON r.categorie_id = c.id
            GROUP BY c.id
            ORDER BY c.nom";
    return $pdo->query($sql)->fetchAll();
}

/** Catégorie par son id. */
function getCategorieById($pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $c = $stmt->fetch();
    return $c ? $c : null;
}

/** Nombre de recettes liées à une catégorie. */
function compterRecettesCategorie($pdo, $id)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM recettes WHERE categorie_id = ?');
    $stmt->execute([$id]);
    return (int) $stmt->fetchColumn();
}

/** Crée une nouvelle catégorie. */
function creerCategorie($pdo, $nom, $slug, $icone, $couleur)
{
    $stmt = $pdo->prepare(
        'INSERT INTO categories (nom, slug, icone, couleur) VALUES (?,?,?,?)'
    );
    $stmt->execute([$nom, $slug, $icone, $couleur]);
}

/** Met à jour une catégorie. */
function modifierCategorie($pdo, $id, $nom, $slug, $icone, $couleur)
{
    $stmt = $pdo->prepare(
        'UPDATE categories SET nom = ?, slug = ?, icone = ?, couleur = ? WHERE id = ?'
    );
    $stmt->execute([$nom, $slug, $icone, $couleur, $id]);
}

/** Supprime une catégorie. */
function supprimerCategorie($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
}

/** Nombre total de catégories. */
function compterCategories($pdo)
{
    return (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
}

/* ── Table COMMENTAIRES ───────────────────────────────────── */

/** Tous les commentaires d'une recette (avec le pseudo de l'auteur). */
function getCommentairesRecette($pdo, $recetteId)
{
    $sql = "SELECT c.*, u.pseudo
            FROM commentaires c
            JOIN utilisateurs u ON u.id = c.auteur_id
            WHERE c.recette_id = ?
            ORDER BY c.date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$recetteId]);
    return $stmt->fetchAll();
}

/** Ajoute un nouveau commentaire. */
function creerCommentaire($pdo, $auteurId, $recetteId, $contenu)
{
    $stmt = $pdo->prepare(
        'INSERT INTO commentaires (auteur_id, recette_id, contenu) VALUES (?,?,?)'
    );
    $stmt->execute([$auteurId, $recetteId, $contenu]);
}

/** Supprime un commentaire. */
function supprimerCommentaire($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM commentaires WHERE id = ?');
    $stmt->execute([$id]);
}

/** Nombre de commentaires écrits par un utilisateur. */
function compterCommentairesUtilisateur($pdo, $userId)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM commentaires WHERE auteur_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/** Tous les commentaires (pour la modération admin). */
function getTousCommentaires($pdo)
{
    $sql = "SELECT c.*, u.pseudo, r.titre AS recette_titre, r.slug AS recette_slug
            FROM commentaires c
            JOIN utilisateurs u ON u.id = c.auteur_id
            JOIN recettes     r ON r.id  = c.recette_id
            ORDER BY c.date_creation DESC";
    return $pdo->query($sql)->fetchAll();
}

/** Nombre total de commentaires. */
function compterCommentaires($pdo)
{
    return (int) $pdo->query('SELECT COUNT(*) FROM commentaires')->fetchColumn();
}

/* ── Table FAVORIS ────────────────────────────────────────── */

/** Vrai si la recette est déjà dans les favoris de l'utilisateur. */
function estDejaFavori($pdo, $userId, $recetteId)
{
    $stmt = $pdo->prepare(
        'SELECT 1 FROM favoris WHERE utilisateur_id = ? AND recette_id = ?'
    );
    $stmt->execute([$userId, $recetteId]);
    return (bool) $stmt->fetch();
}

/** Ajoute une recette aux favoris. */
function ajouterFavori($pdo, $userId, $recetteId)
{
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO favoris (utilisateur_id, recette_id) VALUES (?,?)'
    );
    $stmt->execute([$userId, $recetteId]);
}

/** Retire une recette des favoris. */
function retirerFavori($pdo, $userId, $recetteId)
{
    $stmt = $pdo->prepare(
        'DELETE FROM favoris WHERE utilisateur_id = ? AND recette_id = ?'
    );
    $stmt->execute([$userId, $recetteId]);
}

/** Ajoute ou retire un favori, et retourne 'added' ou 'removed'. */
function basculerFavori($pdo, $userId, $recetteId)
{
    if (estDejaFavori($pdo, $userId, $recetteId)) {
        retirerFavori($pdo, $userId, $recetteId);
        return 'removed';
    }
    ajouterFavori($pdo, $userId, $recetteId);
    return 'added';
}

/** Toutes les recettes favorites d'un utilisateur. */
function getFavorisUtilisateur($pdo, $userId)
{
    $sql = "SELECT r.*, c.nom AS cat_nom, c.icone AS cat_icone, c.slug AS cat_slug,
                   COALESCE(AVG(n.valeur), 0) AS note_moy,
                   (r.temps_preparation + r.temps_cuisson) AS duree_totale
            FROM favoris f
            JOIN recettes    r ON r.id = f.recette_id
            JOIN categories  c ON c.id = r.categorie_id
            LEFT JOIN notes  n ON n.recette_id = r.id
            WHERE f.utilisateur_id = ?
            GROUP BY r.id
            ORDER BY f.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/** Nombre de favoris d'un utilisateur. */
function compterFavorisUtilisateur($pdo, $userId)
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/* ── Table NOTES ──────────────────────────────────────────── */

/** Moyenne et nombre de notes pour une recette. */
function getMoyenneNote($pdo, $recetteId)
{
    $stmt = $pdo->prepare(
        'SELECT AVG(valeur) AS moy, COUNT(*) AS nb FROM notes WHERE recette_id = ?'
    );
    $stmt->execute([$recetteId]);
    return $stmt->fetch();
}

/** Note donnée par un utilisateur pour une recette, ou null. */
function getNoteUtilisateur($pdo, $userId, $recetteId)
{
    $stmt = $pdo->prepare(
        'SELECT valeur FROM notes WHERE recette_id = ? AND utilisateur_id = ?'
    );
    $stmt->execute([$recetteId, $userId]);
    $val = $stmt->fetchColumn();
    return $val !== false ? (int) $val : null;
}

/** Ajoute ou met à jour la note d'un utilisateur pour une recette. */
function enregistrerNote($pdo, $userId, $recetteId, $valeur)
{
    $sql = 'INSERT INTO notes (utilisateur_id, recette_id, valeur) VALUES (?,?,?)
            ON DUPLICATE KEY UPDATE valeur = VALUES(valeur)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $recetteId, $valeur]);
}
