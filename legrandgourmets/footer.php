</main><!-- /.main-content -->

<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <span class="footer-logo">🍽️ LE GRAND GOURMET</span>
      <p>Des recettes savoureuses, partagées avec passion.</p>
    </div>
    <div class="footer-links">
      <h4>Navigation</h4>
      <ul>
        <li><a href="<?= url('') ?>">Accueil</a></li>
        <li><a href="<?= url('recettes') ?>">Toutes les recettes</a></li>
        <?php if (isLoggedIn()): ?>
        <li><a href="<?= url('favoris') ?>">Mes favoris</a></li>
        <li><a href="<?= url('profil') ?>">Mon profil</a></li>
        <?php else: ?>
        <li><a href="<?= url('inscription') ?>">S'inscrire</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="footer-links">
      <h4>Catégories</h4>
      <ul>
        <li><a href="<?= url('recettes?cat=entrees') ?>">Entrées</a></li>
        <li><a href="<?= url('recettes?cat=plats') ?>">Plats</a></li>
        <li><a href="<?= url('recettes?cat=desserts') ?>">Desserts</a></li>
        <li><a href="<?= url('recettes?cat=soupes') ?>">Soupes</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> LE GRAND GOURMET — Projet RNCP Bac+2</p>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
