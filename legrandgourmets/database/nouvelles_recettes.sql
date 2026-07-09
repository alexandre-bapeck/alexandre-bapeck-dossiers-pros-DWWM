-- ============================================================
--   LE GRAND GOURMET — Ajout de recettes avec photos (Unsplash)
-- ============================================================
USE legrandgourmet;

-- ── Second utilisateur de test (id = 2) ──────────────────────
INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role) VALUES
  ('Sophie', 'sophie@legrandgourmet.fr', '$2y$10$HASH_A_REMPLACER', 'user');

-- ── Photos sur les 3 recettes existantes ─────────────────────
UPDATE recettes SET image = 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=800' WHERE id = 1;
UPDATE recettes SET image = 'https://images.unsplash.com/photo-1621743478914-cc8a86d7e7b5?w=800' WHERE id = 2;
UPDATE recettes SET image = 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=800' WHERE id = 3;

-- ── Nouveaux ingrédients (id 18 à 55) ────────────────────────
INSERT INTO ingredients (nom) VALUES
  ('Poulet'),              -- 18
  ('Citron'),              -- 19
  ('Thym'),                -- 20
  ('Romarin'),             -- 21
  ('Mozzarella'),          -- 22
  ('Jambon'),              -- 23
  ('Champignons'),         -- 24
  ('Crème liquide'),       -- 25
  ('Riz'),                 -- 26
  ('Bouillon de volaille'),-- 27
  ('Saumon'),              -- 28
  ('Aneth'),               -- 29
  ('Câpres'),              -- 30
  ('Avocat'),              -- 31
  ('Crevettes'),           -- 32
  ('Sauce soja'),          -- 33
  ('Gingembre'),           -- 34
  ('Carotte'),             -- 35
  ('Poireau'),             -- 36
  ('Gruyère'),             -- 37
  ('Poire'),               -- 38
  ('Roquefort'),           -- 39
  ('Noix'),                -- 40
  ('Quinoa'),              -- 41
  ('Courgette'),           -- 42
  ('Tomates cerises'),     -- 43
  ('Vinaigre balsamique'), -- 44
  ('Miel'),                -- 45
  ('Pommes de terre'),     -- 46
  ('Mascarpone'),          -- 47
  ('Biscuits à la cuillère'),-- 48
  ('Café'),                -- 49
  ('Cacao'),               -- 50
  ('Chocolat noir'),       -- 51
  ('Nouilles de riz'),     -- 52
  ('Nachos'),              -- 53
  ('Laitue romaine'),      -- 54
  ('Vin blanc');           -- 55

-- ============================================================
--   NOUVELLES RECETTES (id 4 à 15)
-- ============================================================

-- ── RECETTE 4 : Poulet rôti au citron ──────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Poulet Rôti au Citron & Thym',
  'poulet-roti-citron-thym',
  'Un poulet doré et juteux parfumé au citron et aux herbes fraîches.',
  '1. Préchauffer le four à 200°C.
2. Frotter le poulet avec de l''huile d''olive, sel, poivre.
3. Glisser des tranches de citron et du thym sous la peau.
4. Placer le poulet dans un plat, ajouter du romarin autour.
5. Enfourner 1h15 en arrosant toutes les 20 min.
6. Laisser reposer 10 min avant de servir.',
  15, 75, 4, 'Facile',
  'https://images.unsplash.com/photo-1598103442097-8b74394b95c7?w=800',
  1, 2, 1
);

-- ── RECETTE 5 : Pizza Margherita ───────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Pizza Margherita Maison',
  'pizza-margherita-maison',
  'La pizza italienne classique avec une pâte croustillante et une garniture généreuse.',
  '1. Préparer la pâte : mélanger farine, eau tiède, levure, sel, huile. Pétrir 10 min.
2. Laisser lever 1h à température ambiante.
3. Étaler la pâte en cercle fin.
4. Étaler les tomates, puis la mozzarella en tranches.
5. Ajouter quelques feuilles de basilic.
6. Cuire 12-15 min à 240°C.
7. Arroser d''un filet d''huile d''olive à la sortie.',
  70, 15, 4, 'Moyen',
  'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800',
  1, 2, 1
);

