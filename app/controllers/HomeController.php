<?php

require_once APP_ROOT . '/app/models/VehiculeModel.php';
require_once APP_ROOT . '/app/models/PhotoVehiculeModel.php';

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

    public function index(): void
    {
        $vehiculesVedette = $this->vehiculeModel->getDisponibles(6);

        // Ajouter la photo principale à chaque véhicule
        foreach ($vehiculesVedette as &$v) {
            $v['_main_photo'] = PhotoVehiculeModel::getMainPhoto($v);
        }
        unset($v);

        $this->render('home/index', [
            'page_title'       => APP_NAME . " — Location de véhicules",
            'vehiculesVedette' => $vehiculesVedette,
        ]);
    }
}
