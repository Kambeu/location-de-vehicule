<?php
/**
 * VehiculeModel — table `vehicule`
 *
 * Colonnes : ID_VEHICULE | ID_CATEGORIE | ID_AGENCE | MARQUE | MODELE
 *            IMMATRICULATION | ANNEE | NOMBRE_PLACES | TRANSMISSION
 *            CARBURANT | TARIF_JOUR | STATUT_DISPONIBLE | IMAGE_PRINCIPALE | HEURE
 */
class VehiculeModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Véhicules disponibles pour la page d'accueil.
     */
    public function getDisponibles(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, c.NOM_CATEGORIE
             FROM vehicule v
             LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
             WHERE v.STATUT_DISPONIBLE = 'disponible'
             ORDER BY v.ID_VEHICULE DESC
             LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Recherche avec filtres optionnels.
     */
    public function search(array $filtres): array
    {
        $sql    = "SELECT v.*, c.NOM_CATEGORIE
                   FROM vehicule v
                   LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
                   WHERE v.STATUT_DISPONIBLE = 'disponible'";
        $params = [];
        $types  = '';

        if (!empty($filtres['categorie'])) {
            $sql     .= " AND c.NOM_CATEGORIE = ?";
            $types   .= 's';
            $params[] = $filtres['categorie'];
        }

        if (!empty($filtres['prix_max'])) {
            $sql     .= " AND v.TARIF_JOUR <= ?";
            $types   .= 'd';
            $params[] = (float) $filtres['prix_max'];
        }

        if (!empty($filtres['transmission'])) {
            $sql     .= " AND v.TRANSMISSION = ?";
            $types   .= 's';
            $params[] = $filtres['transmission'];
        }

        $sql .= " ORDER BY v.TARIF_JOUR ASC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Trouve un véhicule par son ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, c.NOM_CATEGORIE
             FROM vehicule v
             LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
             WHERE v.ID_VEHICULE = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }

    /**
     * Retourne les catégories distinctes depuis la table categorie_vehicule.
     */
    public function getCategories(): array
    {
        $result = $this->db->query(
            "SELECT DISTINCT c.NOM_CATEGORIE
             FROM categorie_vehicule c
             INNER JOIN vehicule v ON v.ID_CATEGORIE = c.ID_CATEGORIE
             WHERE v.STATUT_DISPONIBLE = 'disponible'
             ORDER BY c.NOM_CATEGORIE"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Met à jour le statut de disponibilité d'un véhicule.
     * Valeurs : 'disponible' | 'loue' | 'maintenance'
     */
    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vehicule SET STATUT_DISPONIBLE = ? WHERE ID_VEHICULE = ?"
        );
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Insère un nouveau véhicule.
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO vehicule
             (ID_CATEGORIE, ID_AGENCE, MARQUE, MODELE, IMMATRICULATION,
              ANNEE, NOMBRE_PLACES, TRANSMISSION, CARBURANT,
              TARIF_JOUR, STATUT_DISPONIBLE, IMAGE_PRINCIPALE)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'disponible', ?)"
        );
        $stmt->bind_param(
            "iissssissds",
            $data['id_categorie'],
            $data['id_agence'],
            $data['marque'],
            $data['modele'],
            $data['immatriculation'],
            $data['annee'],
            $data['nombre_places'],
            $data['transmission'],
            $data['carburant'],
            $data['tarif_jour'],
            $data['image_principale']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Met à jour un véhicule existant.
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vehicule
             SET ID_CATEGORIE=?, MARQUE=?, MODELE=?, IMMATRICULATION=?,
                 ANNEE=?, NOMBRE_PLACES=?, TRANSMISSION=?, CARBURANT=?,
                 TARIF_JOUR=?, IMAGE_PRINCIPALE=?
             WHERE ID_VEHICULE = ?"
        );
        $stmt->bind_param(
            "issssissdsi",
            $data['id_categorie'],
            $data['marque'],
            $data['modele'],
            $data['immatriculation'],
            $data['annee'],
            $data['nombre_places'],
            $data['transmission'],
            $data['carburant'],
            $data['tarif_jour'],
            $data['image_principale'],
            $id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Supprime un véhicule.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM vehicule WHERE ID_VEHICULE = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
