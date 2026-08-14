<nav class="navbar">

    <a href="<?= APP_URL ?>/index.php?page=home" class="nav-brand">
        🚗 <span class="brand-accent">Car</span>Reserve
    </a>

    <div class="nav-links">

        <a href="<?= APP_URL ?>/index.php?page=vehicules" class="nav-link hide-mobile">Véhicules</a>

        <?php if (Session::isLoggedIn()): ?>

            <a href="<?= APP_URL ?>/index.php?page=mes-reservations" class="nav-link hide-mobile">Mes réservations</a>

            <?php if (Session::isAdmin()): ?>
                <a href="<?= APP_URL ?>/index.php?page=admin-dashboard" class="nav-link hide-mobile">
                    Admin <span class="nav-admin-badge">⚙</span>
                </a>
            <?php endif; ?>

            <div class="nav-user">
                <span class="user-dot"></span>
                <?= htmlspecialchars(Session::get('prenom') . ' ' . Session::get('nom')) ?>
            </div>
            <a href="<?= APP_URL ?>/index.php?page=logout" class="btn btn-danger btn-sm">Déconnexion</a>

        <?php else: ?>
            <a href="<?= APP_URL ?>/index.php?page=login"    class="btn btn-ghost btn-sm">Se connecter</a>
            <a href="<?= APP_URL ?>/index.php?page=register" class="btn btn-primary btn-sm">S'inscrire</a>
        <?php endif; ?>

    </div>

</nav>
