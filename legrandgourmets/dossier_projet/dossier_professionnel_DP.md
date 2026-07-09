# Dossier Professionnel (DP) — Alexandre BAPECK
## Titre Professionnel Développeur Web et Web Mobile — RNCP Niveau 5

---

# ACTIVITÉ-TYPE 1 : Développer la partie front-end d'une application web ou web mobile sécurisée

## Exemple 1 — Intégration responsive de la page d'accueil LE GRAND GOURMET

**Contexte :** Projet fil rouge de formation, site de recettes culinaires.

**Tâches réalisées :**
- Maquettage mobile-first sur Figma (wireframes, moodboard, charte graphique)
- Intégration HTML5 sémantique (header, nav, main, section, footer)
- CSS3 avec variables custom (--terra, --brun, --creme), Flexbox et CSS Grid
- Media queries pour 3 breakpoints : mobile (<768px), tablette, desktop
- Menu burger en JavaScript pour la navigation mobile
- Animations CSS (hover sur cartes, transitions)

**Compétences validées :**
- Maquetter une application ✅
- Réaliser une interface utilisateur web statique et adaptable ✅
- Développer une interface utilisateur web dynamique ✅

**Extrait de code clé :**
```css
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 22px;
}
@media (max-width: 768px) {
  .nav-links { display: none; flex-direction: column; }
  .nav-links.open { display: flex; }
  .nav-burger { display: flex; }
}
```

---

## Exemple 2 — Système de favoris en AJAX (interface dynamique)

**Contexte :** Permettre aux membres d'ajouter/retirer des favoris sans recharger la page.

**Tâches réalisées :**
- Développement JavaScript avec l'API Fetch (promesses, asynchrone)
- Communication avec le back-end via JSON
- Mise à jour du DOM en temps réel (icône cœur 🤍 → ❤️)
- Gestion des erreurs (utilisateur non connecté)

**Compétences validées :**
- Développer une interface utilisateur web dynamique ✅

**Extrait de code clé :**
```javascript
function toggleFavori(e, id) {
  e.preventDefault();
  fetch('/api/favori.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({recette_id: id})
  })
  .then(r => r.json())
  .then(data => {
    const btn = e.currentTarget;
    btn.textContent = data.favori ? '❤️' : '🤍';
  });
}
```

---

## Exemple 3 — Interface web du Festival "Mars IA"

**Contexte :** Conception d'une interface web événementielle pour un festival dédié à l'intelligence artificielle.

**Tâches réalisées :**
- Réflexion UX/UI : parcours utilisateur, arborescence des pages
- Wireframes et mockups sur Figma
- Travail sur l'identité visuelle (couleurs, typographies)
- Intégration HTML/CSS responsive de la structure du site

**Compétences validées :**
- Maquetter une application ✅
- Réaliser une interface utilisateur web statique et adaptable ✅

---

# ACTIVITÉ-TYPE 2 : Développer la partie back-end d'une application web ou web mobile sécurisée

## Exemple 1 — Authentification sécurisée et gestion des rôles (LE GRAND GOURMET)

**Contexte :** Système d'inscription/connexion avec deux rôles : utilisateur et administrateur.

**Tâches réalisées :**
- Formulaire d'inscription avec validation côté serveur (email unique, mdp 8 caractères min)
- Hash des mots de passe avec `password_hash()` (algorithme bcrypt)
- Connexion avec `password_verify()` et `session_regenerate_id()` (anti session fixation)
- Helpers de contrôle d'accès : `requireLogin()`, `requireAdmin()`
- Redirection selon le rôle après connexion

**Compétences validées :**
- Mettre en place une base de données relationnelle ✅
- Développer des composants d'accès aux données SQL et NoSQL ✅
- Développer des composants métier côté serveur ✅

**Extrait de code clé :**
```php
// Hash à l'inscription
$hash = password_hash($mdp, PASSWORD_BCRYPT);

// Vérification à la connexion
if ($user && password_verify($mdp, $user['mot_de_passe'])) {
    session_regenerate_id(true);  // Anti session fixation
    $_SESSION['utilisateur_id']   = $user['id'];
    $_SESSION['utilisateur_role'] = $user['role'];
}
```

---

## Exemple 2 — CRUD complet du back-office avec upload de fichiers

**Contexte :** Interface d'administration pour gérer les recettes du site.

**Tâches réalisées :**
- Conception BDD : MCD, MLD, MPD — 8 tables relationnelles avec clés étrangères
- Requêtes préparées PDO pour toutes les opérations (anti injection SQL)
- CRUD : Create, Read, Update, Delete sur les recettes et catégories
- Upload d'images sécurisé : vérification extension, taille (3 Mo max), nom unique (uniqid)
- Gestion de la table pivot recettes_ingredients (relation N-N)
- Protection XSS avec `htmlspecialchars()` sur tous les affichages

**Compétences validées :**
- Mettre en place une base de données relationnelle ✅
- Développer des composants d'accès aux données ✅
- Développer des composants métier côté serveur ✅
- Documenter le déploiement d'une application dynamique web ✅

**Extrait de code clé :**
```php
// Requête préparée contre l'injection SQL
$stmt = $pdo->prepare("
    INSERT INTO recettes (titre, description, temps_preparation,
    temps_cuisson, difficulte, portions, categorie_id, image)
    VALUES (?,?,?,?,?,?,?,?)
");
$stmt->execute([$titre, $desc, $tprep, $tcuiss, $diff, $portions, $cat_id, $image_nom]);

// Upload sécurisé
$ext_ok = ['jpg','jpeg','png','webp'];
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $ext_ok)) { $erreurs[] = 'Format non supporté'; }
$image_nom = uniqid('rec_') . '.' . $ext;
```

---

## Exemple 3 — API REST de fichiers vidéo (Node.js / Express)

**Contexte :** Création d'une API listant des fichiers vidéo, exposée en JSON.

**Tâches réalisées :**
- Mise en place d'un serveur Express avec architecture MVC simple
- Gestion des routes API (GET /videos, GET /videos/:id)
- Lecture du système de fichiers et exposition en JSON
- Accès aux fichiers vidéo via URL
- Tests des endpoints avec Postman

**Compétences validées :**
- Développer des composants métier côté serveur ✅
- Développer des composants d'accès aux données ✅

**Extrait de code clé :**
```javascript
const express = require('express');
const app = express();

// Route API : liste des vidéos en JSON
app.get('/api/videos', (req, res) => {
  const videos = lireDossierVideos();   // modèle
  res.json(videos);                      // réponse JSON
});

// Accès au fichier via URL
app.use('/videos', express.static('public/videos'));

app.listen(3000);
```

---

# Récapitulatif des compétences

| Compétence du référentiel | Exemples |
|---|---|
| Maquetter une application | AT1-Ex1, AT1-Ex3 |
| Réaliser une interface web statique et adaptable | AT1-Ex1, AT1-Ex3 |
| Développer une interface web dynamique | AT1-Ex1, AT1-Ex2 |
| Mettre en place une BDD relationnelle | AT2-Ex1, AT2-Ex2 |
| Développer des composants d'accès aux données | AT2-Ex1, AT2-Ex2, AT2-Ex3 |
| Développer des composants métier côté serveur | AT2-Ex1, AT2-Ex2, AT2-Ex3 |
