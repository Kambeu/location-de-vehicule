<?php
/**
 * VehiculeModel — table `vehicule`
 */
class VehiculeModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

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

    /** Tous les véhicules (admin) — sans filtre statut */
    public function getAll(): array
    {
        $r = $this->db->query(
            "SELECT v.*, c.NOM_CATEGORIE, a.NOM_AGENCE_
             FROM vehicule v
             LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
             LEFT JOIN agence a ON v.ID_AGENCE = a.ID_AGENCE
             ORDER BY v.ID_VEHICULE DESC"
        );
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function search(array $filtres): array
    {
        $sql    = "SELECT v.*, c.NOM_CATEGORIE
                   FROM vehicule v
                   LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
                   WHERE v.STATUT_DISPONIBLE = 'disponible'";
        $params = [];
        $types  = '';

        if (!empty($filtres['categorie'])) {
            $sql .= " AND c.NOM_CATEGORIE = ?";
            $types   .= 's';
            $params[] = $filtres['categorie'];
        }
        if (!empty($filtres['prix_max'])) {
            $sql .= " AND v.TARIF_JOUR <= ?";
            $types   .= 'd';
            $params[] = (float) $filtres['prix_max'];
        }
        if (!empty($filtres['transmission'])) {
            $sql .= " AND v.TRANSMISSION = ?";
            $types   .= 's';
            $params[] = $filtres['transmission'];
        }

        $sql .= " ORDER BY v.TARIF_JOUR ASC";
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, c.NOM_CATEGORIE
             FROM vehicule v
             LEFT JOIN categorie_vehicule c ON v.ID_CATEGORIE = c.ID_CATEGORIE
             WHERE v.ID_VEHICULE = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }

    public function getCategories(): array
    {
        $r = $this->db->query("SELECT ID_CATEGORIE, NOM_CATEGORIE FROM categorie_vehicule ORDER BY NOM_CATEGORIE");
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAgences(): array
    {
        $r = $this->db->query("SELECT ID_AGENCE, NOM_AGENCE_ FROM agence ORDER BY NOM_AGENCE_");
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ----------------------------------------------------------------
    //  ÉCRITURE
    // ----------------------------------------------------------------

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO vehicule
             (ID_CATEGORIE, ID_AGENCE, MARQUE, MODELE, IMMATRICULATION,
              ANNEE, NOMBRE_PLACES, TRANSMISSION, CARBURANT,
              TARIF_JOUR, STATUT_DISPONIBLE, IMAGE_PRINCIPALE)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'disponible', ?)"
        );
        if (!$stmt) { error_log("VehiculeModel::create prepare: " . $this->db->error); return false; }

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
        if (!$result) error_log("VehiculeModel::create execute: " . $stmt->error);
        $stmt->close();
        return $result;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vehicule
             SET ID_CATEGORIE=?, ID_AGENCE=?, MARQUE=?, MODELE=?,
                 IMMATRICULATION=?, ANNEE=?, NOMBRE_PLACES=?,
                 TRANSMISSION=?, CARBURANT=?, TARIF_JOUR=?, IMAGE_PRINCIPALE=?
             WHERE ID_VEHICULE = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param(
            "iisssissdsi",
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
            $data['image_principale'],
            $id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM vehicule WHERE ID_VEHICULE = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare("UPDATE vehicule SET STATUT_DISPONIBLE = ? WHERE ID_VEHICULE = ?");
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
