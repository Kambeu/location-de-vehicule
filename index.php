<?php
include __DIR__ . "/config.php";
include __DIR__ . "/fonction.php";

$page_title = APP_NAME . " — Accueil";
?>
<?php include __DIR__ . "/includes/header.php"; ?>
<?php include __DIR__ . "/includes/navbar.php"; ?>

<main>

<?php if (is_logged_in()): ?>

    <!-- ============================================================
         VUE CONNECTÉE — Dashboard avec infos de session
    ============================================================ -->

    <?= flash_message() ?>

    <!-- Bannière de bienvenue -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Bienvenue, <?= htmlspecialchars($_SESSION['full_name']) ?> 👋</h2>
            <p>Vous êtes connecté depuis votre compte <?= APP_NAME ?>. Bonne navigation !</p>
        </div>
        <div class="welcome-avatar">
            <?= ($_SESSION['genre'] === 'Femme') ? '👩' : '👨' ?>
        </div>
    </div>

    <!-- Grille de cartes -->
    <div class="dashboard-grid">

        <!-- Carte : Informations du profil -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">👤</div>
                <div>
                    <div class="card-title">Mon profil</div>
                    <div class="card-subtitle">Vos informations personnelles</div>
                </div>
            </div>
            <ul class="info-list">
                <li>
                    <span class="info-label">📛 Nom complet</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                </li>
                <li>
                    <span class="info-label">🏷️ Nom d'utilisateur</span>
                    <span class="info-value">@<?= htmlspecialchars($_SESSION['username']) ?></span>
                </li>
                <li>
                    <span class="info-label">✉️ Email</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['email']) ?></span>
                </li>
                <li>
                    <span class="info-label">⚧ Genre</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['genre']) ?></span>
                </li>
            </ul>
        </div>

        <!-- Carte : Session active -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">🔐</div>
                <div>
                    <div class="card-title">Session active</div>
                    <div class="card-subtitle">Détails de votre connexion</div>
                </div>
            </div>
            <ul class="info-list">
                <li>
                    <span class="info-label">🆔 ID utilisateur</span>
                    <span class="info-value">#<?= htmlspecialchars($_SESSION['user_id']) ?></span>
                </li>
                <li>
                    <span class="info-label">📅 Date</span>
                    <span class="info-value"><?= date('d/m/Y') ?></span>
                </li>
                <li>
                    <span class="info-label">🕐 Heure</span>
                    <span class="info-value"><?= date('H:i') ?></span>
                </li>
                <li>
                    <span class="info-label">✅ Statut</span>
                    <span class="info-value" style="color: #1e7e34;">● Connecté</span>
                </li>
            </ul>
        </div>

        <!-- Carte : Actions rapides -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon">⚡</div>
                <div>
                    <div class="card-title">Actions rapides</div>
                    <div class="card-subtitle">Gérer votre compte</div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 6px;">
                <a href="#" class="btn btn-primary" style="justify-content: center;">
                    ✏️ Modifier mon profil
                </a>
                <a href="#" class="btn btn-outline" style="justify-content: center;">
                    🔑 Changer mon mot de passe
                </a>
                <a href="<?= APP_URL ?>/logout.php" class="btn btn-danger" style="justify-content: center;">
                    🚪 Se déconnecter
                </a>
            </div>
        </div>

    </div>
    <!-- Fin dashboard-grid -->

<?php else: ?>

    <!-- ============================================================
         VUE PUBLIQUE — Page d'accueil pour les visiteurs
    ============================================================ -->

    <section class="hero">
        <h1>Découvrez le <span>Patrimoine</span><br>autrement</h1>
        <p>
            Rejoignez notre communauté et explorez, partagez et préservez
            le patrimoine culturel qui nous unit.
        </p>
        <div class="hero-buttons">
            <a href="<?= APP_URL ?>/inscription.php" class="btn btn-primary">
                🚀 Créer un compte gratuit
            </a>
            <a href="<?= APP_URL ?>/login.php" class="btn btn-outline">
                🔑 Se connecter
            </a>
        </div>
    </section>

<?php endif; ?>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
