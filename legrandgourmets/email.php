<?php
/**
 * Module d'envoi d'e-mails (Mission 3).
 *
 * Envoi RÉEL via SMTP grâce à la librairie PHPMailer (installée avec Composer).
 * Les identifiants SMTP ne sont JAMAIS écrits en dur dans le code : ils sont
 * lus dans le fichier .env (non versionné), via la fonction chargerEnv().
 *
 * Repli (fallback) : si le .env n'est pas configuré ou si PHPMailer n'est pas
 * installé, le mail est simplement journalisé dans assets/emails/log.txt.
 * Le site continue donc de fonctionner même sans configuration SMTP.
 */

// Autoloader de Composer (donne accès à PHPMailer)
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Lit le fichier .env (au format CLE=valeur) et renvoie un tableau associatif.
 * Le résultat est mis en cache pour ne lire le fichier qu'une seule fois.
 */
function chargerEnv()
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $config  = [];
    $fichier = __DIR__ . '/.env';
    if (!is_file($fichier)) {
        return $config;
    }

    // On lit chaque ligne en ignorant les lignes vides et les commentaires (#)
    $lignes = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '' || $ligne[0] === '#') {
            continue;
        }
        if (strpos($ligne, '=') === false) {
            continue;
        }
        list($cle, $valeur) = explode('=', $ligne, 2);
        $valeur = trim($valeur);
        // On retire d'éventuels guillemets autour de la valeur (ex: "LE GRAND GOURMET")
        if (strlen($valeur) >= 2) {
            $premier = $valeur[0];
            $dernier = $valeur[strlen($valeur) - 1];
            if (($premier === '"' && $dernier === '"') || ($premier === "'" && $dernier === "'")) {
                $valeur = substr($valeur, 1, -1);
            }
        }
        $config[trim($cle)] = $valeur;
    }

    return $config;
}

/**
 * Envoie le mail de bienvenue à un nouvel inscrit.
 * Retourne true si l'envoi (ou la journalisation de repli) a fonctionné.
 */
function envoyerMailBienvenue($email, $pseudo)
{
    $sujet = 'Bienvenue sur LE GRAND GOURMET !';

    // Construction du contenu du mail (en HTML)
    $contenu  = "<h1>Bienvenue, " . htmlspecialchars($pseudo) . " !</h1>";
    $contenu .= "<p>Votre inscription sur <strong>LE GRAND GOURMET</strong> a bien été prise en compte.</p>";
    $contenu .= "<p>Vous pouvez maintenant :</p>";
    $contenu .= "<ul>";
    $contenu .= "<li>🍽️ Consulter et noter les recettes</li>";
    $contenu .= "<li>❤️ Sauvegarder vos favorites</li>";
    $contenu .= "<li>✍️ Publier vos propres recettes</li>";
    $contenu .= "</ul>";
    $contenu .= "<p>À très vite en cuisine !<br>L'équipe LE GRAND GOURMET 👨‍🍳</p>";

    $env = chargerEnv();

    // Si PHPMailer est installé ET que le .env contient une config SMTP,
    // on envoie réellement le mail. Sinon, on bascule sur la journalisation.
    if (class_exists(PHPMailer::class) && !empty($env['MAIL_HOST'])) {
        return envoyerViaSmtp($env, $email, $pseudo, $sujet, $contenu);
    }

    enregistrerMailDansLog($email, $sujet, $contenu);
    return true;
}

/**
 * Envoie effectivement le mail via le serveur SMTP configuré dans le .env.
 * Retourne true en cas de succès, false (+ log) en cas d'échec.
 */
function envoyerViaSmtp(array $env, $email, $pseudo, $sujet, $contenu)
{
    $mail = new PHPMailer(true); // true = active les exceptions

    try {
        // --- Configuration du serveur SMTP (depuis le .env) ---
        $mail->isSMTP();
        $mail->Host       = $env['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = isset($env['MAIL_USERNAME']) ? $env['MAIL_USERNAME'] : '';
        $mail->Password   = isset($env['MAIL_PASSWORD']) ? $env['MAIL_PASSWORD'] : '';
        $mail->Port       = isset($env['MAIL_PORT']) ? (int) $env['MAIL_PORT'] : 2525;
        $mail->CharSet    = 'UTF-8';

        // Chiffrement : 'tls' si demandé, sinon STARTTLS automatique
        if (!empty($env['MAIL_ENCRYPTION']) && $env['MAIL_ENCRYPTION'] === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // --- Expéditeur et destinataire ---
        $fromEmail = !empty($env['MAIL_FROM_ADDRESS']) ? $env['MAIL_FROM_ADDRESS'] : 'noreply@legrandgourmet.fr';
        $fromName  = !empty($env['MAIL_FROM_NAME'])    ? $env['MAIL_FROM_NAME']    : 'LE GRAND GOURMET';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email, $pseudo);

        // --- Contenu ---
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $contenu;
        $mail->AltBody = strip_tags($contenu); // version texte pour les clients sans HTML

        $mail->send();

        // On garde aussi une trace dans le log (pratique pour la démo au jury)
        enregistrerMailDansLog($email, $sujet, $contenu);
        return true;

    } catch (Exception $e) {
        // En cas d'échec SMTP, on journalise au lieu de planter l'inscription
        error_log('Echec envoi mail : ' . $mail->ErrorInfo);
        enregistrerMailDansLog($email, $sujet, $contenu);
        return false;
    }
}

/**
 * Enregistre un mail dans le fichier de log assets/emails/log.txt.
 * Utile pour la démonstration et le debug en développement.
 */
function enregistrerMailDansLog($destinataire, $sujet, $contenu)
{
    $dossier = __DIR__ . '/assets/emails';
    if (!is_dir($dossier)) {
        mkdir($dossier, 0755, true);
    }

    $separateur = str_repeat('=', 60) . "\n";
    $log  = $separateur;
    $log .= "MAIL ENVOYÉ — " . date('d/m/Y H:i:s') . "\n";
    $log .= $separateur;
    $log .= "À      : $destinataire\n";
    $log .= "Sujet  : $sujet\n";
    $log .= "\n--- Contenu HTML ---\n";
    $log .= $contenu . "\n\n";

    file_put_contents($dossier . '/log.txt', $log, FILE_APPEND);
}
