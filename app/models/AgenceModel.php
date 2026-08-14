<?php

/**
 * AgenceModel — CRUD table `agence`
 *
 * Colonnes : ID_AGENCE | NOM_AGENCE_ | ADRESSE_ | VILLE
 *            LATITUDE | LONGITUDE | STATU_VALIDATION
 */
class AgenceModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Toutes les agences. */
    public function getAll(): array
    {
        $r = $this->db->query(
            "SELECT a.*,
                    (SELECT COUNT(*) FROM vehicule v WHERE v.ID_AGENCE = a.ID_AGENCE) AS nb_vehicules
             FROM agence a
             ORDER BY a.VILLE ASC, a.NOM_AGENCE_ ASC"
        );
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Trouve une agence par son ID. */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM agence WHERE ID_AGENCE = ? LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }

    /** Insère une nouvelle agence. */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO agence
             (NOM_AGENCE_, ADRESSE_, VILLE, LATITUDE, LONGITUDE, STATU_VALIDATION)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return false;

        $lat    = !empty($data['latitude'])  ? (float) $data['latitude']  : null;
        $lng    = !empty($data['longitude']) ? (float) $data['longitude'] : null;
        $statut = $data['statut'] ?? 'active';

        $stmt->bind_param(
            "sssdds",
            $data['nom'],
            $data['adresse'],
            $data['ville'],
            $lat,
            $lng,
            $statut
        );
        $result = $stmt->execute();
        if (!$result) error_log("AgenceModel::create: " . $stmt->error);
        $stmt->close();
        return $result;
    }

    /** Met à jour une agence. */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE agence
             SET NOM_AGENCE_ = ?, ADRESSE_ = ?, VILLE = ?,
                 LATITUDE = ?, LONGITUDE = ?, STATU_VALIDATION = ?
             WHERE ID_AGENCE = ?"
        );
        if (!$stmt) return false;

        $lat    = !empty($data['latitude'])  ? (float) $data['latitude']  : null;
        $lng    = !empty($data['longitude']) ? (float) $data['longitude'] : null;
        $statut = $data['statut'] ?? 'active';

        $stmt->bind_param(
            "sssddsi",
            $data['nom'],
            $data['adresse'],
            $data['ville'],
            $lat,
            $lng,
            $statut,
            $id
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /** Supprime une agence (bloqué si des véhicules y sont rattachés). */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS n FROM vehicule WHERE ID_AGENCE = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $n = (int) $stmt->get_result()->fetch_assoc()['n'];
        $stmt->close();

        if ($n > 0) return false;

        $stmt = $this->db->prepare("DELETE FROM agence WHERE ID_AGENCE = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /** Active ou désactive une agence. */
    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE agence SET STATU_VALIDATION = ? WHERE ID_AGENCE = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
