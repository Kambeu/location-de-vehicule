<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/styleinscription.css">
</head>
<body>

<div class="auth-wrapper">

    <div class="auth-logo">
        <a href="<?= APP_URL ?>/index.php?page=home">🚗 <span class="accent">Car</span>Reserve</a>
    </div>

    <div class="auth-card">

        <div class="auth-header">
            <h1>Créer un compte 🚀</h1>
            <p>Rejoignez <span class="accent"><?= APP_NAME ?></span> et réservez votre véhicule</p>
        </div>

        <?= Session::getFlash() ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/index.php?page=register" novalidate>

            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom"
                       placeholder="votre nom"
                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                       required autocomplete="family-name">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="prenom" name="prenom"
                       placeholder="votre prénom"
                       value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                       required autocomplete="given-name">
            </div>

            <div class="form-group">
                <label for="email">Adresse email <span class="required">*</span></label>
                <input type="email" id="email" name="email"
                       placeholder="jean@email.com"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                       required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="telephone">
                    Téléphone
                    <small style="color:#6B6B7A;text-transform:none;font-weight:400;">(optionnel, 9 chiffres)</small>
                </label>
                <input type="tel" id="telephone" name="telephone"
                       placeholder="699000000"
                       value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                       maxlength="9" pattern="\d{9}" autocomplete="tel">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <div class="input-rel">
                    <input type="password" id="password" name="password"
                           placeholder="Minimum 8 caractères"
                           required minlength="8" autocomplete="new-password">
                    <button type="button" class="btn-toggle-pw" id="tp1"
                            onclick="toggleField('password','tp1')">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                <div class="input-rel">
                    <input type="password" id="password_confirm" name="password_confirm"
                           placeholder="Répétez le mot de passe"
                           required autocomplete="new-password">
                    <button type="button" class="btn-toggle-pw" id="tp2"
                            onclick="toggleField('password_confirm','tp2')">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Créer mon compte →</button>

        </form>

    </div>

    <div class="auth-footer">
        Déjà un compte ? <a href="<?= APP_URL ?>/index.php?page=login">Se connecter</a>
    </div>

</div>

<script>
function toggleField(id, btnId) {
    const i = document.getElementById(id);
    const b = document.getElementById(btnId);
    const t = i.type === 'text';
    i.type = t ? 'password' : 'text';
    b.textContent = t ? '👁️' : '🙈';
}
</script>
</body>
</html>
