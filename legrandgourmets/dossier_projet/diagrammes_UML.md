# Diagrammes UML — LE GRAND GOURMET

> 💡 Copie chaque bloc de code dans **https://mermaid.live** pour générer les images,
> puis exporte en PNG pour ton dossier de projet.

---

## 1. Diagramme de Use Cases

```mermaid
graph LR
    subgraph Acteurs
        V[👤 Visiteur]
        U[👤 Utilisateur inscrit]
        A[👤 Administrateur]
    end

    subgraph "Système LE GRAND GOURMET"
        UC1([Consulter les recettes])
        UC2([Rechercher / Filtrer])
        UC3([S'inscrire / Se connecter])
        UC4([Gérer ses favoris])
        UC5([Noter une recette])
        UC6([Commenter une recette])
        UC7([Gérer les recettes CRUD])
        UC8([Gérer les catégories])
        UC9([Modérer les commentaires])
        UC10([Gérer les utilisateurs])
        UC11([Consulter le dashboard])
    end

    V --> UC1
    V --> UC2
    V --> UC3
    U --> UC1
    U --> UC2
    U --> UC4
    U --> UC5
    U --> UC6
    A --> UC7
    A --> UC8
    A --> UC9
    A --> UC10
    A --> UC11
```

---

## 2. Diagramme de Packages

```mermaid
graph TB
    subgraph "LE GRAND GOURMET"
        subgraph Presentation["📦 Présentation (Vues)"]
            P1[pages/]
            P2[includes/]
            P3[assets/]
        end
        subgraph Metier["📦 Métier (Contrôleurs)"]
            M1[auth/]
            M2[admin/]
            M3[api/]
        end
        subgraph Donnees["📦 Données (Modèle)"]
            D1[config/database.php]
            D2[(MySQL legrandgourmet)]
        end
    end

    Presentation --> Metier
    Metier --> Donnees
    D1 --> D2
```

---

## 3. Diagramme d'activité — Mission 1 : CRUD Recette (Admin)

```mermaid
flowchart TD
    Start([Début]) --> Login{Admin connecté ?}
    Login -- Non --> Redirect[Redirection connexion.php]
    Redirect --> End1([Fin])
    Login -- Oui --> Liste[Afficher liste des recettes]
    Liste --> Choix{Action choisie}
    Choix -- Ajouter --> Form[Afficher formulaire vide]
    Choix -- Modifier --> FormPre[Formulaire pré-rempli]
    Choix -- Supprimer --> Confirm{Confirmation ?}
    Confirm -- Non --> Liste
    Confirm -- Oui --> Delete[DELETE FROM recettes]
    Delete --> Flash1[Message flash succès]
    Flash1 --> Liste
    Form --> Submit[Soumission POST]
    FormPre --> Submit
    Submit --> Valid{Validation OK ?}
    Valid -- Non --> Erreurs[Afficher erreurs]
    Erreurs --> Form
    Valid -- Oui --> Upload{Image fournie ?}
    Upload -- Oui --> SaveImg[Upload + uniqid]
    Upload -- Non --> SaveBDD
    SaveImg --> SaveBDD[INSERT/UPDATE recettes]
    SaveBDD --> SaveIngr[Sauvegarder ingrédients pivot]
    SaveIngr --> Flash2[Message flash succès]
    Flash2 --> Liste
```

---

## 4. Diagramme de séquence — Mission 2 : Favori AJAX

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant N as Navigateur (JS)
    participant A as api/favori.php
    participant B as BDD MySQL

    U->>N: Clic sur 🤍 (bouton favori)
    N->>A: fetch POST {recette_id: 5}
    A->>A: session_start() + vérif connexion
    alt Non connecté
        A-->>N: JSON {error: "Non connecté"}
    else Connecté
        A->>B: SELECT COUNT(*) FROM favoris WHERE...
        B-->>A: Résultat (existe ou non)
        alt Déjà en favori
            A->>B: DELETE FROM favoris
            B-->>A: OK
            A-->>N: JSON {favori: false}
            N->>N: Bouton devient 🤍
        else Pas encore favori
            A->>B: INSERT INTO favoris
            B-->>A: OK
            A-->>N: JSON {favori: true}
            N->>N: Bouton devient ❤️
        end
    end
```

---

## 5. Diagramme de séquence — Authentification

```mermaid
sequenceDiagram
    actor U as Utilisateur
    participant F as connexion.php (Form)
    participant C as connexion.php (Traitement)
    participant B as BDD MySQL

    U->>F: Saisit email + mot de passe
    F->>C: POST email, mot_de_passe
    C->>B: SELECT * FROM utilisateurs WHERE email = ?
    B-->>C: Données utilisateur (ou null)
    alt Utilisateur inexistant
        C-->>F: Erreur "Email ou mot de passe incorrect"
    else Utilisateur trouvé
        C->>C: password_verify(mdp, hash)
        alt Mot de passe incorrect
            C-->>F: Erreur "Email ou mot de passe incorrect"
        else Mot de passe valide
            C->>C: session_regenerate_id(true)
            C->>C: $_SESSION[id, nom, role]
            alt role == admin
                C-->>U: Redirection /admin/dashboard.php
            else role == user
                C-->>U: Redirection /index.php
            end
        end
    end
