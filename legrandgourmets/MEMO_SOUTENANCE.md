# 🎯 MÉMO SOUTENANCE — LE GRAND GOURMET

> **À lire la veille de la soutenance.** Réponds avec tes propres mots, courts et clairs.
> Si tu bloques sur une question : "Je vous propose de vous montrer dans le code" + ouvrir le bon fichier.

---

## 1. PRÉSENTATION DU PROJET (à apprendre par cœur)

> **« LE GRAND GOURMET est un site web de partage de recettes de cuisine.
> Les visiteurs peuvent consulter et chercher des recettes par catégorie ou difficulté.
> Les utilisateurs inscrits peuvent noter, commenter, sauvegarder en favoris et publier leurs propres recettes.
> Les administrateurs gèrent l'ensemble depuis un back-office.
> Le site est développé en PHP procédural avec une base MySQL, sans framework. »**

**Durée : 30 secondes.** Si on te demande plus, tu enchaînes sur les fonctionnalités, puis la structure.

---

## 2. STRUCTURE DU PROJET (apprendre les noms)

Tout est dans **un seul dossier** (`legrandgourmet/`), pas de sous-dossiers complexes :

| Fichier / Dossier | Rôle |
|---|---|
| `index.php` | Page d'accueil |
| `recettes.php` | Liste des recettes avec filtres et pagination |
| `recette.php` | Détail d'une recette (note, favori, commentaire, suppression) |
| `top-recettes.php` | Classement des recettes les mieux notées |
| `aleatoire.php` | Redirige vers une recette tirée au hasard |
| `connexion.php` / `inscription.php` / `deconnexion.php` | Authentification |
| `profil.php` | Page profil utilisateur |
| `favoris.php` | Recettes sauvegardées |
| `ajouter-recette.php` | Publier une recette (utilisateurs connectés) |
| `404.php` | Page d'erreur |
| `header.php` / `footer.php` / `navbar.php` | Parties HTML communes |
| `database.php` | Connexion à la base (variable `$pdo`) |
| `fonctions.php` | **TOUTES** les fonctions du site (utilitaires, session, requêtes BDD) |
| `admin/` | 5 pages d'administration |
| `ajax/favori.php` | Ajout/retrait favori en arrière-plan (sans recharger la page) |
| `assets/` | CSS, JS, images, uploads des utilisateurs |
| `database/` | Scripts SQL pour créer la base et les données |

---

## 3. BASE DE DONNÉES (8 tables)

Mémorise les **noms des tables** :

1. **`utilisateurs`** — pseudo, email, mot_de_passe (haché), role (user/admin)
2. **`categories`** — Entrées, Plats, Desserts, etc.
3. **`recettes`** — titre, slug, description, instructions, temps, difficulté, image
4. **`ingredients`** — liste des ingrédients (sans doublon)
5. **`recette_ingredient`** — table pivot : quel ingrédient dans quelle recette + quantité
6. **`commentaires`** — avis des utilisateurs sur les recettes
7. **`favoris`** — recettes sauvegardées par les utilisateurs
8. **`notes`** — notation 1-5 étoiles

**Relations à connaître :**
- 1 recette appartient à 1 catégorie et a 1 auteur (clé étrangère)
- 1 recette a plusieurs ingrédients (table pivot `recette_ingredient`)
- 1 utilisateur peut avoir plusieurs favoris, plusieurs commentaires, plusieurs notes

---

## 4. CONCEPTS CLÉS (à comprendre)

### 🔐 Mot de passe — `password_hash` et `password_verify`
- **Pourquoi :** on ne stocke JAMAIS un mot de passe en clair. Si la base est volée, les mots de passe restent illisibles.
- **Comment :** `password_hash($mdp, PASSWORD_BCRYPT)` crée un hash, `password_verify($mdp, $hash)` vérifie. Voir `inscription.php` et `connexion.php`.

### 🛡️ Injection SQL — Requêtes préparées avec PDO
- **Le danger :** si on écrit `"SELECT * WHERE email = '$email'"`, un pirate peut taper `' OR 1=1 --` pour tout voir.
- **La solution :** on utilise `$pdo->prepare()` avec des `?`, puis `$stmt->execute([$email])`. PDO échappe automatiquement.
- **Où :** dans toutes les fonctions de `fonctions.php`.

