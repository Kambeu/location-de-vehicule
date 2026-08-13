<?php
include __DIR__ . "/config.php";
include __DIR__ . "/fonction.php";

// Déconnecter uniquement si une session est active
if (is_logged_in()) {
    logout_user();
}

// Supprimer le cookie "Se souvenir de moi"
setcookie('remember_email', '', time() - 3600, '/');

// Message flash visible sur la page login
$_SESSION['auth_success'] = "Vous avez été déconnecté avec succès.";

header("Location: " . APP_URL . "/login.php");
exit();
