<?php

require_once APP_ROOT . '/app/models/AdminModel.php';
require_once APP_ROOT . '/app/models/VehiculeModel.php';
require_once APP_ROOT . '/app/models/UserModel.php';
require_once APP_ROOT . '/app/models/CategorieModel.php';
require_once APP_ROOT . '/app/models/AgenceModel.php';

/**
 * AdminController — Back-office complet.
 * Toutes les actions exigent le rôle 'admin'.
 */
class AdminController extends Controller
{
    private AdminModel    $adminModel;
    private VehiculeModel $vehiculeModel;
    private UserModel     $userModel;
    private CategorieModel $categorieModel;
    private AgenceModel   $agenceModel;

    public function __construct()
    {
        $this->adminModel     = new AdminModel();
        $this->vehiculeModel  = new VehiculeModel();
        $this->userModel      = new UserModel();
        $this->categorieModel = new CategorieModel();
        $this->agenceModel    = new AgenceModel();
    }

    // ================================================================
    //  TABLEAU DE BORD
    // ================================================================
    public function dashboard(): void
    {
        $this->requireAdmin();

        $stats = [
            'clients'      => $this->adminModel->countClients(),
            'vehicules'    => $this->adminModel->countVehicules(),
            'disponibles'  => $this->adminModel->countDisponibles(),
            'reservations' => $this->adminModel->countReservations(),
            'chiffre'      => $this->adminModel->chiffreAffaires(),
        ];

        $this->render('admin/dashboard', [
            'page_title' => "Tableau de bord — " . APP_NAME,
            'stats'      => $stats,
        ]);
    }

    // ================================================================
    //  VÉHICULES — Liste
    // ================================================================
    public function vehicules(): void
    {
        $this->requireAdmin();

        $this->render('admin/vehicules', [
            'page_title' => "Gestion des véhicules — " . APP_NAME,
            'vehicules'  => $this->vehiculeModel->getAll(),
        ]);
    }

    // ================================================================
    //  VÉHICULES — Formulaire ajout
    //  GET ?page=admin-vehicule-ajouter
    // ================================================================
    public function ajouterVehicule(): void
    {
        $this->requireAdmin();

        $this->render('admin/vehicule_form', [
            'page_title' => "Ajouter un véhicule — " . APP_NAME,
            'categories' => $this->vehiculeModel->getCategories(),
            'agences'    => $this->vehiculeModel->getAgences(),
            'vehicule'   => null,
            'errors'     => [],
            'old'        => [],
            'mode'       => 'ajout',
        ]);
    }

    // ================================================================
    //  VÉHICULES — Traitement ajout
    //  POST ?page=admin-vehicule-store
    // ================================================================
    public function storeVehicule(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-vehicules');
        $this->verifyCsrf();

        [$errors, $data] = $this->validateVehiculeForm();

        if (!empty($errors)) {
            $this->render('admin/vehicule_form', [
                'page_title' => "Ajouter un véhicule — " . APP_NAME,
                'categories' => $this->vehiculeModel->getCategories(),
                'agences'    => $this->vehiculeModel->getAgences(),
                'vehicule'   => null,
                'errors'     => $errors,
                'old'        => $data,
                'mode'       => 'ajout',
            ]);
            return;
        }

        // Gestion upload photo
        $data['image_principale'] = $this->handleUpload() ?? '';

        if ($this->vehiculeModel->create($data)) {
            Session::setFlash('success', "Véhicule ajouté avec succès.");
            $this->redirect('admin-vehicules');
        } else {
            Session::setFlash('error', "Erreur lors de l'ajout. Vérifiez les logs.");
            $this->redirect('admin-vehicule-ajouter');
        }
    }

    // ================================================================
    //  VÉHICULES — Formulaire édition
    //  GET ?page=admin-vehicule-editer&id=X
    // ================================================================
    public function editerVehicule(): void
    {
        $this->requireAdmin();

        $id       = (int) $this->get('id');
        $vehicule = $id > 0 ? $this->vehiculeModel->findById($id) : null;

        if (!$vehicule) {
            Session::setFlash('error', "Véhicule introuvable.");
            $this->redirect('admin-vehicules');
        }

        $this->render('admin/vehicule_form', [
            'page_title' => "Modifier le véhicule — " . APP_NAME,
            'categories' => $this->vehiculeModel->getCategories(),
            'agences'    => $this->vehiculeModel->getAgences(),
            'vehicule'   => $vehicule,
            'errors'     => [],
            'old'        => [],
            'mode'       => 'edition',
        ]);
    }

