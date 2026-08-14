<?php
/**
 * ReservationModel — table `reservation`
 *
 * Colonnes : ID_RESERVATION | ID_UTILSATEUR | DATE_DEBUT | DATE_FIN
 *            LIEU_PRISE__EN_CHARGE | LIEU_RETOUR | OPTION_VALIDATION
 *            MONTANT_TOTAL | STATUT | DATEL_CREATION
 *
 * La table de liaison véhicule/réservation est `concerner`
 * (ID_VEHICULE, ID_RESERVATION)
 */
class ReservationModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crée une réservation et lie le véhicule via la table `concerner`.
     */
    public function create(array $data): bool
    {
        // 1. Insérer la réservation
        $stmt = $this->db->prepare(
            "INSERT INTO reservation
             (ID_UTILSATEUR, DATE_DEBUT, DATE_FIN,
              LIEU_PRISE__EN_CHARGE, LIEU_RETOUR,
              MONTANT_TOTAL, STATUT, DATEL_CREATION)
             VALUES (?, ?, ?, ?, ?, ?, 'confirmee', NOW())"
        );
        $stmt->bind_param(
            "issssd",
            $data['client_id'],
            $data['date_debut'],
            $data['date_fin'],
            $data['lieu_prise'],
            $data['lieu_retour'],
            $data['montant']
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $reservationId = (int) $this->db->insert_id;
        $stmt->close();

        // 2. Lier le véhicule dans `concerner`
        $stmt2 = $this->db->prepare(
            "INSERT INTO concerner (ID_VEHICULE, ID_RESERVATION) VALUES (?, ?)"
        );
        $stmt2->bind_param("ii", $data['vehicule_id'], $reservationId);
        $result = $stmt2->execute();
        $stmt2->close();

        return $result;
    }

    /**
     * Réservations d'un client avec les infos véhicule.
     */
    public function findByClient(int $clientId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*,
                    v.MARQUE, v.MODELE, v.IMAGE_PRINCIPALE,
                    c.NOM_CATEGORIE
             FROM reservation r
             JOIN concerner  cn ON cn.ID_RESERVATION = r.ID_RESERVATION
             JOIN vehicule   v  ON v.ID_VEHICULE      = cn.ID_VEHICULE
             LEFT JOIN categorie_vehicule c ON c.ID_CATEGORIE = v.ID_CATEGORIE
             WHERE r.ID_UTILSATEUR = ?
             ORDER BY r.DATEL_CREATION DESC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Trouve une réservation par son ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, cn.ID_VEHICULE
             FROM reservation r
             LEFT JOIN concerner cn ON cn.ID_RESERVATION = r.ID_RESERVATION
             WHERE r.ID_RESERVATION = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }

    /**
     * Toutes les réservations (admin).
     */
    public function findAll(): array
    {
        $result = $this->db->query(
            "SELECT r.*,
                    CONCAT(u.PRENOM, ' ', u.NOM) AS client_nom,
                    u.ADRESSE_EMAIL              AS client_email,
                    v.MARQUE, v.MODELE
             FROM reservation r
             JOIN utilisateur u  ON u.ID_UTILSATEUR  = r.ID_UTILSATEUR
             JOIN concerner   cn ON cn.ID_RESERVATION = r.ID_RESERVATION
             JOIN vehicule    v  ON v.ID_VEHICULE     = cn.ID_VEHICULE
             ORDER BY r.DATEL_CREATION DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Met à jour le statut d'une réservation.
     * Valeurs : 'confirmee' | 'annulee' | 'terminee'
     */
    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reservation SET STATUT = ? WHERE ID_RESERVATION = ?"
        );
        $stmt->bind_param("si", $statut, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
