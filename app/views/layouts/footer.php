    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand">
                🚗 <span class="accent">Car</span>Reserve
                <span>Location de véhicules en ligne</span>
            </div>
            <nav class="footer-nav">
                <a href="<?= APP_URL ?>/index.php?page=home">Accueil</a>
                <a href="<?= APP_URL ?>/index.php?page=vehicules">Véhicules</a>
                <?php if (Session::isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/index.php?page=mes-reservations">Mes réservations</a>
                    <a href="<?= APP_URL ?>/index.php?page=logout">Déconnexion</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/index.php?page=login">Connexion</a>
                    <a href="<?= APP_URL ?>/index.php?page=register">Inscription</a>
                <?php endif; ?>
            </nav>
            <p class="footer-copy">
                &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. Tous droits réservés.
            </p>
        </div>
    </footer>

</body>
</html>