-- ── RECETTE 6 : Risotto aux champignons ───────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Risotto Crémeux aux Champignons',
  'risotto-cremeux-champignons',
  'Un risotto onctueux et savoureux, parfait pour les soirées d''hiver.',
  '1. Faire revenir l''oignon émincé dans le beurre.
2. Ajouter les champignons tranchés, faire dorer 5 min.
3. Ajouter le riz, nacrer 2 min.
4. Verser le vin blanc, laisser absorber.
5. Ajouter le bouillon louche par louche en remuant constamment.
6. Après 18 min, ajouter le parmesan et la crème.
7. Rectifier l''assaisonnement et servir immédiatement.',
  10, 25, 4, 'Moyen',
  'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=800',
  1, 2, 1
);

-- ── RECETTE 7 : Salade César ──────────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Salade César au Poulet Grillé',
  'salade-cesar-poulet-grille',
  'La salade César authentique avec sa sauce crémeuse et ses croûtons dorés.',
  '1. Griller les blancs de poulet assaisonnés 6 min de chaque côté.
2. Préparer la sauce : mélanger mayo, parmesan râpé, ail, jus de citron, moutarde.
3. Couper la laitue romaine en morceaux.
4. Faire dorer les croûtons dans du beurre à l''ail.
5. Mélanger la salade avec la sauce.
6. Disposer le poulet tranché, les croûtons et copeaux de parmesan.',
  15, 12, 2, 'Facile',
  'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800',
  1, 5, 1
);

-- ── RECETTE 8 : Saumon en papillote ──────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Saumon en Papillote à l''Aneth',
  'saumon-papillote-aneth',
  'Un saumon tendre et parfumé, cuit en papillote pour conserver tous ses arômes.',
  '1. Préchauffer le four à 180°C.
2. Déposer chaque pavé de saumon sur une feuille d''aluminium.
3. Arroser de jus de citron, ajouter l''aneth et les câpres.
4. Saler, poivrer, ajouter un filet d''huile d''olive.
5. Fermer hermétiquement les papillotes.
6. Cuire 20 min au four.
7. Servir directement dans la papillote.',
  10, 20, 2, 'Facile',
  'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800',
  1, 2, 2
);

-- ── RECETTE 9 : Tiramisu ─────────────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Tiramisu Classique',
  'tiramisu-classique',
  'Le dessert italien indémodable, léger et savoureux au café.',
  '1. Séparer les blancs des jaunes d''œufs.
2. Fouetter les jaunes avec le sucre jusqu''à blanchissement.
3. Incorporer le mascarpone et bien mélanger.
4. Monter les blancs en neige ferme, les incorporer délicatement.
5. Tremper les biscuits rapidement dans le café froid.
6. Alterner couches de biscuits et crème dans un plat.
7. Saupoudrer de cacao, réfrigérer 4h minimum.',
  30, 0, 6, 'Moyen',
  'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800',
  1, 3, 1
);

-- ── RECETTE 10 : Guacamole ───────────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Guacamole Frais Maison',
  'guacamole-frais-maison',
  'Un guacamole frais et savoureux, prêt en 10 minutes.',
  '1. Couper les avocats en deux, retirer le noyau.
2. Récupérer la chair et l''écraser à la fourchette.
3. Ajouter le jus d''un citron vert, sel, poivre.
4. Incorporer l''oignon finement haché.
5. Ajouter les tomates cerises coupées en deux.
6. Mélanger et rectifier l''assaisonnement.
7. Servir immédiatement avec des nachos.',
  10, 0, 4, 'Facile',
  'https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=800',
  1, 1, 2
);

-- ── RECETTE 11 : Crème brûlée ────────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Crème Brûlée à la Vanille',
  'creme-brulee-vanille',
  'Le grand classique français avec sa carapace de caramel craquante.',
  '1. Préchauffer le four à 150°C.
2. Faire chauffer la crème avec la gousse de vanille fendue.
3. Fouetter les jaunes avec le sucre.
4. Verser la crème chaude sur les œufs en remuant.
5. Filtrer et verser dans des ramequins.
6. Cuire au bain-marie 45 min.
7. Réfrigérer 2h, puis caraméliser le sucre au chalumeau.',
  20, 45, 4, 'Difficile',
  'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=800',
  1, 3, 1
);