### 🛡️ XSS (Cross-Site Scripting) — `htmlspecialchars` (fonction `e()`)
- **Le danger :** un utilisateur tape `<script>alert('hack')</script>` dans un commentaire. Sans protection, le navigateur exécute.
- **La solution :** ma fonction `e()` transforme `<` en `&lt;`. Voir `fonctions.php`. On l'utilise partout : `<?= e($r['titre']) ?>`.

### 🛡️ CSRF (Cross-Site Request Forgery) — Jeton anti-faux-formulaire
- **Le danger :** un autre site fait soumettre un formulaire à ma place (ex : supprimer mon compte sans que je le sache).
- **La solution :** chaque formulaire contient un jeton aléatoire (`csrfField()`). Le serveur vérifie qu'il correspond à celui en session (`verifyCsrfToken()`).

### 🔗 Slug
- **C'est :** un identifiant lisible dans l'URL, ex : `"Pâtes Carbonara"` devient `"pates-carbonara"`.
- **Avantages :** URL plus jolie et meilleur SEO.
- **Comment :** fonction `slugify()` enlève les accents, met en minuscule, remplace les espaces par `-`.

### 📦 Session
- **C'est :** des variables stockées sur le serveur, liées à un cookie envoyé au navigateur (`PHPSESSID`).
- **Usage :** `$_SESSION['user_id'] = 5;` pour mémoriser l'utilisateur connecté entre les pages.
- **Voir :** `connexion.php`, fonctions `isLoggedIn()`, `currentUser()`, `requireLogin()`.

### 🗄️ PDO (PHP Data Objects)
- **C'est :** la bibliothèque PHP moderne pour parler à la base.
- **Pourquoi PDO et pas mysqli :** PDO supporte plusieurs SGBD (MySQL, PostgreSQL, SQLite...). Plus standard.
- **Sécurité :** requêtes préparées par défaut.

### 📤 Upload d'image
- **Étapes :** récupérer le fichier (`$_FILES`), vérifier extension et type MIME, créer nom unique avec `uniqid()`, déplacer avec `move_uploaded_file()`.
- **Sécurité :** limite à 5 Mo, types autorisés (jpg/png/webp/gif), vrai type MIME vérifié avec `finfo`.
- **Voir :** `ajouter-recette.php` et `admin/recettes.php`.

---

## 5. QUESTIONS PROBABLES DU JURY (avec réponses)

### Q1. "Présente ton projet en 1 minute."
👉 *Utiliser le pitch du §1 ci-dessus.*

### Q2. "Pourquoi PHP ?"
> « PHP est le langage le plus utilisé pour les sites web côté serveur, simple à apprendre, gratuit, et compatible avec XAMPP/MySQL en local. C'est ce qui est enseigné dans ma formation. »

### Q3. "Pourquoi tu n'as pas utilisé un framework comme Laravel ou Symfony ?"
> « Je voulais comprendre les bases du PHP avant d'utiliser un framework. Un framework cache beaucoup de mécanismes ; là je maîtrise tout ce que mon code fait. »

### Q4. "Tu n'as pas utilisé la programmation orientée objet, pourquoi ?"
> « J'ai fait le choix d'une approche procédurale, plus simple à comprendre et à présenter, adaptée au niveau Bac+2. Les fonctions sont organisées par table dans `fonctions.php`. »

### Q5. "Comment tu te protèges des injections SQL ?"
> « J'utilise PDO avec des requêtes préparées : les paramètres sont passés séparément avec des `?`, jamais concaténés dans la requête. PDO échappe tout automatiquement. »
→ *Ouvrir une fonction comme `getRecetteBySlug` pour montrer.*

### Q6. "Comment sont stockés les mots de passe ?"
> « Avec `password_hash` et l'algorithme BCRYPT. Le hash est différent à chaque fois (sel automatique). Pour vérifier à la connexion, j'utilise `password_verify`. »
→ *Ouvrir `connexion.php` ligne ~25.*

### Q7. "C'est quoi le token CSRF ?"
> « Un jeton aléatoire généré au début de la session et inclus dans chaque formulaire. Au moment de la soumission, le serveur vérifie que le jeton correspond, sinon il refuse. Ça empêche qu'un autre site fasse soumettre un formulaire à ma place. »

