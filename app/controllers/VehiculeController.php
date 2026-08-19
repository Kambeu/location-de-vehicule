<?php

require_once APP_ROOT . '/app/models/VehiculeModel.php';
require_once APP_ROOT . '/app/models/ReservationModel.php';
require_once APP_ROOT . '/app/models/PhotoVehiculeModel.php';

/**
 * VehiculeController — Catalogue, détail et réservation.
 */
class VehiculeController extends Controller
{
    private VehiculeModel    $vehiculeModel;
    private ReservationModel $reservationModel;

    public function __construct()
    {
        $this->vehiculeModel    = new VehiculeModel();
        $this->reservationModel = new ReservationModel();
    }

    // ----------------------------------------------------------------
    //  GET ?page=vehicules
    // ----------------------------------------------------------------
    public function index(): void
    {
        $filtres = [
            'categorie'    => $this->get('categorie')    ?: null,
            'prix_max'     => $this->get('prix_max')     ?: null,
            'transmission' => $this->get('transmission') ?: null,
        ];

        $vehicules  = $this->vehiculeModel->search($filtres);
        $categories = $this->vehiculeModel->getCategories();

        // Ajouter la photo principale à chaque véhicule
        foreach ($vehicules as &$v) {
            $v['_main_photo'] = PhotoVehiculeModel::getMainPhoto($v);
        }
        unset($v);

        $this->render('vehicules/index', [
            'page_title' => "Nos véhicules — " . APP_NAME,
            'vehicules'  => $vehicules,
            'categories' => $categories,
            'filtres'    => $filtres,
        ]);
    }

    // ----------------------------------------------------------------
    //  GET ?page=vehicule-detail&id=X
    // ----------------------------------------------------------------
    public function detail(): void
    {
        $id = (int) $this->get('id');

        if ($id <= 0) {
            $this->redirect('vehicules');
        }

        $vehicule = $this->vehiculeModel->findById($id);

        if (!$vehicule) {
            http_response_code(404);
            $this->render('errors/404', ['page_title' => "Véhicule introuvable"]);
            return;
        }

        // Récupérer toutes les photos du dossier
        $photos = PhotoVehiculeModel::getPhotos($vehicule['DOSSIER_PHOTOS'] ?? '');

        // Fallback si dossier vide
        if (empty($photos)) {
            $main = PhotoVehiculeModel::getMainPhoto($vehicule);
            if ($main) $photos = [$main];
        }

        $this->render('vehicules/detail', [
            'page_title' => htmlspecialchars($vehicule['MARQUE'] . ' ' . $vehicule['MODELE']) . " — " . APP_NAME,
            'vehicule'   => $vehicule,
            'photos'     => $photos,
        ]);
    }

    // ----------------------------------------------------------------
    //  POST ?page=vehicule-reserver
    // ----------------------------------------------------------------
    public function reserver(): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('vehicules');
        }

        $this->verifyCsrf();
        $vehiculeId = (int)   $this->post('vehicule_id');
        $dateDebut  =         $this->post('date_debut');
        $dateFin    =         $this->post('date_fin');
        $lieuPrise  =         $this->post('lieu_prise')  ?: 'Non précisé';
        $lieuRetour =         $this->post('lieu_retour') ?: 'Non précisé';
        $errors     = [];

        if ($vehiculeId <= 0)
            $errors[] = "Véhicule invalide.";
        if (empty($dateDebut) || empty($dateFin))
            $errors[] = "Les dates sont obligatoires.";
        if (!empty($dateDebut) && !empty($dateFin) && $dateDebut >= $dateFin)
            $errors[] = "La date de fin doit être après la date de début.";
        if (!empty($dateDebut) && strtotime($dateDebut) < strtotime('today'))
            $errors[] = "La date de début ne peut pas être dans le passé.";

        if (empty($errors)) {
            $vehicule = $this->vehiculeModel->findById($vehiculeId);
            if (!$vehicule || $vehicule['STATUT_DISPONIBLE'] !== 'disponible') {
                $errors[] = "Ce véhicule n'est plus disponible.";
            }
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirect('vehicule-detail', ['id' => $vehiculeId]);
        }

        $jours   = (int) ceil((strtotime($dateFin) - strtotime($dateDebut)) / 86400);
        $total   = $jours * (float) $vehicule['TARIF_JOUR'];

        $success = $this->reservationModel->create([
            'client_id'   => (int) Session::get('user_id'),
            'vehicule_id' => $vehiculeId,
            'date_debut'  => $dateDebut,
            'date_fin'    => $dateFin,
            'lieu_prise'  => $lieuPrise,
            'lieu_retour' => $lieuRetour,
            'montant'     => $total,
        ]);

        if ($success) {
            $this->vehiculeModel->updateStatut($vehiculeId, 'loue');
            Session::setFlash('success', "Réservation confirmée ! {$jours} jour(s) — " . number_format($total, 0, ',', ' ') . " FCFA.");
            $this->redirect('mes-reservations');
        } else {
            Session::setFlash('error', "Erreur lors de la réservation. Veuillez réessayer.");
            $this->redirect('vehicule-detail', ['id' => $vehiculeId]);
        }
    }
}