    // ================================================================
    //  VÉHICULES — Traitement édition
    //  POST ?page=admin-vehicule-update
    // ================================================================
    public function updateVehicule(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-vehicules');
        $this->verifyCsrf();

        $id       = (int) $this->post('vehicule_id');
        $vehicule = $id > 0 ? $this->vehiculeModel->findById($id) : null;

        if (!$vehicule) {
            Session::setFlash('error', "Véhicule introuvable.");
            $this->redirect('admin-vehicules');
        }

        [$errors, $data] = $this->validateVehiculeForm();

        if (!empty($errors)) {
            $this->render('admin/vehicule_form', [
                'page_title' => "Modifier le véhicule — " . APP_NAME,
                'categories' => $this->vehiculeModel->getCategories(),
                'agences'    => $this->vehiculeModel->getAgences(),
                'vehicule'   => $vehicule,
                'errors'     => $errors,
                'old'        => $data,
                'mode'       => 'edition',
            ]);
            return;
        }

        // Upload nouvelle photo ou conserver l'ancienne
        $nouvellePhoto = $this->handleUpload();
        $data['image_principale'] = $nouvellePhoto ?? $vehicule['IMAGE_PRINCIPALE'];

        if ($this->vehiculeModel->update($id, $data)) {
            Session::setFlash('success', "Véhicule mis à jour avec succès.");
        } else {
            Session::setFlash('error', "Erreur lors de la mise à jour.");
        }
        $this->redirect('admin-vehicules');
    }

    // ================================================================
    //  VÉHICULES — Suppression
    //  GET ?page=admin-vehicule-supprimer&id=X
    // ================================================================
    public function supprimerVehicule(): void
    {
        $this->requireAdmin();

        $id = (int) $this->get('id');

        if ($id > 0 && $this->vehiculeModel->delete($id)) {
            Session::setFlash('success', "Véhicule supprimé.");
        } else {
            Session::setFlash('error', "Suppression impossible.");
        }
        $this->redirect('admin-vehicules');
    }

    // ================================================================
    //  VÉHICULES — Changement de statut rapide
    //  POST ?page=admin-vehicule-statut
    // ================================================================
    public function updateVehiculeStatut(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-vehicules');
        $this->verifyCsrf();

        $id     = (int) $this->post('vehicule_id');
        $statut = $this->post('statut');

        $allowed = ['disponible', 'loue', 'maintenance'];
        if ($id > 0 && in_array($statut, $allowed)) {
            $this->vehiculeModel->updateStatut($id, $statut);
            Session::setFlash('success', "Statut mis à jour.");
        } else {
            Session::setFlash('error', "Données invalides.");
        }
        $this->redirect('admin-vehicules');
    }

    // ================================================================
    //  RÉSERVATIONS — Liste complète
    //  GET ?page=admin-reservations
    // ================================================================
    public function reservations(): void
    {
        $this->requireAdmin();

        $this->render('admin/reservations', [
            'page_title'   => "Gestion des réservations — " . APP_NAME,
            'reservations' => $this->adminModel->getAllReservations(),
        ]);
    }

    // ================================================================
    //  RÉSERVATIONS — Changer statut
    //  POST ?page=admin-reservation-statut
    // ================================================================
    public function updateReservationStatut(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-reservations');
        $this->verifyCsrf();

        $id     = (int) $this->post('reservation_id');
        $statut = $this->post('statut');

        $allowed = ['confirmee', 'annulee', 'terminee'];
        if ($id > 0 && in_array($statut, $allowed)) {
            $this->adminModel->updateReservationStatut($id, $statut);

            // Si annulée, remettre le véhicule disponible
            if ($statut === 'annulee') {
                $resa = $this->adminModel->getReservationById($id);
                if ($resa && !empty($resa['ID_VEHICULE'])) {
                    $this->vehiculeModel->updateStatut((int) $resa['ID_VEHICULE'], 'disponible');
                }
            }
            // Si terminée, remettre le véhicule disponible
            if ($statut === 'terminee') {
                $resa = $this->adminModel->getReservationById($id);
                if ($resa && !empty($resa['ID_VEHICULE'])) {
                    $this->vehiculeModel->updateStatut((int) $resa['ID_VEHICULE'], 'disponible');
                }
            }

            Session::setFlash('success', "Statut de la réservation mis à jour.");
        } else {
            Session::setFlash('error', "Données invalides.");
        }
        $this->redirect('admin-reservations');
    }

    // ================================================================
    //  CLIENTS
    // ================================================================
    public function clients(): void
    {
        $this->requireAdmin();

        $this->render('admin/clients', [
            'page_title' => "Gestion des clients — " . APP_NAME,
            'clients'    => $this->adminModel->getAllClients(),
        ]);
    }

