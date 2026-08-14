<?php

/**
 * AdminModel — Statistiques et opérations back-office.
 */
class AdminModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Nombre total de clients inscrits. */
    public function countClients(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS n FROM utilisateur WHERE ROLE = 'client'");
        return $r ? (int) $r->fetch_assoc()['n'] : 0;
    }

    /** Nombre total de véhicules. */
    public function countVehicules(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS n FROM vehicule");
        return $r ? (int) $r->fetch_assoc()['n'] : 0;
    }

    /** Nombre de véhicules disponibles. */
    public function countDisponibles(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS n FROM vehicule WHERE STATUT_DISPONIBLE = 'disponible'");
        return $r ? (int) $r->fetch_assoc()['n'] : 0;
    }

    /** Nombre total de réservations. */
    public function countReservations(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS n FROM reservation");
        return $r ? (int) $r->fetch_assoc()['n'] : 0;
    }

    /** Chiffre d'affaires total (réservations confirmées + terminées). */
    public function chiffreAffaires(): float
    {
        $r = $this->db->query(
            "SELECT COALESCE(SUM(MONTANT_TOTAL), 0) AS total
             FROM reservation
             WHERE STATUT IN ('confirmee', 'terminee')"
        );
        return $r ? (float) $r->fetch_assoc()['total'] : 0.0;
    }

    /** Liste tous les clients. */
    public function getAllClients(): array
    {
        $r = $this->db->query(
            "SELECT ID_UTILSATEUR, NOM, PRENOM, ADRESSE_EMAIL,
                    NUMERO_DE_TELEPHONE, ROLE, DATE_D_INSCRIPTION
             FROM utilisateur
             ORDER BY DATE_D_INSCRIPTION DESC"
        );
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Toutes les réservations avec infos client et véhicule. */
    public function getAllReservations(): array
    {
        $r = $this->db->query(
            "SELECT res.*,
                    CONCAT(u.PRENOM, ' ', u.NOM) AS client_nom,
                    u.ADRESSE_EMAIL               AS client_email,
                    v.MARQUE, v.MODELE
             FROM reservation res
             JOIN utilisateur u  ON u.ID_UTILSATEUR  = res.ID_UTILSATEUR
             JOIN concerner   cn ON cn.ID_RESERVATION = res.ID_RESERVATION
             JOIN vehicule    v  ON v.ID_VEHICULE     = cn.ID_VEHICULE
             ORDER BY res.DATEL_CREATION DESC"
        );
        return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Met à jour le rôle d'un utilisateur. */
    public function updateRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE utilisateur SET ROLE = ? WHERE ID_UTILSATEUR = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("si", $role, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /** Met à jour le statut d'une réservation. */
    public function updateReservationStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservation SET STATUT = ? WHERE ID_RESERVATION = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /** Retourne une réservation avec son véhicule lié. */
    public function getReservationById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, cn.ID_VEHICULE
             FROM reservation r
             LEFT JOIN concerner cn ON cn.ID_RESERVATION = r.ID_RESERVATION
             WHERE r.ID_RESERVATION = ? LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }
}
