# Mission 3 — Envoi d'e-mail réel via SMTP (PHPMailer)

## Objectif
À l'inscription d'un nouvel utilisateur, lui envoyer un **vrai e-mail de bienvenue**
via un serveur SMTP, au lieu de simplement le journaliser dans un fichier.

## Ce qui a été mis en place (DÉJÀ FAIT dans le projet)

| Élément | Détail |
|---|---|
| Composer | Installé localement (`composer.phar`) — gestionnaire de dépendances PHP |
| PHPMailer | `composer require phpmailer/phpmailer` → version 7.1, dans `vendor/` |
| `email.php` | Réécrit : envoi SMTP réel via PHPMailer, avec repli automatique sur le log |
| `.env.example` | Modèle de configuration SMTP (versionné) |
| `.env` | Fichier de secrets réels (NON versionné — voir `.gitignore`) |
| `.gitignore` | Exclut `/vendor/`, `composer.phar`, `.env` et le log |

## Architecture du code (email.php)

1. `chargerEnv()` — lit le fichier `.env` (clé=valeur) et met le résultat en cache.
   Les identifiants SMTP ne sont **jamais en dur** dans le code → bonne pratique
   de gestion des secrets.
2. `envoyerMailBienvenue($email, $pseudo)` — construit le mail HTML. Si PHPMailer
   est installé **et** que `.env` contient une config SMTP → envoi réel ;
   sinon → repli sur `enregistrerMailDansLog()`. Le site ne plante donc jamais.
3. `envoyerViaSmtp(...)` — configure PHPMailer (host, port, auth, STARTTLS),
   définit expéditeur/destinataire, envoie, et garde une trace dans le log.

## Ce qu'il te reste à faire (≈ 5 min) pour la démo au jury

1. Crée un compte gratuit sur https://mailtrap.io
   → *Email Testing > Inboxes > My Inbox > Integrations > PHP > PHPMailer*
2. Copie le modèle et remplis-le avec TES identifiants Mailtrap :
   ```
   copy .env.example .env
   ```
   Puis édite `.env` (MAIL_USERNAME, MAIL_PASSWORD).
3. Inscris un nouvel utilisateur sur le site → l'e-mail apparaît dans ta boîte
   Mailtrap. **Fais une capture d'écran** : c'est la preuve attendue.

## Points à dire à l'oral
- **SMTP** = protocole standard d'envoi d'e-mails, plus fiable que `mail()`.
- **Mailtrap** = boîte de test qui capture les mails en dev (pas de vrai envoi,
  aucun risque de spammer de vraies adresses).
- **`.env`** = les secrets ne sont jamais dans le code ni sur Git (`.gitignore`).
  On versionne seulement `.env.example` pour documenter les variables attendues.
- **Composer** = gestionnaire de dépendances PHP ; PHPMailer est une dépendance
  réinstallable par `composer install` (d'où `/vendor/` ignoré par Git).
- En production, on remplacerait juste les identifiants `.env` par ceux d'un vrai
  service (Gmail, SendGrid, Mailgun…).
