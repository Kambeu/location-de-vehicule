<?php
/**
 * PhotoVehiculeModel — Lit les photos d'un véhicule depuis son dossier local.
 *
 * Convention :
 *   Dossier : public/assets/uploads/{DOSSIER_PHOTOS}/
 *   Fichiers : 1.jpg, 2.jpg, 3.jpg... OU tout nom .jpg/.png/.webp
 *   La première image triée = IMAGE_PRINCIPALE affichée sur les cartes
 */
class PhotoVehiculeModel
{
    // Extensions acceptées
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Retourne la liste des URLs de photos d'un véhicule.
     * Lit le dossier physique sur le serveur.
     *
     * @param  string $dossier  Valeur de DOSSIER_PHOTOS (ex: "renault_clio")
     * @return array            Tableau d'URLs absolues (http://...)
     */
    public static function getPhotos(string $dossier): array
    {
        if (empty($dossier)) return [];

        $dir = APP_ROOT . '/public/assets/uploads/' . $dossier . '/';

        if (!is_dir($dir)) return [];

        $files = [];
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, self::EXTENSIONS)) continue;
            $files[] = $file;
        }

        // Trier naturellement (1.jpg, 2.jpg, 3.jpg...)
        natsort($files);

        // Construire les URLs
        $urls = [];
        foreach ($files as $file) {
            $urls[] = APP_URL . '/assets/uploads/' . $dossier . '/' . $file;
        }

        return array_values($urls);
    }

    /**
     * Retourne la première photo du dossier (pour les cartes catalogue).
     * Fallback sur IMAGE_PRINCIPALE si le dossier est vide.
     *
     * @param  array  $vehicule  Ligne de la table vehicule
     * @return string            URL de l'image ou chaîne vide
     */
    public static function getMainPhoto(array $vehicule): string
    {
        // Essayer le dossier d'abord
        if (!empty($vehicule['DOSSIER_PHOTOS'])) {
            $photos = self::getPhotos($vehicule['DOSSIER_PHOTOS']);
            if (!empty($photos)) return $photos[0];
        }

        // Fallback sur IMAGE_PRINCIPALE
        if (!empty($vehicule['IMAGE_PRINCIPALE'])) {
            $img = $vehicule['IMAGE_PRINCIPALE'];
            return str_starts_with($img, 'http')
                ? $img
                : APP_URL . '/assets/uploads/' . $img;
        }

        return '';
    }
}
