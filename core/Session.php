<?php

/**
 * Session — Gestion centralisée des sessions et messages flash.
 * Aligné sur la table `utilisateur` :
 *   ID_UTILSATEUR | NOM | PRENOM | ADRESSE_EMAIL | ROLE
 */
class Session
{
    /**
     * Démarre la session si elle n'est pas déjà active.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ----------------------------------------------------------------
    //  AUTHENTIFICATION
    // ----------------------------------------------------------------

    /**
     * Enregistre l'utilisateur en session après connexion réussie.
     * Attend un tableau issu de UserModel::findByEmail().
     */
    public static function setUser(array $user): void
    {
        session_regenerate_id(true); // Prévention fixation de session

        $_SESSION['user_id']   = $user['ID_UTILSATEUR'];
        $_SESSION['nom']       = $user['NOM'];
        $_SESSION['prenom']    = $user['PRENOM'];
        $_SESSION['email']     = $user['ADRESSE_EMAIL'];
        $_SESSION['role']      = $user['ROLE'] ?? 'client';
        $_SESSION['logged_in'] = true;
    }

    /** Retourne le prénom + nom affiché dans l'interface. */
    public static function fullName(): string
    {
        return trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? ''));
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
    }

    /**
     * Détruit proprement la session et son cookie.
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ----------------------------------------------------------------
    //  MESSAGES FLASH
    // ----------------------------------------------------------------

    /**
     * Enregistre un message flash.
     * @param string $type  'success' | 'error' | 'warning' | 'info'
     */
    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Retourne le HTML des messages flash et les efface.
     */
    public static function getFlash(): string
    {
        if (empty($_SESSION['flash'])) {
            return '';
        }

        $icons = [
            'success' => '✅',
            'error'   => '⚠️',
            'warning' => '🔔',
            'info'    => 'ℹ️',
        ];

        $html = '';
        foreach ($_SESSION['flash'] as $type => $message) {
            $safe = htmlspecialchars($message);
            $icon = $icons[$type] ?? 'ℹ️';
            $html .= "<div class=\"alert alert-{$type}\">{$icon} {$safe}</div>\n";
        }

        unset($_SESSION['flash']);
        return $html;
    }

    // ----------------------------------------------------------------
    //  CSRF
    // ----------------------------------------------------------------

    /**
     * Génère (ou retourne) le token CSRF de la session courante.
     */
    public static function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le token CSRF soumis dans $_POST.
     * Lève une réponse 403 et stoppe l'exécution si invalide.
     */
    public static function verifyCsrf(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        $stored    = $_SESSION['csrf_token'] ?? '';

        if (empty($stored) || !hash_equals($stored, $submitted)) {
            http_response_code(403);
            die("<h1>403 — Requête invalide</h1><p>Token CSRF manquant ou incorrect.</p>");
        }

        // Rotation du token après vérification
        unset($_SESSION['csrf_token']);
    }

    // ----------------------------------------------------------------
    //  ACCÈS GÉNÉRIQUE
    // ----------------------------------------------------------------

    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}
