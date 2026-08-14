# AGENTS.md — CarReserve / VehicleReservation
> Généré le : $(Get-Date -Format "dd/MM/yyyy HH:mm")  
> Chemin racine : `C:\Users\pc\Desktop\XAMPP\htdocs\projet-stage`  
> Serveur : XAMPP (Apache + MariaDB 10.4.32) — PHP 8.2.12

---

## Table des matières
1. [Vue d'ensemble du projet](#1-vue-densemble)
2. [Architecture et structure des dossiers](#2-architecture)
3. [Configuration](#3-configuration)
4. [Couche Core (noyau MVC)](#4-core)
5. [Modèles (Models)](#5-models)
6. [Contrôleurs (Controllers)](#6-controllers)
7. [Vues (Views)](#7-views)
8. [CSS / Assets](#8-assets)
9. [Base de données](#9-base-de-données)
10. [Flux de navigation (routing)](#10-routing)
11. [Sécurité — état actuel](#11-sécurité)
12. [État du projet et lacunes identifiées](#12-état-du-projet)
13. [Conventions de code](#13-conventions)
14. [Tâches prioritaires pour un agent IA](#14-tâches-agent)

---

## 1. Vue d'ensemble

**Nom commercial :** CarReserve (affiché) / VehicleReservation (APP_NAME config)  
**Type :** Application web PHP MVC maison — système de location/réservation de véhicules  
**Devise monétaire :** FCFA (marché Afrique centrale, ex. Douala)  
**Thème UI :** Dark — Noir (#0B0B0F) + Orange (#F97316) + Bleu (#3B82F6) — Police Inter  
**Langue :** Français intégral (UI + code + commentaires)

### Fonctionnalités implémentées
| Fonctionnalité | État |
|---|---|
| Inscription utilisateur (client) | ✅ Complet |
| Connexion / Déconnexion | ✅ Complet |
| Remember me (cookie 30j) | ✅ Complet |
| Page d'accueil avec véhicules en vedette | ✅ Complet |
| Catalogue véhicules avec filtres | ✅ Complet |
| Détail véhicule + formulaire réservation | ✅ Complet |
| Calcul du montant total (JS client-side) | ✅ Complet |
| Mes réservations (liste) | ✅ Complet |
| Annulation réservation | ✅ Complet |
| Messages flash (succès / erreur) | ✅ Complet |
| Page 404 | ✅ Complet |
| CSRF protection (tous les formulaires POST) | ✅ Complet |
| Espace admin — tableau de bord | ✅ Complet |
| Espace admin — CRUD véhicules + upload photo | ✅ Complet |
| Espace admin — gestion réservations | ✅ Complet |
| Espace admin — gestion clients + rôles | ✅ Complet |
| Upload d'images véhicule | ✅ Complet (public/assets/uploads/) |
| Mot de passe oublié | ❌ Non implémenté (lien mort) |
| Connexion sociale (Google/Facebook/GitHub) | ❌ Non implémenté (boutons factices) |
| Profil utilisateur éditable | ❌ Non implémenté |
| Pagination catalogue | ❌ Non implémenté |

---

## 2. Architecture

### Structure des dossiers
```
projet-stage/
├── .git/                          ← Dépôt Git initialisé
├── admin/                         ← VIDE — espace admin prévu mais non créé
├── app/
│   ├── controllers/
│   │   ├── AuthController.php     ← Login, register, logout
│   │   ├── HomeController.php     ← Page d'accueil
│   │   ├── ReservationController.php ← Mes réservations, annulation
│   │   └── VehiculeController.php ← Catalogue, détail, réserver
│   ├── models/
│   │   ├── ReservationModel.php   ← CRUD table reservation + concerner
│   │   ├── UserModel.php          ← CRUD table utilisateur
│   │   └── VehiculeModel.php      ← CRUD table vehicule + categorie_vehicule
│   └── views/
│       ├── auth/
│       │   ├── login.php          ← Formulaire connexion (pleine page)
│       │   └── register.php       ← Formulaire inscription (pleine page)
│       ├── errors/
│       │   └── 404.php            ← Page erreur 404
│       ├── home/
│       │   └── index.php          ← Accueil : hero ou banner + véhicules vedette
│       ├── layouts/
│       │   ├── header.php         ← <!DOCTYPE> + <head> + lien CSS global
│       │   ├── navbar.php         ← Barre de navigation sticky
│       │   └── footer.php         ← Footer + fermeture </body></html>
│       ├── reservations/
│       │   └── index.php          ← Tableau des réservations du client
│       └── vehicules/
│           ├── detail.php         ← Fiche véhicule + formulaire de réservation
│           └── index.php          ← Grille véhicules + filtres
├── assets/                        ← CSS SOURCES (utilisés par les vues via APP_URL)
│   ├── style.css                  ← CSS global (navbar, cards, forms, table…)
│   ├── styleacceuil.css           ← VIDE
│   ├── styleinscription.css       ← CSS page inscription
│   └── stylelogin.css             ← CSS page login
├── config/
│   └── config.php                 ← Constantes BDD + APP_NAME + APP_URL
├── core/
│   ├── Controller.php             ← Classe abstraite de base
│   ├── Database.php               ← Singleton MySQLi
│   ├── Router.php                 ← Routeur GET ?page=
│   └── Session.php                ← Gestion session + flash messages
├── includes/                      ← VIDE
├── public/
│   ├── .htaccess                  ← mod_rewrite → index.php (QSA,L)
│   ├── index.php                  ← Front controller unique
│   └── assets/
│       ├── style.css              ← Copie de assets/style.css
│       ├── styleinscription.css   ← Copie de assets/styleinscription.css
│       ├── stylelogin.css         ← Copie de assets/stylelogin.css
│       └── uploads/               ← VIDE — images véhicules attendues ici
└── utilisateur.sql                ← Dump SQL : table utilisateur seulement
```

### Pattern MVC
- **Entrée unique :** `public/index.php` (front controller)
- **Routage :** Paramètre GET `?page=` → `Router::dispatch()` → contrôleur/action
- **Rendu :** `Controller::render()` injecte header + navbar + vue + footer  
  `Controller::renderPartial()` pour les pages auth (sans layout)
- **Pas d'autoloader** : chaque contrôleur/model est `require_once` manuellement

---

## 3. Configuration

**Fichier :** `config/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // ← Pas de mot de passe (dev local)
define('DB_NAME', 'location_véhicule');  // ← Nom avec accent !

define('APP_NAME', 'VehicleReservation');
define('APP_URL',  'http://localhost/projet-stage/public');
define('APP_ROOT', dirname(__DIR__));   // Défini dans index.php avant inclusion

define('TABLE_USERS',        'utilisateur');
define('TABLE_VEHICULES',    'vehicule');
define('TABLE_RESERVATIONS', 'reservation');
```

> ⚠️ Le nom de base de données contient un accent (`location_véhicule`). À surveiller selon l'encodage OS.

---

## 4. Core

### `core/Database.php` — Singleton MySQLi
- Pattern Singleton strict (`private __construct`, `private __clone`)
- `mysqli_report(MYSQLI_REPORT_OFF)` — erreurs gérées manuellement
- Charset `utf8mb4`
- `SET sql_mode = ''` pour désactiver le mode strict SQL (accepte les NULL)
- Log d'erreur via `error_log()` + `die()` si connexion impossible

**Méthode publique :** `Database::getInstance(): mysqli`

---

### `core/Session.php` — Gestion de session
| Méthode | Description |
|---|---|
| `Session::start()` | Démarre la session si inactive |
| `Session::setUser(array $user)` | Stocke l'utilisateur en session + `session_regenerate_id(true)` |
| `Session::isLoggedIn(): bool` | Vérifie `$_SESSION['logged_in']` |
| `Session::isAdmin(): bool` | Vérifie `role === 'admin'` |
| `Session::fullName(): string` | Retourne prénom + nom |
| `Session::destroy()` | Détruit session + cookie |
| `Session::setFlash(type, message)` | Flash message (success/error/warning/info) |
| `Session::getFlash(): string` | Retourne HTML des flash + les efface |
| `Session::get(key)` | Lecture générique `$_SESSION[key]` |
| `Session::set(key, value)` | Écriture générique |

**Clés stockées en session :**
```
user_id   → ID_UTILSATEUR
nom       → NOM
prenom    → PRENOM
email     → ADRESSE_EMAIL
role      → ROLE ('client' | 'admin')
logged_in → true
flash     → array[type => message]
```

---

### `core/Router.php` — Routeur
- Enregistrement : `$router->add('page-slug', 'ControllerClass', 'methodName')`
- Dispatch : lit `$_GET['page']` (défaut `'home'`)
- Charge le fichier controller via `APP_ROOT . '/app/controllers/' . $controller . '.php'`
- Instancie et appelle la méthode

**Routes enregistrées dans `public/index.php` :**
| `?page=` | Contrôleur | Action |
|---|---|---|
| `login` | AuthController | login |
| `register` | AuthController | register |
| `logout` | AuthController | logout |
| `home` | HomeController | index |
| `vehicules` | VehiculeController | index |
| `vehicule-detail` | VehiculeController | detail |
| `vehicule-reserver` | VehiculeController | reserver |
| `mes-reservations` | ReservationController | index |
| `reservation-annuler` | ReservationController | annuler |

> ❌ Route `admin-dashboard` référencée dans `AuthController::login()` mais **non enregistrée** dans le router → erreur 404 si un admin se connecte.

---

### `core/Controller.php` — Classe de base
```
Controller (abstract)
├── render(view, data)          ← header + navbar + vue + footer
├── renderPartial(view, data)   ← vue seule (auth)
├── redirect(page, params)      ← header Location + exit
├── requireLogin()              ← redirige vers login si non connecté
├── requireAdmin()              ← redirige/403 si pas admin
├── redirectIfLoggedIn(page)    ← évite double login
├── method(): string            ← GET | POST
├── isPost(): bool
├── post(key, default): string  ← trim($_POST[key])
└── get(key, default): string   ← trim($_GET[key])
```

---

## 5. Models

### `app/models/UserModel.php`
**Table :** `utilisateur`

| Colonne | Type | Notes |
|---|---|---|
| ID_UTILSATEUR | int PK AI | ⚠️ Typo dans le nom (manque un 'I') |
| NOM | varchar(100) | |
| PRENOM | varchar(100) | |
| ADRESSE_EMAIL | varchar(255) | |
| MOT_DE_PASSE | varchar(200) | bcrypt |
| NUMERO_DE_TELEPHONE | varchar(9) | Optionnel, 9 chiffres |
| ROLE | longtext | 'client' \| 'admin' |
| DATE_D_INSCRIPTION | datetime | |

**Méthodes :**
| Méthode | Description |
|---|---|
| `findByEmail(string): ?array` | Recherche par email (login) |
| `findById(int): ?array` | Recherche par ID |
| `emailExists(string): bool` | Vérification unicité email |
| `create(array): bool` | Insertion + bcrypt password |
| `update(int, array): bool` | Mise à jour NOM/PRENOM/TELEPHONE |
| `updatePassword(int, string): bool` | Changement mot de passe |

---

### `app/models/VehiculeModel.php`
**Table :** `vehicule`

| Colonne | Type | Notes |
|---|---|---|
| ID_VEHICULE | int PK AI | |
| ID_CATEGORIE | int FK | → categorie_vehicule |
| ID_AGENCE | int FK | → table agence (non documentée) |
| MARQUE | varchar | |
| MODELE | varchar | |
| IMMATRICULATION | varchar | |
| ANNEE | date/varchar | Utilisé avec `strtotime()` |
| NOMBRE_PLACES | int | |
| TRANSMISSION | varchar | 'Manuelle' \| 'Automatique' |
| CARBURANT | varchar | |
| TARIF_JOUR | decimal | En FCFA |
| STATUT_DISPONIBLE | varchar | 'disponible' \| 'loue' \| 'maintenance' |
| IMAGE_PRINCIPALE | varchar | Nom de fichier dans /uploads/ |
| HEURE | ? | Colonne présente, usage inconnu |

**Méthodes :**
| Méthode | Description |
|---|---|
| `getDisponibles(int limit): array` | Véhicules disponibles pour l'accueil |
| `search(array filtres): array` | Recherche avec filtres optionnels |
| `findById(int): ?array` | Fiche véhicule avec catégorie |
| `getCategories(): array` | Catégories disponibles (pour filtres) |
| `updateStatut(int, string): bool` | Passe disponible/loue/maintenance |
| `create(array): bool` | Insertion (usage admin prévu) |
| `update(int, array): bool` | Mise à jour (usage admin prévu) |
| `delete(int): bool` | Suppression (usage admin prévu) |

**Table liée :** `categorie_vehicule` (ID_CATEGORIE, NOM_CATEGORIE)

---

### `app/models/ReservationModel.php`
**Table principale :** `reservation`  
**Table de liaison :** `concerner` (ID_VEHICULE, ID_RESERVATION)

| Colonne | Type | Notes |
|---|---|---|
| ID_RESERVATION | int PK AI | |
| ID_UTILSATEUR | int FK | → utilisateur (typo conservée) |
| DATE_DEBUT | date | |
| DATE_FIN | date | |
| LIEU_PRISE__EN_CHARGE | varchar | Double underscore dans le nom |
| LIEU_RETOUR | varchar | |
| OPTION_VALIDATION | ? | Présent, non utilisé côté code |
| MONTANT_TOTAL | decimal | Calculé : jours × tarif_jour |
| STATUT | varchar | 'confirmee' \| 'annulee' \| 'terminee' |
| DATEL_CREATION | datetime | Typo (DATEL au lieu de DATE) |

**Méthodes :**
| Méthode | Description |
|---|---|
| `create(array): bool` | Insère réservation + liaison concerner |
| `findByClient(int): array` | Toutes les réservations d'un client (JOIN véhicule) |
| `findById(int): ?array` | Réservation par ID + ID_VEHICULE via concerner |
| `findAll(): array` | Toutes les réservations (admin, non appelé) |
| `updateStatut(int, string): bool` | Mise à jour statut |

---

## 6. Controllers

### `AuthController`
**Dépendance :** `UserModel`

**`login()`**
- Bloque si déjà connecté (`redirectIfLoggedIn`)
- POST : valide email + password → `password_verify()` contre le hash bcrypt
- Cookie `remember_email` (30 jours, httpOnly)
- Redirige vers `admin-dashboard` si role=admin (⚠️ route inexistante), sinon `home`
- Rendu : `renderPartial('auth/login')`

**`register()`**
- POST : valide nom, prénom, email (FILTER_VALIDATE_EMAIL), password ≥ 8 chars, confirmation, téléphone 9 chiffres
- Vérifie unicité email avant insertion
- Redirige vers `login` après succès
- Rendu : `renderPartial('auth/register')`

**`logout()`**
- `Session::destroy()` + efface cookie remember
- Redirige vers `login`

---

### `HomeController`
**Dépendance :** `VehiculeModel`

**`index()`**
- Charge 6 véhicules disponibles (`getDisponibles(6)`)
- Vue conditionnelle : banner de bienvenue si connecté, hero landing sinon
- Rendu : `render('home/index')`

---

### `VehiculeController`
**Dépendances :** `VehiculeModel`, `ReservationModel`

**`index()`** — Catalogue avec filtres GET (categorie, prix_max, transmission)

**`detail()`** — Fiche véhicule ; 404 si ID invalide ou véhicule non trouvé

**`reserver()`** — POST uniquement ; requiert login
- Validations : vehicule_id > 0, dates non vides, date_fin > date_debut, date_debut ≥ aujourd'hui
- Vérifie statut 'disponible' du véhicule
- Calcule montant : `ceil((fin - debut) / 86400) × tarif_jour`
- Insère réservation + passe véhicule en 'loue'
- Redirige vers `mes-reservations`

---

### `ReservationController`
**Dépendances :** `ReservationModel`, `VehiculeModel`

**`index()`** — Liste des réservations du client connecté

**`annuler()`** — GET avec `?id=`
- Vérifie que la réservation appartient bien au client connecté
- Vérifie que le statut est 'confirmee'
- Passe en 'annulee' + remet le véhicule en 'disponible'

---

## 7. Views

### Layouts
| Fichier | Rôle |
|---|---|
| `layouts/header.php` | `<!DOCTYPE html>` → `<body>` ouvert ; charge `style.css` |
| `layouts/navbar.php` | Navbar sticky ; affiche nom utilisateur si connecté |
| `layouts/footer.php` | Footer + fermeture `</body></html>` |

### Pages Auth (renderPartial — sans layout)
| Vue | CSS chargé | Contenu |
|---|---|---|
| `auth/login.php` | `stylelogin.css` | Email + password + remember me + toggle visibilité |
| `auth/register.php` | `styleinscription.css` | Nom, prénom, email, téléphone, password ×2 |

### Pages principale (render — avec layout)
| Vue | Variables injectées | Contenu |
|---|---|---|
| `home/index.php` | `vehiculesVedette[]` | Hero ou banner + grille 6 véhicules |
| `vehicules/index.php` | `vehicules[]`, `categories[]`, `filtres[]` | Filtres + grille catalogue |
| `vehicules/detail.php` | `vehicule{}` | Photo + specs + formulaire réservation |
| `reservations/index.php` | `reservations[]` | Tableau avec statuts badgés + bouton annuler |
| `errors/404.php` | — | Page 404 stylée |

### Sécurité dans les vues
- Toutes les sorties utilisateur passent par `htmlspecialchars()`
- Les IDs numériques sont castés `(int)`
- `novalidate` sur les formulaires (validation côté PHP)

---

## 8. Assets

### Arborescence CSS
```
assets/                         public/assets/
├── style.css          ←→       ├── style.css         (copie identique)
├── styleinscription.css ←→     ├── styleinscription.css
├── stylelogin.css     ←→       └── stylelogin.css
└── styleacceuil.css             (VIDE — fichier vide)
```
> Les vues référencent `<?= APP_URL ?>/assets/...` → pointe vers `public/assets/`.  
> Les fichiers dans `assets/` (racine) sont des doublons non servis directement.

### Variables CSS (style.css)
```css
--orange: #F97316        --orange-hover: #EA6C0A    --orange-glow: rgba(249,115,22,0.25)
--blue: #3B82F6          --blue-hover: #2563EB      --blue-glow: rgba(59,130,246,0.25)
--bg: #0B0B0F            --bg-card: #111117          --bg-surface: #18181F
--border: #252530        --text: #F1F1F3             --text-muted: #6B6B7A
--success: #22C55E       --error: #EF4444
--radius-sm: 8px         --radius: 12px              --radius-lg: 18px
```

### Composants CSS disponibles
- `.btn`, `.btn-primary`, `.btn-blue`, `.btn-outline`, `.btn-ghost`, `.btn-danger`
- `.btn-sm`, `.btn-lg`, `.btn-xl`
- `.alert`, `.alert-success`, `.alert-error`, `.alert-warning`, `.alert-info`
- `.vehicule-card`, `.card-img`, `.card-body`, `.card-badge`, `.card-specs`, `.card-footer`
- `.detail-grid`, `.specs-grid`, `.spec-box`
- `.filtres-wrap`, `.filtres-grid`
- `.table-wrap`, `thead th`, `tbody td`
- `.badge`, `.badge-success`, `.badge-error`, `.badge-neutral`, `.badge-blue`
- `.empty-state`
- `.hero`, `.hero-glow`, `.hero-stats`, `.stat`
- `.welcome-banner`
- `.page-404`
- `.navbar`, `.nav-brand`, `.nav-link`, `.nav-user`
- `.footer`, `.footer-inner`, `.footer-nav`
- Responsive : breakpoints 960px, 768px, 480px

---

## 9. Base de données

### Tables connues (depuis le code)
| Table | Fichier SQL fourni | Utilisée dans |
|---|---|---|
| `utilisateur` | ✅ utilisateur.sql | UserModel |
| `vehicule` | ❌ manquant | VehiculeModel |
| `categorie_vehicule` | ❌ manquant | VehiculeModel |
| `reservation` | ❌ manquant | ReservationModel |
| `concerner` | ❌ manquant | ReservationModel |
| `agence` | ❌ manquant | VehiculeModel (ID_AGENCE) |

### Table `utilisateur` (seul dump disponible)
```sql
CREATE TABLE utilisateur (
  ID_UTILSATEUR        int(11)      NOT NULL AUTO_INCREMENT,
  NOM                  varchar(100) DEFAULT NULL,
  PRENOM               varchar(100) DEFAULT NULL,
  ADRESSE_EMAIL        varchar(255) DEFAULT NULL,
  MOT_DE_PASSE         varchar(200) DEFAULT NULL,
  NUMERO_DE_TELEPHONE  varchar(9)   DEFAULT NULL,
  ROLE                 longtext     DEFAULT NULL,
  DATE_D_INSCRIPTION   datetime     DEFAULT NULL,
  PRIMARY KEY (ID_UTILSATEUR)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```
> Seul un enregistrement de test existe (ID=8, toutes les valeurs vides).

### Contraintes notées
- `ROLE` est `longtext` au lieu d'un `ENUM('client','admin')` — risque de valeur incohérente
- `NUMERO_DE_TELEPHONE` est `varchar(9)` — cohérent avec la validation PHP
- Pas de contrainte UNIQUE sur `ADRESSE_EMAIL` en BDD (uniquement vérifiée en PHP)
- Pas de FOREIGN KEY explicites dans le dump fourni

---

## 10. Routing

### Flux complet d'une requête
```
Navigateur → http://localhost/projet-stage/public/?page=vehicules
     │
     ↓
public/.htaccess  →  RewriteRule → index.php [QSA,L]
     │
     ↓
public/index.php
  ├── define('APP_ROOT')
  ├── require config/config.php
  ├── require core/Session.php  → Session::start()
  ├── require core/Database.php
  ├── require core/Controller.php
  ├── require core/Router.php
  ├── $router->add(...)  × 9 routes
  └── $router->dispatch()
         │
         ↓
  Router::dispatch()
    ├── lit $_GET['page'] = 'vehicules'
    ├── trouve la route → VehiculeController::index
    ├── require app/controllers/VehiculeController.php
    │     └── require app/models/VehiculeModel.php
    │           └── require app/models/ReservationModel.php
    ├── new VehiculeController()
    └── ->index()
           ├── $this->vehiculeModel->search($filtres)
           └── $this->render('vehicules/index', [...])
                  ├── require views/layouts/header.php
                  ├── require views/layouts/navbar.php
                  ├── require views/vehicules/index.php
                  └── require views/layouts/footer.php
```

---

## 11. Sécurité — état actuel

### Points positifs ✅
| Mesure | Implémentation |
|---|---|
| Mots de passe hachés | `password_hash()` / `password_verify()` — bcrypt |
| Requêtes préparées | `$stmt->prepare()` + `bind_param()` sur toutes les requêtes utilisateur |
| XSS : échappement HTML | `htmlspecialchars()` sur toutes les sorties |
| Fixation de session | `session_regenerate_id(true)` à la connexion |
| Cookie httpOnly | `setcookie(..., true)` pour remember_email |
| Validation serveur | Toutes les entrées validées côté PHP |
| Contrôle d'accès | `requireLogin()` sur toutes les actions protégées |
| Vérification propriété | ReservationController vérifie `ID_UTILSATEUR === clientId` |

### Risques identifiés ⚠️
| Risque | Détail |
|---|---|
| CSRF | Aucun token CSRF sur les formulaires POST |
| Mot de passe en clair dans l'URL | Non — les formulaires utilisent POST |
| Credentials exposés | `config.php` n'est pas protégé si `public/` est mal configuré |
| HTTPS | Non configuré (localhost dev) |
| Rate limiting | Aucune protection contre brute-force login |
| Upload d'images | Non implémenté — à sécuriser lors de l'ajout |
| ROLE en longtext | Pas de contrainte BDD — une valeur corrompue bypasse `requireAdmin()` |
| Route admin manquante | Un admin se connectant obtient une 404 |
| `SET sql_mode = ''` | Désactive les erreurs SQL strictes — peut masquer des bugs |

---

## 12. État du projet

### Ce qui fonctionne
- Architecture MVC propre et cohérente
- Inscription, connexion, déconnexion complets avec validation
- Catalogue véhicules avec filtres multi-critères
- Réservation complète avec calcul dynamique JS
- Historique et annulation des réservations
- Design system dark complet et responsive

### Ce qui manque / est cassé
| Problème | Priorité | Impact |
|---|---|---|
| Route `admin-dashboard` absente | ✅ Résolu le 14/08/2026 | — |
| Espace admin entièrement vide | ✅ Résolu le 14/08/2026 | — |
| SQL des tables véhicule/reservation absent | 🔴 Critique | Impossible de recréer la BDD complète |
| CSRF protection | ✅ Résolu le 14/08/2026 | — |
| Upload images non implémenté | ✅ Résolu le 14/08/2026 | — |
| Contrainte UNIQUE email manquante en BDD | 🟠 Important | Race condition possible |
| `styleacceuil.css` vide | 🟡 Mineur | Fichier orphelin |
| Doublons CSS dans assets/ et public/assets/ | 🟡 Mineur | Maintenance : copier après chaque modif CSS |
| Pagination absente | 🟡 Mineur | UX dégradée avec beaucoup de véhicules |
| Mot de passe oublié non implémenté | 🟡 Mineur | Lien mort affiché |
| Boutons social login factices | 🟡 Mineur | Liens morts affichés |
| `APP_NAME` incohérence | ✅ Résolu — uniformisé à 'CarReserve' |

---

## 13. Conventions de code

### Nommage
- **Classes :** PascalCase (`UserModel`, `AuthController`)
- **Méthodes :** camelCase (`findByEmail`, `getDisponibles`)
- **Variables PHP :** camelCase (`$vehiculeId`, `$dateDebut`)
- **Colonnes BDD :** UPPER_SNAKE_CASE (`ID_UTILSATEUR`, `ADRESSE_EMAIL`)
- **Routes :** kebab-case (`vehicule-detail`, `mes-reservations`)
- **Fichiers PHP :** PascalCase pour classes, kebab-case pour vues

### Patterns utilisés
- Singleton pour Database
- Abstract base class pour Controller
- Static methods pour Session (facade pattern)
- Prepared statements MySQLi pour tout accès BDD

### Ce que le code N'utilise PAS
- Autoloader PSR-4 / Composer
- Namespaces PHP
- Interfaces ou traits
- PDO (utilise MySQLi)
- Template engine (pas de Twig/Blade — PHP natif)
- `.env` pour la configuration

---

## 14. Tâches prioritaires pour un agent IA

### Avant toute modification
1. Lire `config/config.php` pour les constantes globales
2. Lire `core/Controller.php` pour les méthodes de base disponibles
3. Lire `core/Session.php` pour les clés de session disponibles
4. Vérifier que les tables BDD existent avant d'ajouter des requêtes

### Tâche 1 — Corriger la route admin (critique)
```php
// Dans public/index.php, ajouter :
$router->add('admin-dashboard', 'AdminController', 'dashboard');
// Et créer app/controllers/AdminController.php
```

### Tâche 2 — Ajouter les tokens CSRF
```php
// Dans Session.php : Session::generateCsrf(), Session::verifyCsrf()
// Dans chaque formulaire POST : <input type="hidden" name="csrf_token" ...>
// Dans chaque action POST du controller : $this->verifyCsrf()
```

### Tâche 3 — Créer le dump SQL complet
Les tables manquantes à créer : `vehicule`, `categorie_vehicule`, `reservation`, `concerner`, `agence`

### Tâche 4 — Implémenter l'upload d'images
- Cible : `public/assets/uploads/`
- Valider type MIME (image/jpeg, image/png, image/webp)
- Renommer en hash unique pour éviter les collisions
- Ajouter dans `VehiculeModel::create()` et `update()`

### Tâche 5 — Espace admin
Créer `app/controllers/AdminController.php` avec :
- `dashboard()` : stats (nb véhicules, réservations, clients)
- `vehicules()` : CRUD véhicules
- `reservations()` : liste toutes les réservations
- `clients()` : liste utilisateurs

Toutes les actions doivent appeler `$this->requireAdmin()`.

### Pour ajouter une nouvelle page
1. Créer la vue dans `app/views/<dossier>/<page>.php`
2. Ajouter la route dans `public/index.php` : `$router->add(...)`
3. Ajouter la méthode dans le contrôleur approprié
4. Appeler `$this->render('<dossier>/<page>', $data)` ou `renderPartial()`

### Pour ajouter un nouveau modèle
1. Créer `app/models/NomModel.php`
2. Injecter `$this->db = Database::getInstance()` dans le constructeur
3. Utiliser `$this->db->prepare()` + `bind_param()` pour toutes les requêtes
4. `require_once APP_ROOT . '/app/models/NomModel.php'` dans le contrôleur qui l'utilise

---

*Fin du fichier AGENTS.md*
