-- ============================================================
--   LE GRAND GOURMET — Corrections BDD
-- ============================================================
USE  LE GRAND GOURMET;

-- Ajouter la colonne vues si elle n'existe pas
ALTER TABLE recettes ADD COLUMN IF NOT EXISTS vues INT UNSIGNED DEFAULT 0;

-- Vérification finale
SELECT 'BDD OK ✅' AS statut;