### Q8. "Explique-nous le schéma de la base."
👉 *Décrire les 8 tables, insister sur les clés étrangères (catégorie, auteur) et la table pivot `recette_ingredient`.*

### Q9. "Comment tu gères la session utilisateur ?"
> « Quand l'utilisateur se connecte, je stocke son id, son pseudo et son rôle dans `$_SESSION`. À chaque page, j'appelle `isLoggedIn()` ou `requireLogin()` pour vérifier. À la déconnexion, je détruis la session. »

### Q10. "Quelle différence entre un user et un admin ?"
> « Le rôle est stocké dans la table `utilisateurs` (`ENUM('user', 'admin')`). La fonction `isAdmin()` vérifie la session. Les pages du dossier `admin/` appellent `requireAdmin()` en début de fichier. »

### Q11. "C'est quoi un slug ?"
> « C'est la version lisible du titre dans l'URL. Au lieu de `recette.php?id=42`, j'ai `recette.php?slug=tarte-aux-fraises`. C'est plus lisible et meilleur pour le référencement. »

### Q12. "Comment marche la pagination ?"
> « Je calcule `OFFSET = (page - 1) × 9` et je passe `LIMIT 9 OFFSET ?` à la requête SQL. Une autre fonction `countRecettes()` compte le total pour savoir combien de pages afficher. Voir `recettes.php`. »

### Q13. "Comment fonctionne le système de favoris ?"
> « Une table `favoris` avec deux colonnes : `utilisateur_id` et `recette_id`. La clé primaire est composée des deux, pour éviter les doublons. Le bouton ❤ envoie une requête AJAX à `ajax/favori.php` qui appelle `basculerFavori()` : si la recette est dedans on l'enlève, sinon on l'ajoute. »

### Q14. "Comment tu gères l'upload d'images ?"
> « Le formulaire a `enctype="multipart/form-data"`. PHP reçoit le fichier dans `$_FILES`. Je vérifie : extension autorisée, vrai type MIME avec `finfo`, taille max 5 Mo. Je génère un nom unique avec `slugify(titre) + uniqid()`. Je déplace avec `move_uploaded_file()` dans `assets/uploads/`. »

### Q15. "Comment tu te protèges du XSS ?"
> « J'utilise ma fonction `e()` qui appelle `htmlspecialchars` à chaque affichage de données utilisateur. Ça transforme les balises HTML en texte. Par exemple, `<script>` devient `&lt;script&gt;`. »

### Q16. "Tu as utilisé l'architecture MVC ?"
> « Non, j'ai fait du PHP procédural classique : chaque page contient son traitement et son affichage. C'était plus simple à mon niveau. Le MVC sera une amélioration possible avec un framework. »

### Q17. "Pourquoi un seul gros fichier `fonctions.php` ?"
> « Pour avoir tout au même endroit et n'avoir qu'un `require_once` par page. Il est organisé en sections : utilitaires, session, et fonctions de base de données regroupées par table. »

### Q18. "Comment tu gères les erreurs ?"
> « PDO est configuré en mode `ERRMODE_EXCEPTION` : toute erreur SQL lève une exception. Pour la connexion, j'ai un `try/catch` qui arrête le script avec un message clair. Les pages 404 sont gérées par `.htaccess` qui redirige vers `404.php`. »

### Q19. "Quelles ont été les difficultés du projet ?"
👉 *Réponse honnête personnelle. Exemples :*
> « La gestion de l'upload d'images au début, parce qu'il faut faire attention à la sécurité (vrai type MIME, taille, extension). Aussi la pagination, parce qu'il faut compter le total ET récupérer la bonne tranche. »

### Q20. "Quelles améliorations tu envisages ?"
> « Ajouter un système d'envoi de mail de bienvenue, une recherche d'ingrédients plus avancée, un mode sombre, ou passer à un framework comme Symfony pour mieux structurer. »

### Q21. "Montre-nous comment marche la connexion."
👉 *Ouvrir `connexion.php`. Expliquer :*
1. Si déjà connecté → redirection accueil
2. Si POST → récupère email+mdp, cherche l'user en base
3. `password_verify` compare le mdp tapé au hash en base
4. Si OK → on enregistre l'user en session + `session_regenerate_id` (anti vol de session)
5. Redirection : admin → tableau de bord, user → accueil