    public function updateClientRole(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-clients');
        $this->verifyCsrf();

        $id   = (int) $this->post('client_id');
        $role = $this->post('role');

        if ($id > 0 && in_array($role, ['client', 'admin'])) {
            if ($id === (int) Session::get('user_id')) {
                Session::setFlash('error', "Vous ne pouvez pas modifier votre propre rôle.");
            } else {
                $this->adminModel->updateRole($id, $role);
                Session::setFlash('success', "Rôle mis à jour.");
            }
        }
        $this->redirect('admin-clients');
    }

    // ================================================================
    //  CATÉGORIES — CRUD
    // ================================================================

    /** GET ?page=admin-categories */
    public function categories(): void
    {
        $this->requireAdmin();
        $this->render('admin/categories', [
            'page_title' => "Catégories de véhicules — " . APP_NAME,
            'categories' => $this->categorieModel->getAll(),
        ]);
    }

    /** POST ?page=admin-categorie-store */
    public function storeCategorie(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-categories');
        $this->verifyCsrf();

        $nom  = $this->post('nom_categorie');
        $desc = $this->post('description');

        if (empty($nom)) {
            Session::setFlash('error', "Le nom de la catégorie est obligatoire.");
            $this->redirect('admin-categories');
        }

        if ($this->categorieModel->nameExists($nom)) {
            Session::setFlash('error', "Cette catégorie existe déjà.");
            $this->redirect('admin-categories');
        }

        if ($this->categorieModel->create(['nom_categorie' => $nom, 'description' => $desc])) {
            Session::setFlash('success', "Catégorie « {$nom} » créée avec succès.");
        } else {
            Session::setFlash('error', "Erreur lors de la création.");
        }
        $this->redirect('admin-categories');
    }

    /** POST ?page=admin-categorie-update */
    public function updateCategorie(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-categories');
        $this->verifyCsrf();

        $id   = (int) $this->post('categorie_id');
        $nom  = $this->post('nom_categorie');
        $desc = $this->post('description');

        if ($id <= 0 || empty($nom)) {
            Session::setFlash('error', "Données invalides.");
            $this->redirect('admin-categories');
        }

        if ($this->categorieModel->nameExists($nom, $id)) {
            Session::setFlash('error', "Ce nom est déjà utilisé par une autre catégorie.");
            $this->redirect('admin-categories');
        }

        if ($this->categorieModel->update($id, ['nom_categorie' => $nom, 'description' => $desc])) {
            Session::setFlash('success', "Catégorie mise à jour.");
        } else {
            Session::setFlash('error', "Erreur lors de la mise à jour.");
        }
        $this->redirect('admin-categories');
    }

    /** GET ?page=admin-categorie-supprimer&id=X */
    public function supprimerCategorie(): void
    {
        $this->requireAdmin();

        $id = (int) $this->get('id');

        if ($id <= 0) {
            $this->redirect('admin-categories');
        }

        $result = $this->categorieModel->delete($id);

        if ($result) {
            Session::setFlash('success', "Catégorie supprimée.");
        } else {
            Session::setFlash('error', "Impossible de supprimer : des véhicules utilisent cette catégorie.");
        }
        $this->redirect('admin-categories');
    }

    // ================================================================
    //  AGENCES — CRUD
    // ================================================================

    /** GET ?page=admin-agences */
    public function agences(): void
    {
        $this->requireAdmin();
        $this->render('admin/agences', [
            'page_title' => "Agences — " . APP_NAME,
            'agences'    => $this->agenceModel->getAll(),
        ]);
    }

    /** POST ?page=admin-agence-store */
    public function storeAgence(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-agences');
        $this->verifyCsrf();

        $errors = [];
        $data   = [
            'nom'       => $this->post('nom'),
            'adresse'   => $this->post('adresse'),
            'ville'     => $this->post('ville'),
            'latitude'  => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'statut'    => $this->post('statut') ?: 'active',
        ];

        if (empty($data['nom']))   $errors[] = "Le nom de l'agence est obligatoire.";
        if (empty($data['ville'])) $errors[] = "La ville est obligatoire.";

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirect('admin-agences');
        }

