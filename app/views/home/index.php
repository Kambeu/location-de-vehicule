<main>

    <?= Session::getFlash() ?>

    <?php if (Session::isLoggedIn()): ?>
    <!-- ===== WELCOME BANNER ===== -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Bienvenue, <span class="name"><?= htmlspecialchars(Session::get('prenom') . ' ' . Session::get('nom')) ?></span> 👋</h2>
            <p>Prêt à réserver votre prochain véhicule ? Parcourez notre catalogue.</p>
            <div style="display:flex;gap:12px;margin-top:20px;">
                <a href="<?= APP_URL ?>/index.php?page=vehicules"        class="btn btn-primary btn-sm">🚘 Voir les véhicules</a>
                <a href="<?= APP_URL ?>/index.php?page=mes-reservations" class="btn btn-ghost btn-sm">📋 Mes réservations</a>
            </div>
        </div>
        <div class="welcome-icon">🚘</div>
    </div>

    <?php else: ?>
    <!-- ===== HERO ===== -->
    <div class="hero">
        <div class="hero-glow"></div>
        <div class="hero-content">
            <h1>
                Réservez votre<br>
                <span class="accent">véhicule idéal</span><br>
                en quelques clics
            </h1>
            <p>Large choix de voitures, SUV et utilitaires.<br>Simple, rapide et 100 % sécurisé.</p>
            <div class="hero-cta">
                <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-primary btn-xl">🚘 Parcourir les véhicules</a>
                <a href="<?= APP_URL ?>/index.php?page=register"  class="btn btn-ghost btn-lg">Créer un compte</a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-value">50+</span>
                    <span class="stat-label">Véhicules</span>
                </div>
                <div class="stat">
                    <span class="stat-value">24h</span>
                    <span class="stat-label">Disponible</span>
                </div>
                <div class="stat">
                    <span class="stat-value">100%</span>
                    <span class="stat-label">Sécurisé</span>
                </div>
                <div class="stat">
                    <span class="stat-value">0 frais</span>
                    <span class="stat-label">Cachés</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== VÉHICULES EN VEDETTE ===== -->
    <section class="section">
        <div class="section-header">
            <h2>Véhicules <span class="accent">disponibles</span></h2>
            <a href="<?= APP_URL ?>/index.php?page=vehicules" class="btn btn-outline btn-sm">Voir tout →</a>
        </div>

        <?php if (empty($vehiculesVedette)): ?>
            <div class="empty-state">
                <span class="empty-icon">🚗</span>
                <p>Aucun véhicule disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="vehicules-grid">
                <?php foreach ($vehiculesVedette as $v): ?>
                <div class="vehicule-card">
                    <div class="card-img">
                        <?php if (!empty($v['IMAGE_PRINCIPALE'])): ?>
                            <img src="<?= APP_URL ?>/assets/uploads/<?= htmlspecialchars($v['IMAGE_PRINCIPALE']) ?>"
                                 alt="<?= htmlspecialchars($v['MARQUE'] . ' ' . $v['MODELE']) ?>">
                        <?php else: ?>
                            <div class="card-img-placeholder">🚗</div>
                        <?php endif; ?>
                        <span class="card-badge"><?= htmlspecialchars($v['NOM_CATEGORIE'] ?? 'Véhicule') ?></span>
                        <span class="card-badge-blue">Disponible</span>
                    </div>
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($v['MARQUE'] . ' ' . $v['MODELE']) ?></div>
                        <div class="card-specs">
                            <?php if (!empty($v['TRANSMISSION'])): ?>
                                <span class="spec-tag">⚙️ <?= htmlspecialchars($v['TRANSMISSION']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($v['CARBURANT'])): ?>
                                <span class="spec-tag">⛽ <?= htmlspecialchars($v['CARBURANT']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($v['NOMBRE_PLACES'])): ?>
                                <span class="spec-tag">💺 <?= (int)$v['NOMBRE_PLACES'] ?> places</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <div class="card-price">
                                <span class="amount"><?= number_format((float)$v['TARIF_JOUR'], 0, ',', ' ') ?> FCFA</span>
                                <span class="unit">par jour</span>
                            </div>
                            <a href="<?= APP_URL ?>/index.php?page=vehicule-detail&id=<?= (int)$v['ID_VEHICULE'] ?>"
                               class="btn btn-primary btn-sm">Réserver</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>
