<?php
/**
 * Controller — Classe de base pour tous les contrôleurs.
 * Fournit le rendu des vues et les helpers de redirection / auth.
 */
abstract class Controller
{
    /**
     * Rend une vue en lui injectant des données.
     *
     * @param string $view  Chemin relatif depuis app/views/ (ex: "auth/login")
     * @param array  $data  Variables à extraire dans la vue
     */
    protected function render(string $view, array $data = []): void
    {
        // Rendre les variables accessibles dans la vue
        extract($data, EXTR_SKIP);

        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vue introuvable : " . htmlspecialchars($view));
        }

        // Charger le layout
        require APP_ROOT . '/app/views/layouts/header.php';
        require APP_ROOT . '/app/views/layouts/navbar.php';
        require $viewFile;
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    /**
     * Rend une vue SANS layout (ex: pages de connexion pleine page).
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vue introuvable : " . htmlspecialchars($view));
        }

        require $viewFile;
    }

    /**
     * Redirige vers une URL (paramètre ?page=).
     */
    protected function redirect(string $page, array $params = []): never
    {
        $url = APP_URL . '/index.php?page=' . $page;
        foreach ($params as $key => $val) {
            $url .= '&' . urlencode($key) . '=' . urlencode($val);
        }
        header("Location: $url");
        exit();
    }

    /**
     * Protège une action : redirige vers login si non connecté.
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', "Vous devez être connecté pour accéder à cette page.");
            $this->redirect('login');
        }
    }

    /**
     * Protège une action : redirige si l'utilisateur N'EST PAS admin.
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (Session::get('role') !== 'admin') {
            http_response_code(403);
            die("<h1>403 — Accès refusé</h1><p><a href='" . APP_URL . "?page=home'>Retour à l'accueil</a></p>");
        }
    }

    /**
     * Redirige si l'utilisateur est DÉJÀ connecté.
     */
    protected function redirectIfLoggedIn(string $page = 'home'): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect($page);
        }
    }

    /**
     * Vérifie le token CSRF — à appeler en début de chaque action POST.
     */
    protected function verifyCsrf(): void
    {
        Session::verifyCsrf();
    }

    /**
     * Génère un champ CSRF caché prêt à insérer dans un formulaire.
     */
    protected function csrfField(): string
    {
        $token = Session::generateCsrf();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Retourne la méthode HTTP courante.
     */
    protected function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Retourne true si la requête est un POST.
     */
    protected function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * Récupère une valeur POST nettoyée.
     */
    protected function post(string $key, string $default = ''): string
    {
        return trim($_POST[$key] ?? $default);
    }

    /**
     * Récupère une valeur GET nettoyée.
     */
    protected function get(string $key, string $default = ''): string
    {
        return trim($_GET[$key] ?? $default);
    }
}
