<?php

/**
 * Database — Singleton MySQLi avec gestion d'erreurs complète.
 */
class Database
{
    private static ?mysqli $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): mysqli
    {
        if (self::$instance === null) {

            // Désactiver les exceptions MySQLi pour gérer les erreurs manuellement
            mysqli_report(MYSQLI_REPORT_OFF);

            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_errno) {
                error_log("DB connect error [{$conn->connect_errno}]: {$conn->connect_error}");
                die("Service temporairement indisponible.");
            }

            $conn->set_charset('utf8mb4');
            // Désactiver le mode strict pour éviter les rejets sur champs NULL
            $conn->query("SET sql_mode = ''");

            self::$instance = $conn;
        }

        return self::$instance;
    }
}
