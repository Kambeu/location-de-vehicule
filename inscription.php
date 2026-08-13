<?php
// POST traité EN PREMIER, avant tout output HTML
include __DIR__ . "/config.php";
include __DIR__ . "/fonction.php";

// Rediriger si déjà connecté
redirect_if_logged_in('index.php');

$errors   = [];
$old      = []; // Repopuler le formulaire après erreur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et nettoyer les entrées
    $old['fullName']  = trim($_POST['fullName']  ?? '');
    $old['username']  = trim($_POST['username']  ?? '');
    $old['email']     = trim($_POST['email']     ?? '');
    $old['birthdate'] = trim($_POST['birthdate'] ?? '');
    $old['country']   = trim($_POST['country']   ?? '');
    $old['gender']    = trim($_POST['gender']    ?? '');
    $password         = $_POST['password']        ?? '';
    $passwordConfirm  = $_POST['passwordConfirm'] ?? '';

    // ===== VALIDATIONS SERVEUR =====
    if (empty($old['fullName']))                        $errors[] = "Le nom complet est obligatoire.";
    if (empty($old['username']))                        $errors[] = "Le nom d'utilisateur est obligatoire.";
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "L'adresse email est invalide.";
    if (strlen($password) < 8)                          $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    if ($password !== $passwordConfirm)                 $errors[] = "Les mots de passe ne correspondent pas.";
    if (empty($old['country']))                         $errors[] = "Le pays est obligatoire.";
    if (empty($old['gender']))                          $errors[] = "Le genre est obligatoire.";

    // Si pas d'erreurs de validation, tenter l'inscription
    if (empty($errors)) {
        $result = inscription_user(
            $old['gender'],
            $old['fullName'],
            $old['username'],
            $old['email'],
            $password,
            $old['birthdate'],
            $old['country']
        );

        if ($result) {
            // Succès → rediriger vers login avec message flash
            header("Location: " . APP_URL . "/login.php");
            exit();
        }
        // Sinon, l'erreur est dans $_SESSION['auth_error'], on affiche le formulaire
    }
}

$page_title = "Inscription — " . APP_NAME;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/styleinscription.css">
</head>
<body>

<div class="register-container">

    <!-- HEADER -->
    <div class="register-header">
        <span class="icon">📝</span>
        <h1>Créer un compte</h1>
        <p>Rejoignez la communauté <?= APP_NAME ?></p>
    </div>

    <!-- MESSAGES FLASH (session) -->
    <?= flash_message() ?>

    <!-- ERREURS DE VALIDATION -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 18px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- FORMULAIRE -->
    <form method="POST" action="" novalidate>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <!-- NOM COMPLET -->
        <div class="form-group">
            <label for="fullName">Nom complet <span class="required">*</span></label>
            <input type="text" id="fullName" name="fullName"
                   placeholder="Jean Dupont"
                   value="<?= htmlspecialchars($old['fullName'] ?? '') ?>"
                   required>
        </div>

        <!-- NOM D'UTILISATEUR -->
        <div class="form-group">
            <label for="username">Nom d'utilisateur <span class="required">*</span></label>
            <input type="text" id="username" name="username"
                   placeholder="jean_dupont"
                   value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                   required>
        </div>

        <!-- EMAIL -->
        <div class="form-group">
            <label for="email">Adresse email <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                   placeholder="jean@email.com"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required>
        </div>

        <!-- MOT DE PASSE -->
        <div class="form-group">
            <label for="password">Mot de passe <span class="required">*</span></label>
            <input type="password" id="password" name="password"
                   placeholder="Minimum 8 caractères"
                   required minlength="8">
        </div>

        <!-- CONFIRMATION MOT DE PASSE -->
        <div class="form-group">
            <label for="passwordConfirm">Confirmer le mot de passe <span class="required">*</span></label>
            <input type="password" id="passwordConfirm" name="passwordConfirm"
                   placeholder="Confirmez votre mot de passe"
                   required>
        </div>

        <!-- DATE DE NAISSANCE -->
        <div class="form-group">
            <label for="birthdate">Date de naissance</label>
            <input type="date" id="birthdate" name="birthdate"
                   value="<?= htmlspecialchars($old['birthdate'] ?? '') ?>">
        </div>

        <!-- PAYS -->
        <div class="form-group">
            <label for="country">Pays <span class="required">*</span></label>
            <select id="country" name="country" required>
                <option value="">Sélectionnez votre pays</option>
                <?php
                $pays_list = ["France","Cameroun","Côte d'Ivoire","Sénégal","Maroc","Algérie","Tunisie","Canada","Belgique","Suisse","Autre"];
                foreach ($pays_list as $pays):
                    $selected = (($old['country'] ?? '') === $pays) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($pays) ?>" <?= $selected ?>><?= htmlspecialchars($pays) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- GENRE -->
        <div class="form-group">
            <label>Genre <span class="required">*</span></label>
            <div class="radio-group">
                <?php foreach (['Homme', 'Femme', 'Autre'] as $g): ?>
                    <label>
                        <input type="radio" name="gender" value="<?= $g ?>"
                               <?= (($old['gender'] ?? '') === $g) ? 'checked' : '' ?> required>
                        <?= $g ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- BOUTONS -->
        <button type="submit" class="btn-register">S'inscrire</button>
        <button type="reset" class="btn-register"
                style="margin-top: 10px; background: #6c757d; font-size: 14px;">
            Effacer tout
        </button>

    </form>

    <!-- DIVIDER -->
    <div class="divider">ou continuez avec</div>

    <!-- SOCIAL + LIEN LOGIN -->
    <div class="register-footer">
        <div class="social-login">
            <a href="#" class="google"   title="Google">G</a>
            <a href="#" class="facebook" title="Facebook">f</a>
            <a href="#" class="github"   title="GitHub">gh</a>
        </div>
        <p style="margin-top: 20px;">
            Déjà un compte ? <a href="<?= APP_URL ?>/login.php">Se connecter</a>
        </p>
    </div>

</div>

</body>
</html>
