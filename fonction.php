<?php

// ===== INSCRIPTION =====
function inscription_user(string $sexe, string $fullname, string $username, string $email, string $password, string $date_naiss, string $pays): bool
{
    global $connexion;

    // Vérifier si l'email ou le username existe déjà
    $check = $connexion->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['auth_error'] = "Cet email ou nom d'utilisateur est déjà utilisé.";
        $check->close();
        return false;
    }
    $check->close();

    

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insérer l'utilisateur avec une requête préparée
    $stmt = $connexion->prepare(
        "INSERT INTO users3 (username, email, password, full_name, date_naiss, pays, genre, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->bind_param("sssssss", $username, $email, $hashed_password, $fullname, $date_naiss, $pays, $sexe);

    if ($stmt->execute()) {
        $_SESSION['auth_success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        $stmt->close();
        return true;
    } else {
        $_SESSION['auth_error'] = "Erreur lors de l'inscription. Veuillez réessayer.";
        $stmt->close();
        return false;
    }
}

// ===== CONNEXION =====
function login_user(string $email, string $password): bool
{
    global $connexion;

    $stmt = $connexion->prepare("SELECT id, username, full_name, email, password, genre FROM users3 WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['auth_error'] = "Email ou mot de passe incorrect.";
        $stmt->close();
        return false;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Vérifier le mot de passe
    if (!password_verify($password, $user['password'])) {
        $_SESSION['auth_error'] = "Email ou mot de passe incorrect.";
        return false;
    } 
    

    // Régénérer l'ID de session pour éviter la fixation de session
    session_regenerate_id(true);

    // Stocker les infos en session
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['username']   = $user['username'];
    $_SESSION['full_name']  = $user['full_name'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['genre']      = $user['genre'];
    $_SESSION['logged_in']  = true;

    return true;
}

// ===== VÉRIFIER SI CONNECTÉ =====
function is_logged_in(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// ===== PROTÉGER UNE PAGE (redirection si non connecté) =====
function require_login(string $redirect = 'login.php'): void
{
    if (!is_logged_in()) {
        header("Location: " . APP_URL . "/" . $redirect);
        exit();
    }
}

// ===== REDIRIGER SI DÉJÀ CONNECTÉ =====
function redirect_if_logged_in(string $redirect = 'index.php'): void
{
    if (is_logged_in()) {
        header("Location: " . APP_URL . "/" . $redirect);
        exit();
    }
}

// ===== AFFICHER ET VIDER LES MESSAGES FLASH =====
function flash_message(): string
{
    $html = '';

    if (!empty($_SESSION['auth_error'])) {
        $msg = htmlspecialchars($_SESSION['auth_error']);
        $html = "<div class=\"alert alert-error\">⚠️ {$msg}</div>";
        unset($_SESSION['auth_error']);
    }

    if (!empty($_SESSION['auth_success'])) {
        $msg = htmlspecialchars($_SESSION['auth_success']);
        $html = "<div class=\"alert alert-success\">✅ {$msg}</div>";
        unset($_SESSION['auth_success']);
    }

    return $html;
}

// ===== DÉCONNEXION =====
function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}
