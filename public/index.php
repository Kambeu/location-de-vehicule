<?php
/**
 * public/index.php — Front Controller unique
 */

// En production, mettre display_errors à 0
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

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

// Auth
$router->add('login',    'AuthController', 'login');
$router->add('register', 'AuthController', 'register');
$router->add('logout',   'AuthController', 'logout');

// Accueil
$router->add('home', 'HomeController', 'index');

// Véhicules
$router->add('vehicules',          'VehiculeController', 'index');
$router->add('vehicule-detail',    'VehiculeController', 'detail');
$router->add('vehicule-reserver',  'VehiculeController', 'reserver');

// Espace client
$router->add('mes-reservations',   'ReservationController', 'index');
$router->add('reservation-annuler','ReservationController', 'annuler');

// Admin
$router->add('admin-dashboard',           'AdminController', 'dashboard');
$router->add('admin-vehicules',           'AdminController', 'vehicules');
$router->add('admin-vehicule-ajouter',    'AdminController', 'ajouterVehicule');
$router->add('admin-vehicule-store',      'AdminController', 'storeVehicule');
$router->add('admin-vehicule-editer',     'AdminController', 'editerVehicule');
$router->add('admin-vehicule-update',     'AdminController', 'updateVehicule');
$router->add('admin-vehicule-supprimer',  'AdminController', 'supprimerVehicule');
$router->add('admin-vehicule-statut',     'AdminController', 'updateVehiculeStatut');
$router->add('admin-reservations',        'AdminController', 'reservations');
$router->add('admin-reservation-statut',  'AdminController', 'updateReservationStatut');
$router->add('admin-clients',             'AdminController', 'clients');
$router->add('admin-client-role',         'AdminController', 'updateClientRole');

// Admin — Catégories
$router->add('admin-categories',          'AdminController', 'categories');
$router->add('admin-categorie-store',     'AdminController', 'storeCategorie');
$router->add('admin-categorie-update',    'AdminController', 'updateCategorie');
$router->add('admin-categorie-supprimer', 'AdminController', 'supprimerCategorie');

// Admin — Agences
$router->add('admin-agences',             'AdminController', 'agences');
$router->add('admin-agence-store',        'AdminController', 'storeAgence');
$router->add('admin-agence-update',       'AdminController', 'updateAgence');
$router->add('admin-agence-supprimer',    'AdminController', 'supprimerAgence');
$router->add('admin-agence-statut',       'AdminController', 'updateAgenceStatut');

// ===== Dispatch =====
$router->dispatch();
