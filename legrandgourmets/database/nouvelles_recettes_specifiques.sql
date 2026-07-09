-- ============================================================
-- LE GRAND GOURMET — Ajout de nouvelles recettes spécifiques
-- ============================================================
USE legrandgourmet;

-- ── Nouveaux ingrédients ────────────────────────────────────
INSERT IGNORE INTO ingredients (nom, unite) VALUES
  ('Saumon frais', 'g'),
  ('Betteraves', 'pièce'),
  ('Chèvre frais', 'g'),
  ('Éclats de pistache', 'g'),
  ('Oignons rouges', 'pièce'),
  ('Vinaigre blanc', 'ml'),
  ('Sucre vanillé', 'g'),
  ('Blancs de poulet', 'pièce'),
  ('Sauce teriyaki', 'ml'),
  ('Piments rouges', 'pièce'),
  ('Noix de coco râpée', 'g'),
  ('Pâte de curry rouge', 'c.à.s.'),
  ('Lait de coco', 'ml'),
  ('Sauce nuoc-mâm', 'ml'),
  ('Cacahuètes concassées', 'g'),
  ('Crevettes décortiquées', 'g'),
  ('Jus de citron frais', 'ml'),
  ('Piment de Cayenne', 'pincée'),
  ('Oignons blancs', 'pièce'),
  ('Glaçage au miel', 'ml'),
  ('Ail haché', 'c.à.c.'),
  ('Tofu soyeux', 'g'),
  ('Huile de sésame', 'ml'),
  ('Échalotes', 'pièce'),
  ('Crème de marron', 'g');

-- ============================================================
--   NOUVELLES RECETTES
-- ============================================================

-- RECETTE 1 : Salade de betteraves, chèvre et pistache
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Salade de Betteraves, Chèvre et Pistache',
  'salade-betteraves-chevre-pistache',
  'Une salade rafraîchissante et gourmande mêlant betteraves cuites, fromage frais et pistaches croquantes.',
  '1. Cuire les betteraves à l''eau bouillante 45 min.
2. Laisser refroidir puis couper en dés.
3. Préparer une vinaigrette : vinaigre blanc, huile d''olive, sucre, sel.
4. Mélanger les betteraves, chèvre émietté, oignons rouges finement tranchés.
5. Ajouter la vinaigrette.
6. Parsemer d''éclats de pistache au moment de servir.',
  20, 45, 4, 'Facile',
  'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800',
  1, 5, 1
);

-- RECETTE 2 : Filet de saumon teriyaki aux légumes
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Filet de Saumon Teriyaki aux Légumes',
  'saumon-teriyaki-legumes',
  'Un saumon glacé à la teriyaki, accompagné de légumes de saison sautés.',
  '1. Préparer la sauce teriyaki : sauce soja, miel, gingembre râpé, ail.
2. Placer le saumon peau vers le bas dans un plat.
3. Napper de sauce teriyaki.
4. Cuire au four 15 min à 200°C.
5. Sauté les légumes (brocoli, carottes, poivrons) dans un wok.
6. Ajouter le saumon cuit aux légumes.',
  15, 20, 4, 'Moyen',
  'https://images.unsplash.com/photo-1615937657715-bc7706b960fc?w=800',
  1, 2, 1
);

-- RECETTE 3 : Curry rouge de crevettes à la noix de coco
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Curry Rouge de Crevettes à la Noix de Coco',
  'curry-crevettes-coco',
  'Un curry asiatique savoureux avec crevettes juteuses et sauce crémeuse à la noix de coco.',
  '1. Faire revenir l''ail et l''échalote dans l''huile de sésame.
2. Ajouter la pâte de curry rouge, mélanger 1 min.
3. Ajouter le lait de coco et la sauce nuoc-mâm.
4. Laisser mijoter 5 min.
5. Ajouter les crevettes décortiquées, piment rouge en lanières.
6. Cuire 5 min. Parsemer de cacahuètes concassées.
7. Servir avec du riz thaï.',
  15, 15, 4, 'Moyen',
  'https://images.unsplash.com/photo-1455619452474-d2be8b1e4e31?w=800',
  1, 2, 1
);

