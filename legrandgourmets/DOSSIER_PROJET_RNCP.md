# 📁 DOSSIER DE PROJET —  LE GRAND GOURMET
## Titre RNCP Bac+2 — Développeur Web et Web Mobile

---

## 1. PRÉSENTATION DU PROJET

**Nom du projet :**  LE GRAND GOURMET  
**Type :** Application web de partage de recettes de cuisine  
**Durée de développement :** ~4 semaines  
**Développeur :** Alexandre Bapeck  
**Technologies :** PHP 8, MySQL, HTML5, CSS3, JavaScript (ES6+)

### Description
 LE GRAND GOURMET est une plateforme web permettant aux utilisateurs de consulter, noter et sauvegarder des recettes de cuisine. Un espace d'administration permet de gérer l'intégralité du contenu du site.

---

## 2. CAHIER DES CHARGES

### 2.1 Besoins fonctionnels

#### Côté visiteur
- Consulter les recettes sans inscription
- Rechercher une recette par mot-clé
- Filtrer par catégorie, difficulté, temps de préparation
- Voir le détail d'une recette (ingrédients, étapes, note)
- Accéder à une recette aléatoire

#### Côté utilisateur inscrit
- Créer un compte / Se connecter / Se déconnecter
- Ajouter/retirer des recettes en favoris
- Laisser une note (1 à 5 étoiles) sur une recette
- Publier un commentaire sur une recette
- Modifier son profil (pseudo, email, mot de passe)

#### Côté administrateur
- Ajouter / modifier / supprimer une recette avec ses ingrédients
- Gérer les catégories (CRUD)
- Gérer les utilisateurs (rôles, suppression)
- Modérer les commentaires
- Tableau de bord avec KPIs

### 2.2 Besoins non-fonctionnels
- Site responsive (mobile, tablette, desktop)
- Sécurité : mots de passe hashés bcrypt, requêtes préparées PDO, protection XSS
- Performance : requêtes SQL optimisées, pagination
- Accessibilité : contraste, balises sémantiques

---

## 3. ARCHITECTURE TECHNIQUE

### 3.1 Stack technique
| Couche | Technologie |
|---|---|
| Back-end | PHP 8.x (POO, PDO) |
| Base de données | MySQL 8 / MariaDB |
| Front-end | HTML5, CSS3, JavaScript ES6 |
| Serveur local | XAMPP (Apache + MySQL) |
| Versioning | Git |

### 3.2 Architecture MVC simplifiée
```
 LE GRAND GOURMET/
├── config/          → Connexion BDD, fonctions auth/session
├── auth/            → Pages connexion/inscription/déconnexion
├── admin/           → Back-office administration
├── includes/        → Composants réutilisables (header, footer, navbar)
├── assets/          → CSS, JavaScript, images
├── ajax/            → Endpoints AJAX (favoris)
└── *.php            → Pages publiques (index, recettes, recette, favoris, profil)
```

---

## 4. BASE DE DONNÉES — MCD/MLD

### 4.1 Entités
| Table | Description |
|---|---|
| `utilisateurs` | Membres inscrits (id, pseudo, email, mot_de_passe, role) |
| `recettes` | Cœur du site (titre, description, instructions, temps, difficulté) |
| `categories` | Classification des recettes |
| `ingredients` | Liste des ingrédients |
| `recette_ingredient` | Table pivot recette ↔ ingrédient (avec quantité) |
| `commentaires` | Avis des utilisateurs sur les recettes |
| `favoris` | Association utilisateur ↔ recette préférée |
| `notes` | Note de 1 à 5 par utilisateur et par recette |

### 4.2 Relations clés
- `recettes` → `categories` : N..1 (une recette appartient à une catégorie)
- `recettes` → `utilisateurs` : N..1 (une recette a un auteur)
- `recettes` ↔ `ingredients` : N..N via `recette_ingredient`
- `utilisateurs` ↔ `recettes` (favoris) : N..N via `favoris`
- `utilisateurs` → `commentaires` : 1..N
- `utilisateurs` ↔ `recettes` (notes) : N..N via `notes`

---

## 5. SÉCURITÉ

