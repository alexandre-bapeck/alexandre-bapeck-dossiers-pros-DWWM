-- ============================================
--   LE GRAND GOURMET — Script de création de la BDD
-- ============================================

CREATE DATABASE IF NOT EXISTS `legrandgourmet`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `legrandgourmet`;

-- ============================================
-- 1. TABLE : categories
-- ============================================
CREATE TABLE categories (
  id        INT          AUTO_INCREMENT PRIMARY KEY,
  nom       VARCHAR(100) NOT NULL,
  slug      VARCHAR(100) NOT NULL UNIQUE,
  icone     VARCHAR(10),
  couleur   VARCHAR(7),
  image     VARCHAR(255)
);

-- ============================================
-- 2. TABLE : utilisateurs
-- ============================================
CREATE TABLE utilisateurs (
  id                INT          AUTO_INCREMENT PRIMARY KEY,
  pseudo            VARCHAR(100) NOT NULL UNIQUE,
  email             VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe      VARCHAR(255) NOT NULL,
  role              ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  date_inscription  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 3. TABLE : recettes
-- ============================================
CREATE TABLE recettes (
  id                 INT          AUTO_INCREMENT PRIMARY KEY,
  titre              VARCHAR(200) NOT NULL,
  slug               VARCHAR(200) NOT NULL UNIQUE,
  description        TEXT,
  instructions       TEXT         NOT NULL,
  temps_preparation  INT          NOT NULL DEFAULT 0 COMMENT 'en minutes',
  temps_cuisson      INT          NOT NULL DEFAULT 0 COMMENT 'en minutes',
  difficulte         ENUM('Facile', 'Moyen', 'Difficile') NOT NULL DEFAULT 'Moyen',
  nb_personnes       INT          NOT NULL DEFAULT 4,
  image              VARCHAR(255),
  est_publiee        TINYINT(1)   NOT NULL DEFAULT 1,
  vues               INT          NOT NULL DEFAULT 0,
  categorie_id       INT          NOT NULL,
  auteur_id          INT          NOT NULL,
  date_creation      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_recette_categorie
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_recette_auteur
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================
-- 4. TABLE : ingredients
-- ============================================
CREATE TABLE ingredients (
  id     INT          AUTO_INCREMENT PRIMARY KEY,
  nom    VARCHAR(100) NOT NULL UNIQUE,
  unite  VARCHAR(50)  COMMENT 'ex: g, ml, cuillère, pièce…'
);

-- ============================================
-- 5. TABLE : recette_ingredient  (table pivot)
-- ============================================
CREATE TABLE recette_ingredient (
  recette_id     INT          NOT NULL,
  ingredient_id  INT          NOT NULL,
  quantite       VARCHAR(50)  NOT NULL,
  PRIMARY KEY (recette_id, ingredient_id),
  CONSTRAINT fk_ri_recette
    FOREIGN KEY (recette_id)    REFERENCES recettes(id)     ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ri_ingredient
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)  ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================
-- 6. TABLE : commentaires
-- ============================================
CREATE TABLE commentaires (
  id              INT      AUTO_INCREMENT PRIMARY KEY,
  auteur_id       INT      NOT NULL,
  recette_id      INT      NOT NULL,
  contenu         TEXT     NOT NULL,
  date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_commentaire_auteur
    FOREIGN KEY (auteur_id)   REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_commentaire_recette
    FOREIGN KEY (recette_id)  REFERENCES recettes(id)     ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- 7. TABLE : favoris
-- ============================================
CREATE TABLE favoris (
  utilisateur_id  INT      NOT NULL,
  recette_id      INT      NOT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (utilisateur_id, recette_id),
  CONSTRAINT fk_favori_user
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_favori_recette
    FOREIGN KEY (recette_id)     REFERENCES recettes(id)     ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- 8. TABLE : notes
-- ============================================
CREATE TABLE notes (
  id              INT     AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id  INT     NOT NULL,
  recette_id      INT     NOT NULL,
  valeur          TINYINT NOT NULL CHECK (valeur BETWEEN 1 AND 5),
  UNIQUE KEY uq_note_user_recette (utilisateur_id, recette_id),
  CONSTRAINT fk_note_user
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_note_recette
    FOREIGN KEY (recette_id)     REFERENCES recettes(id)     ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================
-- DONNÉES DE TEST (jeu de données initial)
-- ============================================

-- Catégories
INSERT INTO categories (nom, slug, icone, couleur) VALUES
  ('Entrées',        'entrees',        '🥗', '#4CAF50'),
  ('Plats',          'plats',          '🍽️', '#C8603A'),
  ('Desserts',       'desserts',       '🍰', '#E91E8C'),
  ('Petit-déjeuner', 'petit-dejeuner', '☕', '#FF9800'),
  ('Salades',        'salades',        '🥙', '#8BC34A'),
  ('Soupes',         'soupes',         '🍲', '#2196F3');

-- Admin
INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role) VALUES
  ('Admin', 'admin@legrandgourmet.fr', '$2y$10$HASH_A_REMPLACER', 'admin');

-- Ingrédients
INSERT INTO ingredients (nom, unite) VALUES
  ('Pâtes',          'g'),
  ('Lardons',        'g'),
  ('Crème fraîche',  'ml'),
  ('Oeufs',          'pièce'),
  ('Parmesan',       'g'),
  ('Sel',            'pincée'),
  ('Poivre',         'pincée'),
  ('Tomates',        'pièce'),
  ('Basilic',        'feuilles'),
  ('Pâte brisée',    'rouleau'),
  ('Moutarde',       'cuillère à soupe'),
  ('Fromage râpé',   'g'),
  ('Fraises',        'g'),
  ('Sucre',          'g'),
  ('Beurre',         'g'),
  ('Farine',         'g'),
  ('Lait',           'ml');

-- Recettes (auteur_id = 1 = Admin)
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, difficulte, nb_personnes, est_publiee, categorie_id, auteur_id) VALUES
  ('Pâtes carbonara',
   'pates-carbonara',
   'Un grand classique italien crémeux et savoureux, prêt en 20 minutes.',
   'Faire cuire les pâtes al dente.\nFaire revenir les lardons.\nMélanger œufs et parmesan.\nHors du feu, incorporer la sauce aux pâtes égouttées.',
   10, 15, 'Facile', 4, 1, 2, 1),
  ('Tarte aux tomates et basilic',
   'tarte-tomates-basilic',
   'Une tarte estivale légère et parfumée, idéale en entrée ou en plat léger.',
   'Étaler la pâte brisée.\nBadigeonner de moutarde.\nDisposer les tomates en tranches.\nParsemer de fromage et basilic.\nCuire 25 min à 200°C.',
   15, 25, 'Facile', 6, 1, 1, 1),
  ('Tarte aux fraises',
   'tarte-fraises',
   'Un dessert frais et gourmand avec une crème pâtissière maison.',
   'Préparer la crème pâtissière avec lait, œufs, sucre et farine.\nFaire cuire la pâte brisée à blanc.\nGarnir de crème refroidie.\nDisposer les fraises lavées et équeutées.',
   30, 20, 'Moyen', 8, 1, 3, 1);

-- Recette ↔ Ingrédients
INSERT INTO recette_ingredient (recette_id, ingredient_id, quantite) VALUES
  (1, 1,  '400g'),   -- pâtes
  (1, 2,  '150g'),   -- lardons
  (1, 3,  '20ml'),   -- crème fraîche
  (1, 4,  '3'),      -- oeufs
  (1, 5,  '80g'),    -- parmesan
  (1, 6,  '1'),      -- sel
  (1, 7,  '1'),      -- poivre
  (2, 8,  '4'),      -- tomates
  (2, 9,  '10'),     -- feuilles basilic
  (2, 10, '1'),      -- pâte brisée
  (2, 11, '2'),      -- c.à.s. moutarde
  (2, 12, '100g'),   -- fromage râpé
  (3, 13, '500g'),   -- fraises
  (3, 14, '80g'),    -- sucre
  (3, 15, '50g'),    -- beurre
  (3, 16, '100g'),   -- farine
  (3, 17, '250ml');  -- lait