-- RECETTE 4 : Poulet mariné au miel et citron
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Poulet Mariné au Miel et Citron',
  'poulet-miel-citron',
  'Des blancs de poulet tendres et juteux, marinés dans un glaçage miel-citron irrésistible.',
  '1. Préparer la marinade : miel, jus de citron, ail haché, sauce soja, huile d''olive.
2. Placer les blancs de poulet dans un plat.
3. Verser la marinade, couvrir et laisser reposer 2h au réfrigérateur.
4. Cuire à la poêle 12 min (6 min de chaque côté) à feu moyen-vif.
5. Arroser avec la marinade en cours de cuisson.
6. Servir chaud avec des légumes grillés.',
  10, 12, 4, 'Facile',
  'https://images.unsplash.com/photo-1598103442097-8b74394b95c7?w=800',
  1, 2, 1
);

-- RECETTE 5 : Soupe asiatique au tofu et miso
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Soupe Asiatique au Tofu et Miso',
  'soupe-tofu-miso',
  'Une soupe réconfortante et légère, parfumée au miso et agrémentée de tofu soyeux.',
  '1. Porter le bouillon à ébullition dans une casserole.
2. Ajouter le miso délayé dans un peu d''eau froide.
3. Ajouter le tofu coupé en dés, les oignons verts.
4. Laisser frémir 5 min sans bouillir.
5. Ajouter les champignons de Paris tranchés finement.
6. Cuire 3 min de plus.
7. Verser dans les bols et garnir d''oignons blancs émincés.',
  10, 12, 4, 'Facile',
  'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800',
  1, 6, 1
);

-- RECETTE 6 : Crème de marron et châtaignes
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Velouté de Marron et Châtaignes',
  'veloute-marron-chataignes',
  'Un velouté onctueux et savoureux, parfait pour les premiers jours d''automne.',
  '1. Faire sauté les échalotes dans le beurre.
2. Ajouter la crème de marron et les châtaignes.
3. Verser le bouillon de volaille.
4. Laisser mijoter 15 min.
5. Mixer à l''aide d''un mixeur plongeant jusqu''à obtenir un velouté lisse.
6. Rectifier l''assaisonnement en sel et poivre.
7. Servir avec des croûtons dorés et un trait de crème fraîche.',
  10, 25, 4, 'Facile',
  'https://images.unsplash.com/photo-1476124369162-f4978d5c4b1a?w=800',
  1, 6, 1
);

-- RECETTE 7 : Tiramisu maison
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Tiramisu Maison Traditional',
  'tiramisu-maison',
  'Le classique italien : couches de biscuits à la cuillère imbibés de café et crème mascarpone.',
  '1. Mélanger les jaunes d''œufs avec le sucre vanillé jusqu''à blanchissement.
2. Incorporer délicatement le mascarpone.
3. Tremper rapidement les biscuits dans le café froid.
4. Alterner les couches : biscuits, crème mascarpone.
5. Finir par une couche de crème.
6. Saupoudrer de cacao en poudre.
7. Laisser reposer au réfrigérateur 4h minimum.',
  30, 0, 8, 'Moyen',
  'https://images.unsplash.com/photo-1571877227200-a0fb556c2a1e?w=800',
  1, 3, 1
);

-- RECETTE 8 : Panna cotta à la vanille
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Panna Cotta à la Vanille et Coulis de Fraises',
  'panna-cotta-vanille',
  'Un dessert italien soyeux et élégant, garni d''un coulis de fraises maison.',
  '1. Verser la crème fraîche dans une casserole.
2. Ajouter la gousse de vanille fendue et grattée.
3. Chauffer sans bouillir jusqu''à frémissement.
4. Retirer du feu et laisser infuser 15 min.
5. Diluer la gélatine dans l''eau froide, puis l''ajouter à la crème chaude.
6. Verser dans les verres ou moules.
7. Laisser prendre 6h au réfrigérateur.
8. Préparer un coulis avec des fraises et du sucre.',
  20, 15, 4, 'Moyen',
  'https://images.unsplash.com/photo-1488477181946-85a2a9e2dd38?w=800',
  1, 3, 1
);

-- RECETTE 9 : Pâtes aux quatre fromages
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Pâtes aux Quatre Fromages',
  'pates-quatre-fromages',
  'Un classique riche et savoureux : mélange de mozzarella, ricotta, parmesan et gorgonzola.',
  '1. Faire cuire les pâtes al dente dans l''eau bouillante salée.