### Q22. "Et si je supprime une catégorie qui a des recettes ?"
> « Impossible. Dans `admin/categories.php`, je vérifie avec `compterRecettesCategorie()`. Si > 0, j'affiche un message d'erreur. Sinon je supprime. C'est une protection métier. »

### Q23. "Comment tu fais pour qu'un utilisateur ne note qu'une fois une recette ?"
> « La table `notes` a une contrainte `UNIQUE(utilisateur_id, recette_id)`. La requête utilise `INSERT ... ON DUPLICATE KEY UPDATE` : si la note existe déjà, elle est mise à jour, sinon créée. »

### Q24. "Comment se passe l'inscription ?"
👉 *Ouvrir `inscription.php`. Étapes :*
1. Validation : pseudo ≥ 3, email valide, mdp ≥ 8, confirmation identique
2. Vérification que pseudo/email pas déjà pris
3. `password_hash` du mdp
4. Insertion en base
5. Connexion automatique (session) + redirection accueil

### Q25. "Comment tu gères les routes / URLs ?"
> « Chaque URL correspond directement à un fichier `.php`. Pas de routeur. Le `.htaccess` permet aussi d'écrire l'URL sans le `.php` (ex : `/recettes` marche comme `/recettes.php`). »

---

## 6. PHRASES DE SECOURS (si tu bloques)

- 🆘 **« C'est une bonne question, je vous propose de regarder directement dans le code. »** → tu ouvres le fichier concerné
- 🆘 **« Je ne suis pas sûr·e à 100%, je préfère vérifier que vous donner une réponse fausse. »** → tu cherches dans `fonctions.php`
- 🆘 **« C'est une amélioration que je n'ai pas eu le temps d'implémenter. »** → si on te demande une fonctionnalité absente
- 🆘 **« Je peux vous le faire en direct ? »** → propose de coder un petit truc, ça impressionne

---

## 7. DÉMO RECOMMANDÉE (ordre de présentation)

1. **Page d'accueil** → recette du jour, catégories, dernières recettes
2. **Liste recettes** → utiliser les filtres et la recherche
3. **Détail recette** → montrer la note, ajouter aux favoris, écrire un commentaire
4. **Inscription / connexion** → créer un compte test
5. **Publier une recette** (page `ajouter-recette.php`) → uploader une image
6. **Profil + favoris** → montrer la cohérence
7. **Back-office admin** → tableau de bord, modifier une recette, supprimer un commentaire
8. **Code** → ouvrir `fonctions.php` (présenter les sections), `recette.php` (logique + HTML)

---

## 8. CHIFFRES À CONNAÎTRE

- **8** tables dans la base
- **8** catégories de recettes (Entrées, Plats, Desserts, Petit-déj, Salades, Soupes...)
- **~15** recettes pré-insérées dans le script SQL
- **~50** fonctions dans `fonctions.php`
- **Environ 20** fichiers PHP au total

---

## 9. TECHNOS UTILISÉES (à citer)

- **Backend :** PHP 8, MySQL, PDO
- **Frontend :** HTML5, CSS3 (Flexbox + Grid), JavaScript (Fetch API, Intersection Observer)
- **Serveur local :** XAMPP (Apache + MySQL)
- **Versioning :** Git
- **Sécurité :** password_hash/verify, requêtes préparées, htmlspecialchars, jeton CSRF, vérification MIME
- **UX :** responsive, animations, AJAX (favoris), upload local + URL externes

---

## ✅ DERNIÈRE CHECKLIST AVANT LE JOUR J

- [ ] J'ai relu ce mémo une fois
- [ ] J'ai testé que mon site marche en local (XAMPP démarré)
- [ ] J'ai un compte admin de prêt (`admin@legrandgourmet.fr` / mdp à connaître)
- [ ] J'ai un compte user de prêt
- [ ] Je sais où sont mes fichiers (ouvrir VS Code à l'avance)
- [ ] J'ai répété ma présentation à voix haute au moins une fois
- [ ] Je respire avant de répondre 🧘

**Tu connais ton projet mieux que personne. Sois calme, parle lentement, et n'hésite pas à dire "je ne sais pas" plutôt que d'inventer.** 💪
