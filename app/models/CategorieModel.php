<?php

/**
 * CategorieModel — CRUD table `categorie_vehicule`
 *
 * Colonnes : ID_CATEGORIE | NOM_CATEGORIE | DESCRIPTION
 */
class CategorieModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Toutes les catégories triées par nom. */
    public function getAll(): array
    {
        $r = $this->db->query(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM vehicule v WHERE v.ID_CATEGORIE = c.ID_CATEGORIE) AS nb_vehicules
             FROM categorie_vehicule c
             ORDER BY c.NOM_CATEGORIE ASC"
        );
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Trouve une catégorie par son ID. */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM categorie_vehicule WHERE ID_CATEGORIE = ? LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }

    /** Vérifie si un nom de catégorie existe déjà (hors ID donné pour l'édition). */
    public function nameExists(string $nom, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT ID_CATEGORIE FROM categorie_vehicule
             WHERE NOM_CATEGORIE = ? AND ID_CATEGORIE != ?
             LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param("si", $nom, $excludeId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /** Insère une nouvelle catégorie. */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categorie_vehicule (NOM_CATEGORIE, DESCRIPTION)
             VALUES (?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ss", $data['nom_categorie'], $data['description']);
        $result = $stmt->execute();
        if (!$result) error_log("CategorieModel::create: " . $stmt->error);
        $stmt->close();
        return $result;
    }

    /** Met à jour une catégorie. */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE categorie_vehicule
             SET NOM_CATEGORIE = ?, DESCRIPTION = ?
             WHERE ID_CATEGORIE = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $data['nom_categorie'], $data['description'], $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /** Supprime une catégorie (échoue si des véhicules y sont liés). */
    public function delete(int $id): bool
    {
        // Vérifier qu'aucun véhicule n'utilise cette catégorie
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS n FROM vehicule WHERE ID_CATEGORIE = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $n = (int) $stmt->get_result()->fetch_assoc()['n'];
        $stmt->close();

        if ($n > 0) return false; // Suppression bloquée

        $stmt = $this->db->prepare(
            "DELETE FROM categorie_vehicule WHERE ID_CATEGORIE = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
