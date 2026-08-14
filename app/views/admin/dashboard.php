<main>

    <?= Session::getFlash() ?>

    <div class="page-header">
        <h1>Tableau de <span class="accent">bord</span></h1>
        <p>Vue d'ensemble de l'activité CarReserve</p>
    </div>

    <!-- STATS CARDS -->
    <div class="admin-stats-grid">

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(249,115,22,.15);color:var(--orange);">👥</div>
            <div class="stat-card-body">
                <span class="stat-card-value"><?= number_format($stats['clients']) ?></span>
                <span class="stat-card-label">Clients inscrits</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(59,130,246,.15);color:var(--blue);">🚗</div>
            <div class="stat-card-body">
                <span class="stat-card-value"><?= number_format($stats['vehicules']) ?></span>
                <span class="stat-card-label">Véhicules total</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(34,197,94,.15);color:var(--success);">✅</div>
            <div class="stat-card-body">
                <span class="stat-card-value"><?= number_format($stats['disponibles']) ?></span>
                <span class="stat-card-label">Disponibles</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(249,115,22,.15);color:var(--orange);">📋</div>
            <div class="stat-card-body">
                <span class="stat-card-value"><?= number_format($stats['reservations']) ?></span>
                <span class="stat-card-label">Réservations</span>
            </div>
        </div>

        <div class="stat-card" style="grid-column: span 2;">
            <div class="stat-card-icon" style="background:rgba(34,197,94,.15);color:var(--success);">💰</div>
            <div class="stat-card-body">
                <span class="stat-card-value"><?= number_format($stats['chiffre'], 0, ',', ' ') ?> FCFA</span>
                <span class="stat-card-label">Chiffre d'affaires total</span>
            </div>
        </div>

    </div>

    <!-- ACCÈS RAPIDES -->
    <div class="section">
        <div class="section-header">
            <h2>Accès <span class="accent">rapides</span></h2>
        </div>
        <div class="admin-quick-grid">
            <a href="<?= APP_URL ?>/index.php?page=admin-vehicules" class="admin-quick-card">
                <span>🚗</span>
                <strong>Gérer les véhicules</strong>
                <small>Ajouter, modifier, statuts</small>
            </a>
            <a href="<?= APP_URL ?>/index.php?page=admin-reservations" class="admin-quick-card">
                <span>📋</span>
                <strong>Réservations</strong>
                <small>Voir et gérer toutes les réservations</small>
            </a>
            <a href="<?= APP_URL ?>/index.php?page=admin-clients" class="admin-quick-card">
                <span>👥</span>
                <strong>Clients</strong>
                <small>Liste et rôles des utilisateurs</small>
            </a>
            <a href="<?= APP_URL ?>/index.php?page=admin-categories" class="admin-quick-card">
                <span>📦</span>
                <strong>Catégories</strong>
                <small>Gérer les catégories de véhicules</small>
            </a>
            <a href="<?= APP_URL ?>/index.php?page=admin-agences" class="admin-quick-card">
                <span>🏢</span>
                <strong>Agences</strong>
                <small>Gérer les agences de location</small>
            </a>
        </div>
    </div>

</main>