        if ($this->agenceModel->create($data)) {
            Session::setFlash('success', "Agence « {$data['nom']} » créée.");
        } else {
            Session::setFlash('error', "Erreur lors de la création.");
        }
        $this->redirect('admin-agences');
    }

    /** POST ?page=admin-agence-update */
    public function updateAgence(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-agences');
        $this->verifyCsrf();

        $id   = (int) $this->post('agence_id');
        $data = [
            'nom'       => $this->post('nom'),
            'adresse'   => $this->post('adresse'),
            'ville'     => $this->post('ville'),
            'latitude'  => $this->post('latitude'),
            'longitude' => $this->post('longitude'),
            'statut'    => $this->post('statut') ?: 'active',
        ];

        if ($id <= 0 || empty($data['nom']) || empty($data['ville'])) {
            Session::setFlash('error', "Données invalides.");
            $this->redirect('admin-agences');
        }

        if ($this->agenceModel->update($id, $data)) {
            Session::setFlash('success', "Agence mise à jour.");
        } else {
            Session::setFlash('error', "Erreur lors de la mise à jour.");
        }
        $this->redirect('admin-agences');
    }

    /** GET ?page=admin-agence-supprimer&id=X */
    public function supprimerAgence(): void
    {
        $this->requireAdmin();

        $id = (int) $this->get('id');

        if ($id <= 0) {
            $this->redirect('admin-agences');
        }

        if ($this->agenceModel->delete($id)) {
            Session::setFlash('success', "Agence supprimée.");
        } else {
            Session::setFlash('error', "Impossible de supprimer : des véhicules sont rattachés à cette agence.");
        }
        $this->redirect('admin-agences');
    }

    /** POST ?page=admin-agence-statut */
    public function updateAgenceStatut(): void
    {
        $this->requireAdmin();
        if (!$this->isPost()) $this->redirect('admin-agences');
        $this->verifyCsrf();

        $id     = (int) $this->post('agence_id');
        $statut = $this->post('statut');

        if ($id > 0 && in_array($statut, ['active', 'inactive'])) {
            $this->agenceModel->updateStatut($id, $statut);
            Session::setFlash('success', "Statut de l'agence mis à jour.");
        }
        $this->redirect('admin-agences');
    }

    // ================================================================
    //  HELPERS PRIVÉS
    // ================================================================

    /**
     * Valide le formulaire véhicule (ajout + édition).
     * Retourne [errors[], data[]].
     */
    private function validateVehiculeForm(): array
    {
        $errors = [];
        $data   = [
            'id_categorie'   => (int) $this->post('id_categorie'),
            'id_agence'      => (int) $this->post('id_agence'),
            'marque'         => $this->post('marque'),
            'modele'         => $this->post('modele'),
            'immatriculation'=> $this->post('immatriculation'),
            'annee'          => $this->post('annee'),
            'nombre_places'  => (int) $this->post('nombre_places'),
            'transmission'   => $this->post('transmission'),
            'carburant'      => $this->post('carburant'),
            'tarif_jour'     => (float) str_replace(',', '.', $this->post('tarif_jour')),
            'image_principale' => '',
        ];

        if ($data['id_categorie'] <= 0)  $errors[] = "La catégorie est obligatoire.";
        if ($data['id_agence'] <= 0)     $errors[] = "L'agence est obligatoire.";
        if (empty($data['marque']))      $errors[] = "La marque est obligatoire.";
        if (empty($data['modele']))      $errors[] = "Le modèle est obligatoire.";
        if (empty($data['immatriculation'])) $errors[] = "L'immatriculation est obligatoire.";
        if (empty($data['annee']))       $errors[] = "L'année est obligatoire.";
        if ($data['nombre_places'] <= 0) $errors[] = "Le nombre de places doit être supérieur à 0.";
        if (empty($data['transmission'])) $errors[] = "La transmission est obligatoire.";
        if (empty($data['carburant']))   $errors[] = "Le carburant est obligatoire.";
        if ($data['tarif_jour'] <= 0)    $errors[] = "Le tarif journalier doit être supérieur à 0.";

        return [$errors, $data];
    }

    /**
     * Gère l'upload de la photo du véhicule.
     * Retourne le nom du fichier ou null si pas d'upload.
     */
    private function handleUpload(): ?string
    {
        if (empty($_FILES['photo']['name'])) return null;

        $file    = $_FILES['photo'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 3 * 1024 * 1024; // 3 Mo

        // Vérifier le type MIME réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            Session::setFlash('error', "Format d'image non supporté. Utilisez JPG, PNG ou WebP.");
            return null;
        }
        if ($file['size'] > $maxSize) {
            Session::setFlash('error', "L'image ne doit pas dépasser 3 Mo.");
            return null;
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
        $dest     = APP_ROOT . '/public/assets/uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Session::setFlash('error', "Erreur lors de l'upload de la photo.");
            return null;
        }

        return $filename;
    }
}
