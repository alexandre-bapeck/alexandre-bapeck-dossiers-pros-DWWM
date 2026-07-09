-- ============================================================
-- Ajouter une photo à la recette "Poulet Rôti au Citron & Thym"
-- ============================================================
USE legrandgourmet;

-- Vérifier si la recette existe, sinon l'insérer avec une photo
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
)
ON DUPLICATE KEY UPDATE 
  image = 'https://images.unsplash.com/photo-1598103442097-8b74394b95c7?w=800';

-- Ajouter les ingrédients s'ils n'existent pas
INSERT IGNORE INTO ingredients (nom, unite) VALUES
  ('Poulet', 'pièce'),
  ('Citron', 'pièce'),
  ('Thym', 'cuillère à café'),
  ('Romarin', 'cuillère à café'),
  ('Huile d''olive', 'ml'),
  ('Sel', 'pincée'),
  ('Poivre', 'pincée');

-- Associer les ingrédients à la recette (si elle n'existe pas déjà)
INSERT IGNORE INTO recette_ingredient (recette_id, ingredient_id, quantite)
SELECT r.id, i.id, 
  CASE 
    WHEN i.nom = 'Poulet' THEN '1 entier'
    WHEN i.nom = 'Citron' THEN '2'
    WHEN i.nom = 'Thym' THEN '2 c.à.c.'
    WHEN i.nom = 'Romarin' THEN '2 c.à.c.'
    WHEN i.nom = 'Huile d''olive' THEN '50'
    WHEN i.nom = 'Sel' THEN '1'
    WHEN i.nom = 'Poivre' THEN '1'
  END
FROM recettes r
JOIN ingredients i ON 1=1
WHERE r.slug = 'poulet-roti-citron-thym'
  AND i.nom IN ('Poulet', 'Citron', 'Thym', 'Romarin', 'Huile d''olive', 'Sel', 'Poivre');