| Risque | Solution mise en place |
|---|---|
| Injection SQL | Requêtes préparées PDO (`prepare` + `execute`) |
| XSS (Cross-Site Scripting) | `htmlspecialchars()` sur toutes les sorties |
| Mots de passe en clair | Hash bcrypt via `password_hash()` / `password_verify()` |
| Session Fixation | `session_regenerate_id(true)` après connexion |
| Accès non autorisé | Fonctions `requireLogin()` et `requireAdmin()` |
| CSRF | Vérification de la méthode HTTP (POST) |

---

## 6. FONCTIONNALITÉS DÉVELOPPÉES

### 6.1 Authentification
- Inscription avec validation (unicité pseudo/email, longueur mdp)
- Connexion avec vérification bcrypt
- Déconnexion propre (destruction session + cookie)
- Gestion des rôles `user` / `admin`

### 6.2 Pages publiques
- **Accueil** : hero dynamique, recherche, catégories, dernières recettes, CTA
- **Liste recettes** : filtres (catégorie, difficulté, tri), pagination (9/page), recherche
- **Détail recette** : ingrédients, instructions étape par étape, note ⭐, commentaires
- **Top recettes** : classement par note moyenne
- **Recette aléatoire** : redirection vers une recette au hasard

### 6.3 Espace utilisateur
- **Favoris** : ajout/suppression AJAX sans rechargement
- **Profil** : modification des infos personnelles et mot de passe
- **Notation** : système d'étoiles interactif

### 6.4 Administration
- **Dashboard** : 4 KPIs (recettes, users, commentaires, catégories)
- **Recettes** : CRUD complet avec gestion dynamique des ingrédients
- **Catégories** : CRUD avec icône emoji et couleur personnalisée
- **Utilisateurs** : liste, modification rôle, suppression, reset mot de passe
- **Commentaires** : modération (suppression)

---

## 7. DESIGN ET UX

- **Palette** : Noir #0D0D0D · Or #E8B84B · Émeraude #10B981
- **Typographies** : Playfair Display (titres) + Inter (corps)
- **Responsive** : Breakpoints à 900px et 640px
- **Animations** : Page loader, scroll reveal, compteurs, ripple, favoris heartbeat
- **Accessibilité** : `prefers-reduced-motion` respecté, contrastes WCAG

---

## 8. TESTS RÉALISÉS

| Test | Résultat |
|---|---|
| Inscription avec données valides | ✅ |
| Inscription avec email déjà utilisé | ✅ Erreur affichée |
| Connexion avec bon mot de passe | ✅ |
| Connexion avec mauvais mot de passe | ✅ Erreur affichée |
| Ajout/suppression favori (AJAX) | ✅ |
| Notation d'une recette | ✅ |
| Ajout commentaire | ✅ |
| Accès admin sans rôle admin | ✅ Redirigé |
| Ajout recette en admin | ✅ |
| Suppression utilisateur | ✅ (cascade sur favoris/commentaires) |
| Responsive mobile | ✅ |
| Injection SQL tentée | ✅ Bloquée |

---

## 9. AMÉLIORATIONS POSSIBLES

- Upload d'images (actuellement URLs uniquement)
- Système de pagination des commentaires
- Partage sur réseaux sociaux
- Suggestion de recettes similaires
- API REST pour une future application mobile
- Envoi d'email de confirmation à l'inscription

---

## 10. CONCLUSION

Le projet  LE GRAND GOURMET répond à l'ensemble du cahier des charges défini. Il couvre les compétences attendues pour un titre RNCP Bac+2 en développement web :

✅ Conception et modélisation d'une base de données relationnelle  
✅ Développement back-end sécurisé en PHP/PDO  
✅ Intégration front-end responsive (HTML/CSS/JS)  
✅ Gestion des sessions et authentification  
✅ Développement d'un back-office d'administration  
✅ Sécurisation des données (bcrypt, requêtes préparées, XSS)  
✅ Respect des bonnes pratiques (séparation config/routes/vues)

---

*Document rédigé dans le cadre du titre RNCP Bac+2 — Développeur Web et Web Mobile*
