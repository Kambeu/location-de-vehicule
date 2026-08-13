<?php

/**
 * Database — Singleton MySQLi
 * Une seule connexion partagée dans toute l'application.
 */
class Database
{
    private static ?mysqli $instance = null;

    // Empêcher instanciation directe
    private function __construct() {}
    private function __clone() {}

    /**
     * Retourne la connexion unique à la base de données.
     */
    public static function getInstance(): mysqli
    {
        if (self::$instance === null) {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                // En production, loguer l'erreur et afficher un message générique
                error_log("DB connection failed: " . $conn->connect_error);
                die("Service temporairement indisponible. Veuillez réessayer plus tard.");
            }

            $conn->set_charset('utf8mb4');
            self::$instance = $conn;
        }

        return self::$instance;
    }
}
