# 🍽️ LE GRAND GOURMET

Application web de partage de recettes de cuisine — Projet RNCP Bac+2 *Développeur Web et Web Mobile*.

![Statut](https://img.shields.io/badge/statut-fonctionnel-success) ![PHP](https://img.shields.io/badge/PHP-8.x-blue) ![MySQL](https://img.shields.io/badge/MySQL-8.x-orange)

---

## ✨ Fonctionnalités

### 👤 Visiteur
- Consulter et rechercher des recettes
- Filtrer par catégorie, difficulté
- Voir le détail d'une recette (ingrédients, étapes, note moyenne, commentaires)
- Tirer une recette au hasard
- Consulter le classement des meilleures recettes

### 🔐 Utilisateur inscrit
- Créer un compte (avec mail de bienvenue automatique)
- Sauvegarder des recettes en favoris (AJAX)
- Noter une recette (1-5 étoiles)
- Laisser un commentaire
- **Publier sa propre recette** (avec photo)
- **Supprimer sa propre recette**
- Modifier son profil et son mot de passe

### ⚙️ Administrateur
- Tableau de bord avec statistiques
- CRUD complet sur les recettes (avec upload d'image)
- CRUD sur les catégories
- Gestion des utilisateurs et des rôles
- Modération des commentaires

---

## 🛠️ Technologies

| Couche | Outils |
|---|---|
| Back-end | PHP 8 (procédural), PDO MySQL |
| Base de données | MySQL 8 / MariaDB |
| Front-end | HTML5, CSS3 (Flexbox + Grid), JavaScript ES6+ |
| Serveur local | XAMPP (Apache + MySQL) |
| Versioning | Git + GitHub |

---

## 📋 Installation

### Prérequis
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8)
- Un navigateur récent (Chrome, Firefox, Edge)

### Étapes

1. **Cloner le projet** dans le dossier `htdocs` de XAMPP :
   ```bash
   cd c:/xampp/htdocs
   git clone https://github.com/alexandre-bapeck/legrandgourmet.git
   ```

2. **Démarrer XAMPP** : lancer Apache et MySQL depuis le panneau de contrôle.

3. **Créer la base de données** :
   - Ouvrir [phpMyAdmin](http://localhost/phpmyadmin)
   - Importer le fichier `database/legrandgourmetbdd.sql` (création des tables + données de base)
   - Importer ensuite `database/nouvelles_recettes.sql` (ajout de recettes supplémentaires)

4. **Accéder au site** : ouvrir [http://localhost/legrandgourmet/](http://localhost/legrandgourmet/)

---

## 🔑 Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@legrandgourmet.fr` | `admin1234` |
| Utilisateur | `sophie@legrandgourmet.fr` | `sophie1234` |

> ⚠️ Si le mot de passe ne fonctionne pas, c'est que le hash dans le SQL est un placeholder.
> Crée ton compte via [http://localhost/legrandgourmet/inscription](http://localhost/legrandgourmet/inscription) et passe son rôle à `admin` directement dans phpMyAdmin.

---

## 📁 Structure du projet

```
legrandgourmet/
├── *.php                    Pages publiques (index, recettes, recette, connexion...)
├── admin/                   Pages de l'espace administrateur
├── ajax/favori.php          Endpoint AJAX (ajout/retrait favori)
├── assets/                  CSS, JavaScript, images, uploads utilisateurs
├── database/                Scripts SQL (création + données)
├── dossier_projet/          Documentation (UML, user stories, API)
│
├── database.php             Connexion PDO ($pdo)
├── fonctions.php            Toutes les fonctions (utils, session, BDD)
├── email.php                Envoi des mails (bienvenue)
├── header.php / footer.php  Layout commun
├── navbar.php               Barre de navigation
├── admin-sidebar.php        Menu latéral admin
│
├── DOSSIER_PROJET_RNCP.md   Dossier de projet pour le jury
├── MEMO_SOUTENANCE.md       Mémo de préparation à la soutenance
└── README.md                Ce fichier
```

---

## 🔒 Sécurité

- **Mots de passe** : hachés avec `password_hash` (BCRYPT)
- **Injections SQL** : toutes les requêtes utilisent PDO avec des paramètres préparés
- **XSS** : échappement systématique avec `htmlspecialchars()` (fonction `e()`)
- **CSRF** : jeton aléatoire vérifié sur chaque formulaire POST
- **Session** : régénération de l'ID à la connexion (`session_regenerate_id`)
- **Upload** : vérification de l'extension, du vrai type MIME et de la taille (5 Mo max)
- **Rôles** : `requireLogin()` et `requireAdmin()` bloquent les accès non autorisés

---

## 📧 Envoi d'email

L'inscription déclenche un mail de bienvenue (cf. `email.php`).

- **En développement** (XAMPP sans SMTP configuré) : les mails sont enregistrés dans `assets/emails/log.txt` — utile pour démontrer la fonctionnalité au jury
- **En production** : décommenter l'appel à `mail()` dans `email.php` ou utiliser PHPMailer avec un vrai serveur SMTP (Gmail, Mailtrap, etc.)

---

## 📚 Documentation

| Fichier | Contenu |
|---|---|
| `DOSSIER_PROJET_RNCP.md` | Dossier complet (cahier des charges, architecture, BDD, sécurité, tests, bilan) |
| `MEMO_SOUTENANCE.md` | Pitch + questions probables du jury + réponses préparées |
| `dossier_projet/diagrammes_UML.md` | Use cases, packages, activité, séquence, MCD, MLD, MPD |
| `dossier_projet/user_stories.md` | User stories formalisées |
| `dossier_projet/dossier_professionnel_DP.md` | Dossier Pro (AT1 Front + AT2 Back) |
| `dossier_projet/documentation_api.md` | Documentation de l'endpoint AJAX favori |

---

## 👤 Auteur

**Alexandre Bapeck** — Étudiant en formation Développeur Web et Web Mobile (RNCP Bac+2)

[![GitHub](https://img.shields.io/badge/GitHub-alexandre--bapeck-181717?logo=github)](https://github.com/alexandre-bapeck)
