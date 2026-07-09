# Plan du Dossier de Projet (20 pages max) — LE GRAND GOURMET

## Structure recommandée page par page

| Pages | Section | Contenu |
|-------|---------|---------|
| 1 | **Page de garde** | Titre, nom, formation, année, logo LE GRAND GOURMET |
| 2 | **Sommaire** | Table des matières |
| 3 | **Introduction** | Présentation rapide : qui je suis, le projet, pourquoi ce choix |
| 4 | **1. Présentation du projet** | Contexte (projet fil rouge Laplateforme), besoin identifié (plateforme de recettes accessible), objectifs |
| 5 | **2. Gestion de projet** | Méthodologie séquentielle adaptée (étapes : idée → moodboard → maquette → BDD → dev), outil Trello (capture), planning |
| 6 | **3. Spécifications fonctionnelles** | Diagramme de use cases, user stories principales (tableau résumé) |
| 7 | **4. Spécifications techniques** | Stack : PHP 8/PDO, MySQL 8, HTML5/CSS3/JS, XAMPP, Git/GitHub, VS Code, Figma |
| 8 | **5. Maquettage** | Charte graphique (palette terracotta/brun/crème), typographies (Playfair Display + Poppins), maquette mobile |
| 9 | **5. Maquettage (suite)** | Maquette desktop, arborescence des pages, justification UX (mobile-first) |
| 10 | **6. Intégration** | Screenshots responsive (mobile/desktop), extrait media queries, extrait CSS Grid |
| 11 | **7. Conception BDD** | MCD complet (image), explication des cardinalités |
| 12 | **7. Conception BDD (suite)** | MLD, MPD, justification des choix (CASCADE, UNIQUE, CHECK), propriétés ACID |
| 13 | **8. Mise en place BDD** | Extraits SQL : CREATE TABLE recettes, FOREIGN KEY, contraintes |
| 14 | **9. Mission 1 — CRUD Back-office** | Intro, diagramme d'activité, formulaire admin, code contrôleur (extraits en gras), validation front + back |
| 15 | **9. Mission 1 (suite)** | Gestion des rôles (requireAdmin), upload image sécurisé, mini-bilan |
| 16 | **10. Mission 2 — Favoris AJAX** | Intro, diagramme de séquence, code fetch JS, code API PHP, format JSON, test Postman, mini-bilan |
| 17 | **11. Mission 3 — Email SMTP** | Intro, PHPMailer + Composer, configuration SMTP, code envoi, screenshot email reçu, mini-bilan |
| 18 | **12. Sécurité** | Injection SQL (requêtes préparées), XSS (htmlspecialchars), CSRF (token), bcrypt, session_regenerate_id, contrôle d'accès |
| 19 | **13. Déploiement** | Environnements dev/prod, .gitignore (config sensible), GitHub, perspectives (hébergement) |
| 20 | **Conclusion** | Bilan du travail (fonctionnalités livrées), bilan formation, la suite (recherche d'emploi développeur web) |
| 21+ | **Annexes** | Maquettes supplémentaires, diagrammes complémentaires, code additionnel |

---

# Plan de la Présentation Orale (35 min — 15 slides min)

## Découpage temporel recommandé

| Temps | Slides | Contenu |
|-------|--------|---------|
| 0-2 min | 1-2 | **Présentation personnelle** : parcours, reconversion, Bachelor IT + DWWM |
| 2-5 min | 3-4 | **Le projet** : LE GRAND GOURMET, besoin, objectifs |
| 5-8 min | 5-6 | **Gestion de projet** : méthodologie, planning, Trello |
| 8-12 min | 7-9 | **Conception** : maquettes Figma, charte graphique, MCD/MLD |
| 12-18 min | 10-12 | **Mission 1** : CRUD back-office (diagrammes + code) |
| 18-23 min | 13-14 | **Mission 2** : Favoris AJAX (séquence + JSON) |
| 23-27 min | 15-16 | **Mission 3** : Email SMTP (PHPMailer) |
| 27-30 min | 17 | **Sécurité** : SQL injection, XSS, CSRF, bcrypt |
| 30-33 min | — | **DÉMO LIVE** : parcours visiteur → connexion → favori → admin |
| 33-35 min | 18 | **Conclusion** : bilan, perspectives, remerciements |

## Scénario de démo (à préparer)

1. **Accueil** → montrer le responsive (réduire la fenêtre)
2. **Recherche** "carbonara" → filtrer par difficulté
3. **Détail recette** → ingrédients, note moyenne
4. **Connexion** avec compte de test user
5. **Ajouter un favori** → montrer l'AJAX (pas de rechargement !)
6. **Noter + commenter** la recette
7. **Déconnexion** → connexion admin
8. **Dashboard** → statistiques
9. **Ajouter une recette** avec photo + ingrédients
10. **Vérifier** qu'elle apparaît sur le site public

## Données de test à préparer AVANT

- ✅ Compte user : user@test.fr / motdepasse123
- ✅ Compte admin : admin@legrandgourmet.fr / (ton mdp)
- ✅ Au moins 10 recettes avec photos
- ✅ Quelques commentaires et notes existants
- ✅ Une photo de recette prête sur le bureau pour la démo d'ajout

---

# Questions du jury — Réponses préparées

**"Pourquoi PHP et pas un framework comme Symfony ?"**
> J'ai choisi PHP natif avec PDO pour maîtriser les fondamentaux avant d'aborder un framework. Cela m'a permis de comprendre ce que les frameworks automatisent : le routing, la sécurité, l'accès aux données. Mon code suit déjà une logique MVC qui facilitera ma montée en compétence sur Symfony ou Laravel.

**"Comment avez-vous géré la sécurité ?"**
> Quatre axes : (1) requêtes préparées PDO contre l'injection SQL, (2) htmlspecialchars() sur tous les affichages contre le XSS, (3) tokens CSRF sur les formulaires, (4) bcrypt pour les mots de passe et session_regenerate_id() contre la fixation de session.

**"Qu'est-ce que vous referiez différemment ?"**
> J'ajouterais des tests unitaires dès le début, j'utiliserais un système de migrations pour la BDD, et je structurerais le code en vrai MVC avec un routeur central plutôt que des fichiers PHP indépendants.

**"Expliquez la différence entre hash et chiffrement"**
> Le chiffrement est réversible (on peut déchiffrer avec une clé). Le hash est à sens unique : impossible de retrouver le mot de passe d'origine. Bcrypt ajoute un salt aléatoire et un coût configurable qui ralentit les attaques par force brute.

**"C'est quoi ACID ?"**
> Atomicité (une transaction est tout ou rien), Cohérence (la BDD reste dans un état valide), Isolation (les transactions simultanées ne s'interfèrent pas), Durabilité (une fois validée, une transaction est permanente même en cas de panne).

**"Pourquoi une table pivot recettes_ingredients ?"**
> C'est une relation N-N : une recette contient plusieurs ingrédients ET un ingrédient apparaît dans plusieurs recettes. La table pivot porte aussi la quantité, qui dépend du couple recette-ingrédient.
