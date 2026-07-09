<?php
/**
 * Admin — modération des commentaires.
 */
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../fonctions.php';
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id     = isset($_GET['id'])     ? (int) $_GET['id'] : 0;

// Suppression d'un commentaire
if ($action === 'delete' && $id) {
    supprimerCommentaire($pdo, $id);
    setFlash('success', 'Commentaire supprimé.');
    header('Location: ' . url('admin/commentaires'));
    exit;
}

$commentaires = getTousCommentaires($pdo);

$pageTitle = 'Admin – Commentaires';
include __DIR__ . '/../header.php';
?>
<div class="admin-layout">
<?php include __DIR__ . '/../admin-sidebar.php'; ?>
<div class="admin-content">

  <div class="admin-header">
    <h1>💬 Gestion des commentaires</h1>
    <p><?= count($commentaires) ?> commentaire<?= count($commentaires) > 1 ? 's' : '' ?></p>
  </div>

  <table class="admin-table">
    <thead><tr><th>Auteur</th><th>Recette</th><th>Commentaire</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($commentaires as $c): ?>
      <tr>
        <td><?= e($c['pseudo']) ?></td>
        <td><a href="<?= url('recette?slug=' . e($c['recette_slug'])) ?>" target="_blank"><?= e($c['recette_titre']) ?></a></td>
        <td><?= e(mb_substr($c['contenu'], 0, 80)) ?><?= mb_strlen($c['contenu']) > 80 ? '…' : '' ?></td>
        <td><?= date('d/m/Y H:i', strtotime($c['date_creation'])) ?></td>
        <td class="actions">
          <a href="?action=delete&id=<?= $c['id'] ?>" class="btn-icon text-danger"
             onclick="return confirm('Supprimer ce commentaire ?')">🗑️</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>


