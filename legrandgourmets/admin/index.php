<?php
/**
 * Tableau de bord de l'administration.
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
requireAdmin();

// Récupération des statistiques globales
$nbRecettes     = compterRecettes($pdo);
$nbUsers        = compterUtilisateurs($pdo);
$nbCommentaires = compterCommentaires($pdo);
$nbCategories   = compterCategories($pdo);

// Les 5 dernières recettes (avec auteur et catégorie)
$sql = "SELECT r.*, u.pseudo AS auteur, c.nom AS cat_nom
        FROM recettes r
        JOIN utilisateurs u ON u.id = r.auteur_id
        JOIN categories   c ON c.id = r.categorie_id
        ORDER BY r.date_creation DESC LIMIT 5";
$dernieresRecettes = $pdo->query($sql)->fetchAll();

// Les 5 derniers utilisateurs inscrits
$derniersUsers = getDerniersUtilisateurs($pdo, 5);

$pageTitle = 'Administration';
include __DIR__ . '/../header.php';
?>
<div class="admin-layout">
<?php include __DIR__ . '/../admin-sidebar.php'; ?>
<div class="admin-content">

  <div class="admin-header">
    <h1>🏠 Tableau de bord</h1>
    <p>Bienvenue, <?= e(currentUser()['pseudo']) ?> !</p>
  </div>

  <!-- KPI -->
  <div class="kpi-grid">
    <div class="kpi-card kpi-terracotta">
      <span class="kpi-icon">🍽️</span>
      <span class="kpi-value"><?= $nbRecettes ?></span>
      <span class="kpi-label">Recettes</span>
    </div>
    <div class="kpi-card kpi-sage">
      <span class="kpi-icon">👥</span>
      <span class="kpi-value"><?= $nbUsers ?></span>
      <span class="kpi-label">Utilisateurs</span>
    </div>
    <div class="kpi-card kpi-miel">
      <span class="kpi-icon">💬</span>
      <span class="kpi-value"><?= $nbCommentaires ?></span>
      <span class="kpi-label">Commentaires</span>
    </div>
    <div class="kpi-card kpi-brun">
      <span class="kpi-icon">🗂️</span>
      <span class="kpi-value"><?= $nbCategories ?></span>
      <span class="kpi-label">Catégories</span>
    </div>
  </div>

  <!-- Dernières recettes -->
  <div class="admin-section">
    <div class="admin-section-header">
      <h2>Dernières recettes</h2>
      <a href="<?= url('admin/recettes') ?>" class="btn btn-primary btn-sm">Gérer</a>
    </div>
    <table class="admin-table">
      <thead><tr><th>Titre</th><th>Catégorie</th><th>Auteur</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($dernieresRecettes as $rec): ?>
        <tr>
          <td><?= e($rec['titre']) ?></td>
          <td><?= e($rec['cat_nom']) ?></td>
          <td><?= e($rec['auteur']) ?></td>
          <td><?= date('d/m/Y', strtotime($rec['date_creation'])) ?></td>
          <td><span class="badge <?= $rec['est_publiee'] ? 'badge-success' : 'badge-warning' ?>"><?= $rec['est_publiee'] ? 'Publiée' : 'Brouillon' ?></span></td>
          <td class="actions">
            <a href="<?= url('admin/recettes?action=edit&id=' . $rec['id']) ?>" class="btn-icon">✏️</a>
            <a href="<?= url('admin/recettes?action=delete&id=' . $rec['id']) ?>" class="btn-icon text-danger"
               onclick="return confirm('Supprimer cette recette ?')">🗑️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Derniers utilisateurs -->
  <div class="admin-section">
    <div class="admin-section-header">
      <h2>Derniers inscrits</h2>
      <a href="<?= url('admin/utilisateurs') ?>" class="btn btn-primary btn-sm">Gérer</a>
    </div>
    <table class="admin-table">
      <thead><tr><th>Pseudo</th><th>E-mail</th><th>Rôle</th><th>Inscription</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($derniersUsers as $u): ?>
        <tr>
          <td><?= e($u['pseudo']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-danger' : 'badge-info' ?>"><?= e($u['role']) ?></span></td>
          <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
          <td class="actions">
            <a href="<?= url('admin/utilisateurs?action=edit&id=' . $u['id']) ?>" class="btn-icon">✏️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>


