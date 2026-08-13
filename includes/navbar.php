<nav class="navbar">
    <a href="<?= APP_URL ?>/index.php" class="nav-brand">🏛️ <?= APP_NAME ?></a>

    <div class="nav-links">
        <?php if (is_logged_in()): ?>
            <span class="nav-user">👤 <?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline">Se déconnecter</a>
        <?php else: ?>
            <a href="<?= APP_URL ?>/login.php"      class="btn btn-outline">Se connecter</a>
            <a href="<?= APP_URL ?>/inscription.php" class="btn btn-primary">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>
