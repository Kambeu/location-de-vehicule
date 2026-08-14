<?php
/**
 * public/index.php — Front Controller unique
 */

// Afficher toutes les erreurs PHP pour déboguer
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Désactiver les exceptions MySQLi (gestion manuelle)
mysqli_report(MYSQLI_REPORT_OFF);

define('APP_ROOT', dirname(__DIR__));

// ===== Configuration =====
require_once APP_ROOT . '/config/config.php';

// ===== Session =====
require_once APP_ROOT . '/core/Session.php';
Session::start();

// ===== Noyau =====
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Router.php';

// ===== Routes =====
$router = new Router();

$router->add('login',               'AuthController',        'login');
$router->add('register',            'AuthController',        'register');
$router->add('logout',              'AuthController',        'logout');
$router->add('home',                'HomeController',        'index');
$router->add('vehicules',           'VehiculeController',    'index');
$router->add('vehicule-detail',     'VehiculeController',    'detail');
$router->add('vehicule-reserver',   'VehiculeController',    'reserver');
$router->add('mes-reservations',    'ReservationController', 'index');
$router->add('reservation-annuler', 'ReservationController', 'annuler');

// ===== Dispatch =====
$router->dispatch();
