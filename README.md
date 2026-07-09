# 🎓 Dossiers professionnels DWWM — Alexandre Bapeck

Dépôt de certification pour le **Titre Professionnel Développeur Web et Web Mobile** (RNCP niveau 5 — Bac +2), La Plateforme, Cannes — session 2025-2026.

Il contient les **documents remis au jury** ainsi que le **code source du projet fil rouge** qu'ils décrivent : *LE GRAND GOURMET*, une application web communautaire de partage de recettes de cuisine.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white) ![Statut](https://img.shields.io/badge/statut-fonctionnel-success)

---

## 📂 Contenu du dépôt

```
.
├── Dossier DWWM/        Les documents PDF remis au jury
└── legrandgourmets/     Le code source de l'application LE GRAND GOURMET
```

### Dossier DWWM/

| Fichier | Contenu |
|---|---|
| `alexandre-bapeck-project.pdf` | **Dossier de projet** (24 p.) — contexte, specs fonctionnelles et techniques, maquettage, conception BDD, les 3 missions, déploiement, sécurité, annexes |
| `alexandre-bapeck-DP.pdf` | **Dossier professionnel** (14 p.) — exemples de pratique professionnelle pour les deux activités-types du titre |
| `alexandre-bapeck-resume.pdf` | **Synthèse** (4 p.) — récapitulatif condensé des deux documents ci-dessus |

### legrandgourmets/

L'application elle-même. Elle dispose de [son propre README](legrandgourmets/README.md) plus détaillé sur les fonctionnalités.

---

## 🍽️ Le projet en bref

Une plateforme où :

- un **visiteur** parcourt, recherche et filtre le catalogue de recettes, sans compte ;
- un **membre inscrit** publie ses recettes, note (1-5 ⭐), commente et sauvegarde en favoris ;
- un **administrateur** gère le catalogue et modère la communauté depuis un back-office.

Le projet est développé en **PHP natif, sans framework**. C'est un choix assumé : il vise à démontrer la compréhension des mécanismes fondamentaux (cycle requête/réponse, sessions, requêtes préparées, sécurité) plutôt qu'à les masquer derrière une couche d'abstraction.

### Trois missions techniques mises en avant

| Mission | Sujet | Compétences illustrées |
|---|---|---|
| **1** | CRUD des recettes en back-office | Routage par action, double validation (front + back), upload sécurisé, gestion des rôles |
| **2** | Favoris en AJAX | Mini-API JSON, `fetch` asynchrone, codes HTTP, mise à jour du DOM |
| **3** | E-mail de bienvenue via SMTP | Composer, librairie tierce (PHPMailer), secrets en `.env` |

---

## 🛠️ Stack technique

| Couche | Technologie |
|---|---|
| Serveur web | Apache 2.4 (XAMPP) |
| Langage serveur | PHP 8.x, procédural |
| Accès aux données | PDO, requêtes préparées (`EMULATE_PREPARES = false`) |
| Base de données | MySQL 8 / MariaDB, moteur InnoDB, `utf8mb4` |
| Front-end | HTML5 sémantique, CSS3 (variables, Flexbox, Grid), JavaScript ES6 vanilla |
| E-mail | PHPMailer 7 via SMTP |
| Dépendances | Composer |
| Versioning | Git / GitHub |

---

## 🚀 Installation locale