-- ── RECETTE 12 : Velouté de poireaux ─────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Velouté de Poireaux & Pommes de Terre',
  'veloute-poireaux-pommes-de-terre',
  'Un velouté doux et réconfortant, parfait pour les jours froids.',
  '1. Émincer les poireaux, couper les pommes de terre en dés.
2. Faire suer les poireaux dans le beurre 5 min.
3. Ajouter les pommes de terre et le bouillon.
4. Cuire 20 min à feu moyen.
5. Mixer finement.
6. Ajouter la crème fraîche, rectifier l''assaisonnement.
7. Servir avec des croûtons.',
  15, 25, 4, 'Facile',
  'https://images.unsplash.com/photo-1547592180-85f173990554?w=800',
  1, 6, 2
);

-- ── RECETTE 13 : Bowl de quinoa ──────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Bowl Quinoa & Légumes Rôtis',
  'bowl-quinoa-legumes-rotis',
  'Un bowl complet, coloré et nutritif pour un repas sain et gourmand.',
  '1. Cuire le quinoa dans 2x son volume d''eau salée, 12 min.
2. Couper courgette, carotte et tomates cerises.
3. Rôtir les légumes 20 min au four à 200°C avec huile et thym.
4. Préparer la sauce : huile d''olive, jus de citron, miel, moutarde.
5. Assembler le bowl : quinoa, légumes, avocat tranché.
6. Arroser de sauce et parsemer de graines.',
  15, 20, 2, 'Facile',
  'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800',
  1, 5, 2
);

-- ── RECETTE 14 : Fondant au chocolat ─────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Fondant au Chocolat Coulant',
  'fondant-chocolat-coulant',
  'Le dessert choc par excellence : une croûte dorée et un cœur fondant.',
  '1. Préchauffer le four à 200°C.
2. Faire fondre le chocolat noir avec le beurre au bain-marie.
3. Fouetter les œufs avec le sucre jusqu''à blanchissement.
4. Incorporer le mélange chocolat refroidi, puis la farine.
5. Beurrer et fariner des moules individuels.
6. Verser la pâte aux 3/4.
7. Cuire exactement 10-11 min.
8. Démouler immédiatement et servir avec une boule de glace.',
  15, 11, 6, 'Moyen',
  'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800',
  1, 3, 1
);

-- ── RECETTE 15 : Pad Thai ─────────────────────────────────
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Pad Thai aux Crevettes',
  'pad-thai-crevettes',
  'Le célèbre plat thaïlandais sauté, parfumé et légèrement épicé.',
  '1. Faire tremper les nouilles de riz 10 min dans l''eau chaude.
2. Faire revenir les crevettes dans un wok huilé, réserver.
3. Faire sauter l''ail et le gingembre, ajouter les légumes.
4. Ajouter les nouilles égouttées.
5. Mélanger sauce soja, sucre, jus de citron, verser dans le wok.
6. Ajouter les crevettes, mélanger 2 min.
7. Servir avec des cacahuètes concassées et de la coriandre.',
  15, 15, 2, 'Moyen',
  'https://images.unsplash.com/photo-1559314809-0d155014e29e?w=800',
  1, 2, 2
);

