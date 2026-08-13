<?php

// ===== DATABASE CONFIGURATION =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bd_test');

// ===== APPLICATION CONFIGURATION =====
define('APP_NAME', 'Patrimoine');
define('APP_URL',  'http://localhost/projet-stage');

// ===== START SESSION (once, globally) =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== DATABASE CONNECTION =====
$connexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}

$connexion->set_charset('utf8mb4');