```

---

## 6. MLD (Modèle Logique de Données)

```
utilisateurs (id, nom, email, mot_de_passe, role, created_at)
    PK : id
    UNIQUE : email

categories (id, nom, slug, image)
    PK : id
    UNIQUE : slug

recettes (id, titre, description, temps_preparation, temps_cuisson,
          difficulte, portions, image, #categorie_id, created_at)
    PK : id
    FK : categorie_id → categories(id)

ingredients (id, nom, unite)
    PK : id

recettes_ingredients (#recette_id, #ingredient_id, quantite)
    PK : (recette_id, ingredient_id)
    FK : recette_id → recettes(id)
    FK : ingredient_id → ingredients(id)

commentaires (id, #utilisateur_id, #recette_id, contenu, created_at)
    PK : id
    FK : utilisateur_id → utilisateurs(id)
    FK : recette_id → recettes(id)

favoris (#utilisateur_id, #recette_id, created_at)
    PK : (utilisateur_id, recette_id)
    FK : utilisateur_id → utilisateurs(id)
    FK : recette_id → recettes(id)

notes (id, #utilisateur_id, #recette_id, valeur)
    PK : id
    UNIQUE : (utilisateur_id, recette_id)
    FK : utilisateur_id → utilisateurs(id)
    FK : recette_id → recettes(id)
    CHECK : valeur BETWEEN 1 AND 5
```

---

## 7. MPD (Modèle Physique de Données)

```mermaid
erDiagram
    UTILISATEURS {
        INT id PK "AUTO_INCREMENT"
        VARCHAR_100 nom "NOT NULL"
        VARCHAR_150 email "NOT NULL UNIQUE"
        VARCHAR_255 mot_de_passe "NOT NULL (bcrypt)"
        ENUM role "admin|user DEFAULT user"
        DATETIME created_at "DEFAULT CURRENT_TIMESTAMP"
    }
    CATEGORIES {
        INT id PK "AUTO_INCREMENT"
        VARCHAR_100 nom "NOT NULL"
        VARCHAR_100 slug "NOT NULL UNIQUE"
        VARCHAR_255 image "NULL"
    }
    RECETTES {
        INT id PK "AUTO_INCREMENT"
        VARCHAR_200 titre "NOT NULL"
        TEXT description "NULL"
        INT temps_preparation "NOT NULL (minutes)"
        INT temps_cuisson "NOT NULL (minutes)"
        ENUM difficulte "facile|moyen|difficile"
        INT portions "DEFAULT 4"
        VARCHAR_255 image "NULL"
        INT categorie_id FK "NOT NULL"
        DATETIME created_at "DEFAULT CURRENT_TIMESTAMP"
    }
    INGREDIENTS {
        INT id PK "AUTO_INCREMENT"
        VARCHAR_100 nom "NOT NULL"
        VARCHAR_50 unite "NULL (g, ml, piece...)"
    }
    RECETTES_INGREDIENTS {
        INT recette_id PK_FK "ON DELETE CASCADE"
        INT ingredient_id PK_FK "ON DELETE RESTRICT"
        FLOAT quantite "NOT NULL"
    }
    COMMENTAIRES {
        INT id PK "AUTO_INCREMENT"
        INT utilisateur_id FK "ON DELETE CASCADE"
        INT recette_id FK "ON DELETE CASCADE"
        TEXT contenu "NOT NULL"
        DATETIME created_at "DEFAULT CURRENT_TIMESTAMP"
    }
    FAVORIS {
        INT utilisateur_id PK_FK "ON DELETE CASCADE"
        INT recette_id PK_FK "ON DELETE CASCADE"
        DATETIME created_at "DEFAULT CURRENT_TIMESTAMP"
    }
    NOTES {
        INT id PK "AUTO_INCREMENT"
        INT utilisateur_id FK "UNIQUE avec recette_id"
        INT recette_id FK "UNIQUE avec utilisateur_id"
        TINYINT valeur "CHECK 1-5"
    }

    CATEGORIES ||--o{ RECETTES : "classe"
    RECETTES ||--o{ RECETTES_INGREDIENTS : "contient"
    INGREDIENTS ||--o{ RECETTES_INGREDIENTS : "compose"
    UTILISATEURS ||--o{ COMMENTAIRES : "ecrit"
    RECETTES ||--o{ COMMENTAIRES : "recoit"
    UTILISATEURS ||--o{ FAVORIS : "ajoute"
    RECETTES ||--o{ FAVORIS : "figure"
    UTILISATEURS ||--o{ NOTES : "attribue"
    RECETTES ||--o{ NOTES : "recoit"
```