-- ============================================================
--   INGRÉDIENTS DES NOUVELLES RECETTES
--   Format : (recette_id, ingredient_id, quantite)
-- ============================================================
INSERT INTO recette_ingredient (recette_id, ingredient_id, quantite) VALUES
  -- Recette 4 : Poulet rôti au citron
  (4, 18, '1 entier'),         -- Poulet
  (4, 19, '2'),                -- Citron
  (4, 20, 'quelques branches'),-- Thym
  (4, 21, 'quelques branches'),-- Romarin

  -- Recette 5 : Pizza Margherita
  (5, 16, '300g'),             -- Farine
  (5, 22, '250g'),             -- Mozzarella
  (5, 8,  '200g'),             -- Tomates
  (5, 9,  'quelques feuilles'),-- Basilic

  -- Recette 6 : Risotto aux champignons
  (6, 26, '300g'),             -- Riz
  (6, 24, '300g'),             -- Champignons
  (6, 27, '1L'),               -- Bouillon de volaille
  (6, 55, '100ml'),            -- Vin blanc
  (6, 5,  '80g'),              -- Parmesan

  -- Recette 7 : Salade César
  (7, 18, '2 blancs'),         -- Poulet
  (7, 54, '1 tête'),           -- Laitue romaine
  (7, 5,  '60g'),              -- Parmesan

  -- Recette 8 : Saumon en papillote
  (8, 28, '2 pavés'),          -- Saumon
  (8, 19, '1 citron'),         -- Citron
  (8, 29, '1/2 bouquet'),      -- Aneth
  (8, 30, '1 c.à.s'),          -- Câpres

  -- Recette 9 : Tiramisu
  (9, 47, '250g'),             -- Mascarpone
  (9, 48, '24'),               -- Biscuits à la cuillère
  (9, 49, '200ml'),            -- Café
  (9, 14, '80g'),              -- Sucre
  (9, 50, '2 c.à.s'),          -- Cacao

  -- Recette 10 : Guacamole
  (10, 31, '3 bien mûrs'),     -- Avocat
  (10, 19, '1'),               -- Citron
  (10, 43, '100g'),            -- Tomates cerises

  -- Recette 11 : Crème brûlée
  (11, 25, '500ml'),           -- Crème liquide
  (11, 4,  '5'),               -- Oeufs
  (11, 14, '80g'),             -- Sucre

  -- Recette 12 : Velouté de poireaux
  (12, 36, '3'),               -- Poireau
  (12, 46, '400g'),            -- Pommes de terre
  (12, 27, '1L'),              -- Bouillon de volaille
  (12, 3,  '100ml'),           -- Crème fraîche

  -- Recette 13 : Bowl quinoa
  (13, 41, '200g'),            -- Quinoa
  (13, 42, '1'),               -- Courgette
  (13, 35, '2'),               -- Carotte
  (13, 31, '1'),               -- Avocat
  (13, 45, '1 c.à.s'),         -- Miel

  -- Recette 14 : Fondant au chocolat
  (14, 51, '200g'),            -- Chocolat noir
  (14, 15, '100g'),            -- Beurre
  (14, 4,  '4'),               -- Oeufs
  (14, 14, '80g'),             -- Sucre
  (14, 16, '40g'),             -- Farine

  -- Recette 15 : Pad Thai
  (15, 32, '300g'),            -- Crevettes
  (15, 52, '200g'),            -- Nouilles de riz
  (15, 33, '3 c.à.s'),         -- Sauce soja
  (15, 34, '1 morceau');       -- Gingembre

-- ============================================================
--   QUELQUES NOTES ET COMMENTAIRES DE TEST
--   Format : (utilisateur_id, recette_id, valeur)
-- ============================================================
INSERT INTO notes (utilisateur_id, recette_id, valeur) VALUES
  (1, 1,  5),   -- Admin  → Pâtes carbonara
  (1, 3,  5),   -- Admin  → Tarte aux fraises
  (1, 4,  5),   -- Admin  → Poulet rôti
  (1, 9,  5),   -- Admin  → Tiramisu
  (1, 14, 5),   -- Admin  → Fondant
  (2, 5,  4),   -- Sophie → Pizza
  (2, 6,  4),   -- Sophie → Risotto
  (2, 7,  4),   -- Sophie → Salade César
  (2, 13, 4),   -- Sophie → Bowl quinoa
  (2, 15, 4);   -- Sophie → Pad Thai

-- Commentaires : (auteur_id, recette_id, contenu)
INSERT INTO commentaires (auteur_id, recette_id, contenu) VALUES
  (2, 1,  'Recette excellente, j''ai adoré !'),
  (1, 4,  'Parfait pour un dîner en famille, tout le monde a adoré.'),
  (2, 14, 'Le cœur coulant était parfait, à refaire absolument !');

-- ── Résumé ─────────────────────────────────────────────────
SELECT COUNT(*) AS total_recettes      FROM recettes;
SELECT COUNT(*) AS total_ingredients   FROM ingredients;
SELECT COUNT(*) AS total_utilisateurs  FROM utilisateurs;