### Prérequis

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8)
- [Composer](https://getcomposer.org/)

### Étapes

1. **Cloner le dépôt** dans le `htdocs` de XAMPP :

   ```bash
   cd c:/xampp/htdocs
   git clone https://github.com/alexandre-bapeck/alexandre-bapeck-alexandre-bapeck-dossiers-pros-DWWM.git legrandgourmet
   ```

2. **Installer les dépendances** (crée `vendor/` avec PHPMailer) :

   ```bash
   cd legrandgourmet/legrandgourmets
   composer install
   ```

3. **Démarrer Apache et MySQL** depuis le panneau de contrôle XAMPP.

4. **Importer la base de données** dans [phpMyAdmin](http://localhost/phpmyadmin), dans cet ordre :

   | # | Script | Rôle |
   |---|---|---|
   | 1 | `database/legrandgourmetbdd.sql` | Crée la base `legrandgourmet`, les 8 tables et un premier jeu de données |
   | 2 | `database/nouvelles_recettes.sql` | Recettes supplémentaires avec photos |
   | 3 | `database/nouvelles_recettes_specifiques.sql` | Recettes complémentaires *(optionnel)* |

5. **Configurer l'envoi d'e-mails** *(optionnel — voir plus bas)* :

   ```bash
   cp .env.example .env
   ```

   puis renseigner les identifiants [Mailtrap](https://mailtrap.io).

6. **Ouvrir le site** dans le navigateur.

### 🔑 Premier compte administrateur

Le script SQL insère un compte `admin@legrandgourmet.fr`, mais **son mot de passe est un placeholder** (`$2y$10$HASH_A_REMPLACER`) : il ne permet pas de se connecter.

Pour obtenir un accès admin :

1. créer un compte normal via la page d'inscription ;
2. dans phpMyAdmin, passer la colonne `role` de ce compte à `admin` dans la table `utilisateurs`.

---

## 🗄️ Base de données — 8 tables

Conception selon la démarche **Merise** (MCD → MLD → MPD).

| Table | Rôle |
|---|---|
| `utilisateurs` | Membres : pseudo, email, mot de passe haché, rôle (`user` / `admin`) |
| `categories` | Classification des recettes |
| `recettes` | Cœur du site : titre, slug, instructions, temps, difficulté, image |
| `ingredients` | Liste des ingrédients, sans doublon |
| `recette_ingredient` | Table pivot recette ↔ ingrédient, porteuse de la quantité |
| `commentaires` | Avis des membres sur les recettes |
| `favoris` | Table pivot utilisateur ↔ recette |
| `notes` | Valeur de 1 à 5, contrainte `UNIQUE(utilisateur_id, recette_id)` |

L'intégrité référentielle est assurée par des clés étrangères : `ON DELETE CASCADE` pour les données dépendantes (commentaires, notes, favoris disparaissent avec la recette), `ON DELETE RESTRICT` pour empêcher la suppression d'une catégorie encore utilisée.

---

## 🔒 Sécurité

| Menace | Parade |
|---|---|
| Injection SQL | Requêtes préparées PDO partout, aucune concaténation |
| XSS | Échappement systématique via la fonction `e()` (`htmlspecialchars`) |
| CSRF | Jeton aléatoire en session, vérifié avec `hash_equals()` sur chaque POST |
| Mots de passe | Hachage bcrypt (`password_hash`), jamais stockés en clair |
| Fixation de session | `session_regenerate_id(true)` à l'authentification |
| Accès non autorisé | `requireLogin()` et `requireAdmin()` en tête de page |
| Upload malveillant | Extension + vrai type MIME (`finfo`) + taille max + nom régénéré |
| Secrets exposés | Identifiants SMTP dans `.env`, exclu du versionnement |

---

## 📧 Envoi d'e-mails

L'inscription déclenche un e-mail de bienvenue (voir [`email.php`](legrandgourmets/email.php)).

- **Si `.env` est configuré** : envoi réel via SMTP avec PHPMailer. En développement, [Mailtrap](https://mailtrap.io) capture les messages dans une boîte de test sans les envoyer pour de vrai.
- **Sinon** : repli automatique — le mail est journalisé dans `assets/emails/log.txt`. Le site fonctionne donc sans aucune configuration SMTP.

---

## 📚 Documentation

| Fichier | Contenu |
|---|---|
| [`legrandgourmets/DOSSIER_PROJET_RNCP.md`](legrandgourmets/DOSSIER_PROJET_RNCP.md) | Première version du dossier de projet |
| [`legrandgourmets/MEMO_SOUTENANCE.md`](legrandgourmets/MEMO_SOUTENANCE.md) | Pitch, concepts clés et 25 questions probables du jury avec réponses |
| [`legrandgourmets/dossier_projet/diagrammes_UML.md`](legrandgourmets/dossier_projet/diagrammes_UML.md) | Sources Mermaid : cas d'utilisation, packages, activité, séquence, MCD/MLD/MPD |
| [`legrandgourmets/dossier_projet/user_stories.md`](legrandgourmets/dossier_projet/user_stories.md) | Les 19 user stories réparties en 4 épopées |
| [`legrandgourmets/dossier_projet/justifications_technos.md`](legrandgourmets/dossier_projet/justifications_technos.md) | Défense des choix techniques |
| [`legrandgourmets/dossier_projet/mission3_email_smtp.md`](legrandgourmets/dossier_projet/mission3_email_smtp.md) | Détail de la mission SMTP |
| [`legrandgourmets/dossier_projet/plan_dossier_et_soutenance.md`](legrandgourmets/dossier_projet/plan_dossier_et_soutenance.md) | Plan du dossier et déroulé de la soutenance |

> Les PDF de `Dossier DWWM/` font foi : les fichiers Markdown ci-dessus sont des documents de travail, antérieurs, dont certains passages ne reflètent plus l'état du code.

---

## 👤 Auteur

**Alexandre Bapeck** — Étudiant Développeur Web et Web Mobile (RNCP niveau 5), La Plateforme — Cannes.

[![GitHub](https://img.shields.io/badge/GitHub-alexandre--bapeck-181717?logo=github)](https://github.com/alexandre-bapeck)
