<?php

/**
 * UserModel — Opérations BDD sur la table `utilisateur`.
 *
 * Colonnes : ID_UTILSATEUR | NOM | PRENOM | ADRESSE_EMAIL
 *            MOT_DE_PASSE  | NUMERO_DE_TELEPHONE | ROLE | DATE_D_INSCRIPTION
 */
class UserModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ----------------------------------------------------------------
    //  LECTURE
    // ----------------------------------------------------------------

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT ID_UTILSATEUR, NOM, PRENOM, ADRESSE_EMAIL,
                    MOT_DE_PASSE, NUMERO_DE_TELEPHONE, ROLE, DATE_D_INSCRIPTION
             FROM utilisateur
             WHERE ADRESSE_EMAIL = ?
             LIMIT 1"
        );
        if (!$stmt) {
            error_log("findByEmail prepare error: " . $this->db->error);
            return null;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT ID_UTILSATEUR, NOM, PRENOM, ADRESSE_EMAIL,
                    NUMERO_DE_TELEPHONE, ROLE, DATE_D_INSCRIPTION
             FROM utilisateur
             WHERE ID_UTILSATEUR = ?
             LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            "SELECT ID_UTILSATEUR FROM utilisateur
             WHERE ADRESSE_EMAIL = ?
             LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // ----------------------------------------------------------------
    //  ÉCRITURE
    // ----------------------------------------------------------------

    public function create(array $data): bool
    {
        // Hasher le mot de passe
        $hash = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);

        // Préparer toutes les variables AVANT bind_param
        // (bind_param prend des références, pas des expressions)
        $nom       = trim($data['nom']);
        $prenom    = trim($data['prenom']);
        $email     = trim($data['email']);
        $telephone = (!empty($data['telephone'])) ? trim($data['telephone']) : null;
        $role      = 'client';

        $stmt = $this->db->prepare(
            "INSERT INTO utilisateur
             (NOM, PRENOM, ADRESSE_EMAIL, MOT_DE_PASSE,
              NUMERO_DE_TELEPHONE, ROLE, DATE_D_INSCRIPTION)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );

        if (!$stmt) {
            error_log("UserModel::create() prepare error: " . $this->db->error . " | errno: " . $this->db->errno);
            return false;
        }

        $stmt->bind_param("ssssss", $nom, $prenom, $email, $hash, $telephone, $role);

        $result = $stmt->execute();

        if (!$result) {
            error_log("UserModel::create() execute error: " . $stmt->error . " | errno: " . $stmt->errno);
        }

        $stmt->close();
        return $result;
    }

    public function update(int $id, array $data): bool
    {
        $nom       = $data['nom'];
        $prenom    = $data['prenom'];
        $telephone = $data['telephone'] ?? null;

        $stmt = $this->db->prepare(
            "UPDATE utilisateur
             SET NOM = ?, PRENOM = ?, NUMERO_DE_TELEPHONE = ?
             WHERE ID_UTILSATEUR = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("sssi", $nom, $prenom, $telephone, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "UPDATE utilisateur SET MOT_DE_PASSE = ? WHERE ID_UTILSATEUR = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("si", $hash, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
