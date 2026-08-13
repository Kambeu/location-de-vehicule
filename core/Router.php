<?php

/**
 * Router — Routeur frontal simple basé sur le paramètre GET "page".
 *
 * Usage : public/index.php?page=login
 *         public/index.php?page=register
 *         public/index.php       → page d'accueil
 */
class Router
{
    /** @var array<string, array{controller: string, action: string}> */
    private array $routes = [];

    /**
     * Enregistre une route.
     *
     * @param string $page       Valeur du paramètre ?page=
     * @param string $controller Nom de la classe contrôleur
     * @param string $action     Nom de la méthode à appeler
     */
    public function add(string $page, string $controller, string $action): void
    {
        $this->routes[$page] = [
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    /**
     * Résout la route courante et dispatch vers le bon contrôleur.
     */
    public function dispatch(): void
    {
        $page = trim($_GET['page'] ?? 'home');

        if (!array_key_exists($page, $this->routes)) {
            $this->notFound();
            return;
        }

        $route      = $this->routes[$page];
        $controller = $route['controller'];
        $action     = $route['action'];

        // Charger et instancier le contrôleur
        $file = APP_ROOT . '/app/controllers/' . $controller . '.php';

        if (!file_exists($file)) {
            $this->notFound();
            return;
        }

        require_once $file;
        $ctrl = new $controller();
        $ctrl->$action();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "<h1>404 — Page introuvable</h1>";
        echo "<p><a href='" . APP_URL . "'>Retour à l'accueil</a></p>";
    }
}
