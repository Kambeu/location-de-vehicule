<?php

/**
 * Session — Gestion centralisée des sessions et messages flash.
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

    // ===== AUTHENTIFICATION =====

    public static function setUser(array $user): void
    {
        session_regenerate_id(true); // Prévention fixation de session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['genre']     = $user['genre'];
        $_SESSION['logged_in'] = true;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
    }

    // ===== MESSAGES FLASH =====

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(): string
    {
        if (empty($_SESSION['flash'])) {
            return '';
        }

        $html = '';
        foreach ($_SESSION['flash'] as $type => $message) {
            $safe = htmlspecialchars($message);
            $icon = ($type === 'error') ? '⚠️' : '✅';
            $html .= "<div class=\"alert alert-{$type}\">{$icon} {$safe}</div>";
        }

        unset($_SESSION['flash']);
        return $html;
    }

    // ===== RACCOURCIS ACCÈS SESSION =====

    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}
