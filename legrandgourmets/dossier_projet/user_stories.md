# User Stories — LE GRAND GOURMET

## Format : En tant que [rôle], je veux [action] afin de [bénéfice]

---

## 🟦 Épopée 1 : Consultation des recettes (Visiteur)

| ID | User Story | Priorité | Critères d'acceptation |
|----|-----------|----------|------------------------|
| US-01 | En tant que **visiteur**, je veux voir la liste des recettes afin de découvrir des idées de plats | Haute | La page affiche les recettes avec photo, titre, durée, difficulté ; pagination de 9 recettes par page |
| US-02 | En tant que **visiteur**, je veux rechercher une recette par mot-clé afin de trouver rapidement ce que je cherche | Haute | La recherche porte sur le titre et la description ; résultats instantanés après soumission |
| US-03 | En tant que **visiteur**, je veux filtrer par catégorie et difficulté afin d'affiner ma sélection | Haute | Les filtres se combinent ; un bouton réinitialise les filtres |
| US-04 | En tant que **visiteur**, je veux consulter le détail d'une recette afin de connaître les ingrédients et les étapes | Haute | La page affiche : photo, temps, portions, difficulté, ingrédients avec quantités, note moyenne, commentaires |
| US-05 | En tant que **visiteur**, je veux voir les recettes par catégorie afin de naviguer par type de plat | Moyenne | Une page liste toutes les catégories avec le nombre de recettes |

---

## 🟩 Épopée 2 : Compte utilisateur

| ID | User Story | Priorité | Critères d'acceptation |
|----|-----------|----------|------------------------|
| US-06 | En tant que **visiteur**, je veux créer un compte afin d'accéder aux fonctionnalités membres | Haute | Email unique vérifié ; mot de passe 8 caractères min ; hash bcrypt ; message de confirmation |
| US-07 | En tant qu'**utilisateur**, je veux me connecter afin d'accéder à mon espace | Haute | Vérification email + password_verify ; session sécurisée (regenerate_id) ; redirection selon rôle |
| US-08 | En tant qu'**utilisateur**, je veux me déconnecter afin de sécuriser mon compte | Haute | Destruction complète de la session ; redirection accueil |

---

## 🟨 Épopée 3 : Interactions membres

| ID | User Story | Priorité | Critères d'acceptation |
|----|-----------|----------|------------------------|
| US-09 | En tant qu'**utilisateur**, je veux ajouter une recette en favori afin de la retrouver facilement | Moyenne | Toggle en AJAX sans rechargement ; icône cœur qui change d'état |
| US-10 | En tant qu'**utilisateur**, je veux consulter mes favoris afin de retrouver mes recettes préférées | Moyenne | Page dédiée listant uniquement mes favoris ; retrait possible en un clic |
| US-11 | En tant qu'**utilisateur**, je veux noter une recette de 1 à 5 étoiles afin de donner mon avis | Moyenne | Une seule note par utilisateur et par recette (modifiable) ; moyenne mise à jour |
| US-12 | En tant qu'**utilisateur**, je veux commenter une recette afin de partager mon expérience | Moyenne | Commentaire de 3 caractères min ; affichage avec nom et date ; protection XSS |

---

## 🟥 Épopée 4 : Administration

| ID | User Story | Priorité | Critères d'acceptation |
|----|-----------|----------|------------------------|
| US-13 | En tant qu'**admin**, je veux ajouter une recette afin d'enrichir le catalogue | Haute | Formulaire complet : titre, description, temps, difficulté, catégorie, photo (3 Mo max), ingrédients dynamiques |
| US-14 | En tant qu'**admin**, je veux modifier une recette afin de corriger ou compléter les informations | Haute | Formulaire pré-rempli ; remplacement de l'image possible |
| US-15 | En tant qu'**admin**, je veux supprimer une recette afin de retirer un contenu obsolète | Haute | Confirmation obligatoire ; suppression en cascade des favoris/notes/commentaires |
| US-16 | En tant qu'**admin**, je veux gérer les catégories afin d'organiser le contenu | Haute | CRUD complet ; slug auto-généré ; suppression bloquée si recettes liées |
| US-17 | En tant qu'**admin**, je veux modérer les commentaires afin de garantir un contenu approprié | Moyenne | Liste de tous les commentaires avec auteur et recette ; suppression avec confirmation |
| US-18 | En tant qu'**admin**, je veux voir les statistiques du site afin de suivre son activité | Basse | Dashboard avec compteurs : recettes, membres, commentaires, catégories |
| US-19 | En tant qu'**admin**, je veux voir la liste des membres afin de suivre la communauté | Basse | Tableau avec email, rôle, nb commentaires, nb favoris, date d'inscription |

---

## Récapitulatif

- **19 user stories** réparties en **4 épopées**
- **9 priorité Haute** (MVP)
- **7 priorité Moyenne**
- **3 priorité Basse**
