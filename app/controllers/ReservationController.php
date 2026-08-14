<?php

require_once APP_ROOT . '/app/models/ReservationModel.php';
require_once APP_ROOT . '/app/models/VehiculeModel.php';

/**
 * ReservationController — Espace client.
 */
class ReservationController extends Controller
{
    private ReservationModel $reservationModel;
    private VehiculeModel    $vehiculeModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->vehiculeModel    = new VehiculeModel();
    }

    // ----------------------------------------------------------------
    //  GET ?page=mes-reservations
    // ----------------------------------------------------------------
    public function index(): void
    {
        $this->requireLogin();

        $clientId     = (int) Session::get('user_id');
        $reservations = $this->reservationModel->findByClient($clientId);

        $this->render('reservations/index', [
            'page_title'   => "Mes réservations — " . APP_NAME,
            'reservations' => $reservations,
        ]);
    }

    // ----------------------------------------------------------------
    //  GET ?page=reservation-annuler&id=X
    // ----------------------------------------------------------------
    public function annuler(): void
    {
        $this->requireLogin();

        $reservationId = (int) $this->get('id');
        $clientId      = (int) Session::get('user_id');

        $reservation = $this->reservationModel->findById($reservationId);

        if (!$reservation || (int) $reservation['ID_UTILSATEUR'] !== $clientId) {
            Session::setFlash('error', "Réservation introuvable.");
            $this->redirect('mes-reservations');
        }

        if ($reservation['STATUT'] !== 'confirmee') {
            Session::setFlash('error', "Cette réservation ne peut plus être annulée.");
            $this->redirect('mes-reservations');
        }

        $this->reservationModel->updateStatut($reservationId, 'annulee');

        // Remettre le véhicule disponible
        if (!empty($reservation['ID_VEHICULE'])) {
            $this->vehiculeModel->updateStatut((int) $reservation['ID_VEHICULE'], 'disponible');
        }

        Session::setFlash('success', "Réservation annulée avec succès.");
        $this->redirect('mes-reservations');
    }
}
