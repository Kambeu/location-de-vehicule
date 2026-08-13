<?php
// ===== TRAITEMENT POST EN PREMIER, avant tout output =====
include __DIR__ . "/config.php";
include __DIR__ . "/fonction.php";

// Rediriger si déjà connecté
redirect_if_logged_in('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']       ?? '';
    $remember = isset($_POST['remember']);

    // Validation basique côté serveur
    if (empty($email) || empty($password)) {
        $_SESSION['auth_error'] = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['auth_error'] = "L'adresse email est invalide.";
    } else {
        $success = login_user($email, $password);

        if ($success) {
            // Option "Se souvenir de moi" : cookie 30 jours
            if ($remember) {
                setcookie('remember_email', $email, time() + (30 * 24 * 3600), '/', '', false, true);
            } else {
                // Supprimer le cookie si décochée
                setcookie('remember_email', '', time() - 3600, '/');
            }

            header("Location: " . APP_URL . "/index.php");
            exit();
        }
        // En cas d'échec, $_SESSION['auth_error'] est déjà défini dans login_user()
    }
}

// Pré-remplir l'email depuis le cookie "Se souvenir de moi"
$remembered_email = htmlspecialchars($_COOKIE['remember_email'] ?? '');

$page_title = "Connexion — " . APP_NAME;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/stylelogin.css">
</head>
<body>

<div class="login-container">

    <!-- ===== HEADER ===== -->
    <div class="login-header">
        <span class="icon">🏛️</span>
        <h1>Bon retour !</h1>
        <p>Connectez-vous à votre compte <?= APP_NAME ?></p>
    </div>

    <!-- ===== MESSAGES FLASH ===== -->
    <?= flash_message() ?>

    <!-- ===== FORMULAIRE ===== -->
    <form method="POST" action="" novalidate>

        <!-- Email -->
        <div class="form-group">
            <label for="email">
                Adresse email <span class="required">*</span>
            </label>
            <div class="input-wrapper">
                <span class="input-icon">✉️</span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="jean@email.com"
                    value="<?= $remembered_email ?>"
                    required
                    autocomplete="email"
                >
            </div>
        </div>

        <!-- Mot de passe -->
        <div class="form-group">
            <label for="password">
                Mot de passe <span class="required">*</span>
            </label>
            <div class="input-wrapper">
                <span class="input-icon">🔒</span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Afficher/masquer le mot de passe">
                    👁️
                </button>
            </div>
        </div>

        <!-- Se souvenir + Mot de passe oublié -->
        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember"
                       <?= !empty($remembered_email) ? 'checked' : '' ?>>
                Se souvenir de moi
            </label>
            <a href="#" class="forgot-link">Mot de passe oublié ?</a>
        </div>

        <!-- Bouton connexion -->
        <button type="submit" class="btn-login">
            Se connecter
        </button>

    </form>

    <!-- ===== DIVIDER ===== -->
    <div class="divider">ou continuez avec</div>

    <!-- ===== SOCIAL LOGIN ===== -->
    <div class="social-login">
        <a href="#" class="google"   title="Connexion avec Google">G</a>
        <a href="#" class="facebook" title="Connexion avec Facebook">f</a>
        <a href="#" class="github"   title="Connexion avec GitHub">gh</a>
    </div>

    <!-- ===== LIEN INSCRIPTION ===== -->
    <div class="login-footer">
        <p>Pas encore de compte ? <a href="<?= APP_URL ?>/inscription.php">S'inscrire gratuitement</a></p>
    </div>

</div>

<!-- ===== TOGGLE MOT DE PASSE ===== -->
<script>
    function togglePassword() {
        const input  = document.getElementById('password');
        const btn    = document.querySelector('.toggle-password');
        const isText = input.type === 'text';
        input.type   = isText ? 'password' : 'text';
        btn.textContent = isText ? '👁️' : '🙈';
    }
</script>

</body>
</html>