2. Fondre le beurre dans une grande casserole.
3. Ajouter la crème fraîche et les quatre fromages découpés en morceaux.
4. Faire fondre doucement en mélangeant régulièrement.
5. Égoutter les pâtes et les ajouter à la sauce.
6. Mélanger délicatement.
7. Servir immédiatement parsemé de persil frais et de poivre moulu.',
  10, 10, 4, 'Facile',
  'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800',
  1, 2, 1
);

-- RECETTE 10 : Risotto aux champignons de Paris
INSERT INTO recettes (titre, slug, description, instructions, temps_preparation, temps_cuisson, nb_personnes, difficulte, image, est_publiee, categorie_id, auteur_id)
VALUES (
  'Risotto Crémeux aux Champignons de Paris',
  'risotto-champignons-paris',
  'Un risotto onctueux et savoureux, parfait pour les soirées gourmandes.',
  '1. Faire sauté les champignons de Paris nettoyés et tranchés dans le beurre.
2. Réserver les champignons.
3. Faire revenir l''ail et l''échalote dans le beurre.
4. Ajouter le riz arborio en mélangeant 2 min.
5. Verser un verre de vin blanc sec, laisser absorber.
6. Ajouter progressivement le bouillon chaud en mélangeant régulièrement.
7. Après 18 min, ajouter les champignons réservés.
8. Finir avec du beurre et du parmesan râpé.',
  15, 25, 4, 'Moyen',
  'https://images.unsplash.com/photo-1630383249896-424e7b445d32?w=800',
  1, 2, 1
);

-- ============================================================
-- Associations recette ↔ ingrédients
-- ============================================================

-- Recette 1 : Salade betteraves, chèvre et pistache
INSERT IGNORE INTO recette_ingredient (recette_id, ingredient_id, quantite)
VALUES
  ((SELECT id FROM recettes WHERE slug='salade-betteraves-chevre-pistache'), (SELECT id FROM ingredients WHERE nom='Betteraves'), '4'),
  ((SELECT id FROM recettes WHERE slug='salade-betteraves-chevre-pistache'), (SELECT id FROM ingredients WHERE nom='Chèvre frais'), '150g'),
  ((SELECT id FROM recettes WHERE slug='salade-betteraves-chevre-pistache'), (SELECT id FROM ingredients WHERE nom='Oignons rouges'), '1'),
  ((SELECT id FROM recettes WHERE slug='salade-betteraves-chevre-pistache'), (SELECT id FROM ingredients WHERE nom='Vinaigre blanc'), '60ml'),
  ((SELECT id FROM recettes WHERE slug='salade-betteraves-chevre-pistache'), (SELECT id FROM ingredients WHERE nom='Éclats de pistache'), '50g');

-- Recette 4 : Poulet miel citron
INSERT IGNORE INTO recette_ingredient (recette_id, ingredient_id, quantite)
VALUES
  ((SELECT id FROM recettes WHERE slug='poulet-miel-citron'), (SELECT id FROM ingredients WHERE nom='Blancs de poulet'), '4'),
  ((SELECT id FROM recettes WHERE slug='poulet-miel-citron'), (SELECT id FROM ingredients WHERE nom='Miel'), '4 c.à.s.'),
  ((SELECT id FROM recettes WHERE slug='poulet-miel-citron'), (SELECT id FROM ingredients WHERE nom='Citron'), '2'),
  ((SELECT id FROM recettes WHERE slug='poulet-miel-citron'), (SELECT id FROM ingredients WHERE nom='Ail haché'), '2 c.à.c.'),
  ((SELECT id FROM recettes WHERE slug='poulet-miel-citron'), (SELECT id FROM ingredients WHERE nom='Sauce soja'), '3 c.à.s.');

-- Recette 9 : Pâtes quatre fromages
INSERT IGNORE INTO recette_ingredient (recette_id, ingredient_id, quantite)
VALUES
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Pâtes'), '400g'),
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Mozzarella'), '150g'),
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Gruyère'), '100g'),
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Parmesan'), '100g'),
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Crème liquide'), '300ml'),
  ((SELECT id FROM recettes WHERE slug='pates-quatre-fromages'), (SELECT id FROM ingredients WHERE nom='Beurre'), '50g');
