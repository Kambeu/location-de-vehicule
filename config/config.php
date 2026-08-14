<?php

// ===== BASE DE DONNÉES =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'location_véhicule');

// ===== APPLICATION =====
// APP_URL pointe vers le dossier public/ du projet
define('APP_NAME', 'CarReserve');
define('APP_URL',  'http://localhost/projet-stage/public');
// APP_ROOT est déjà défini dans public/index.php avant l'inclusion de ce fichier
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ===== TABLES =====
define('TABLE_USERS',        'utilisateur');
define('TABLE_VEHICULES',    'vehicule');
define('TABLE_RESERVATIONS', 'reservation');
