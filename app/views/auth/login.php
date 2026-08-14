<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/stylelogin.css">
</head>
<body>

<div class="auth-wrapper">

    <div class="auth-logo">
        <a href="<?= APP_URL ?>/index.php?page=home">🚗 <span class="accent">Car</span>Reserve</a>
    </div>

    <div class="auth-card">

        <div class="auth-header">
            <h1>Bon retour 👋</h1>
            <p>Connectez-vous à <span class="accent"><?= APP_NAME ?></span></p>
        </div>

        <?= Session::getFlash() ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/index.php?page=login" novalidate>

            <div class="form-group">
                <label for="email">Adresse email <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="email" name="email"
                           placeholder="jean@email.com"
                           value="<?= htmlspecialchars($rememberedEmail) ?>"
                           required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-password"
                            onclick="togglePw()" aria-label="Afficher/masquer">👁️</button>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-label">
                    <input type="checkbox" name="remember" <?= !empty($rememberedEmail) ? 'checked' : '' ?>>
                    Se souvenir de moi
                </label>
                <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-submit">Se connecter →</button>

        </form>

        <div class="auth-divider">ou</div>

        <div class="social-row">
            <a href="#" class="social-btn" title="Google">G</a>
            <a href="#" class="social-btn" title="Facebook">f</a>
            <a href="#" class="social-btn" title="GitHub">⌥</a>
        </div>

    </div>

    <div class="auth-footer">
        Pas encore de compte ? <a href="<?= APP_URL ?>/index.php?page=register">S'inscrire gratuitement</a>
    </div>

</div>

<script>
function togglePw() {
    const i = document.getElementById('password');
    const b = document.querySelector('.toggle-password');
    const t = i.type === 'text';
    i.type = t ? 'password' : 'text';
    b.textContent = t ? '👁️' : '🙈';
}
</script>
</body>
</html>
