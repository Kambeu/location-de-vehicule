<?php

require_once APP_ROOT . '/app/models/VehiculeModel.php';

/**
 * HomeController — Page d'accueil.
 */
class HomeController extends Controller
{
    private VehiculeModel $vehiculeModel;

    public function __construct()
    {
        $this->vehiculeModel = new VehiculeModel();
    }

    /**
     * GET ?page=home  (ou aucun paramètre page)
     */
    public function index(): void
    {
        // 6 véhicules disponibles mis en avant sur la page d'accueil
        $vehiculesVedette = $this->vehiculeModel->getDisponibles(6);

        $this->render('home/index', [
            'page_title'       => APP_NAME . " — Location de véhicules",
            'vehiculesVedette' => $vehiculesVedette,
        ]);
    }
}
