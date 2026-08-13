<?php

/**
 * UserModel — Toutes les opérations BDD liées aux utilisateurs.
 * Aucune logique HTML ni session ici.
 */
class UserModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Vérifie si un email ou username est déjà pris.
     */
    public function existsByEmailOrUsername(string $email, string $username): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM " . TABLE_USERS . " WHERE email = ? OR username = ? LIMIT 1"
        );
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Insère un nouvel utilisateur. Retourne true si succès.
     */
    public function create(array $data): bool
    {
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            "INSERT INTO " . TABLE_USERS . "
             (username, email, password, full_name, date_naiss, pays, genre, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param(
            "sssssss",
            $data['username'],
            $data['email'],
            $hashed,
            $data['full_name'],
            $data['date_naiss'],
            $data['pays'],
            $data['genre']
        );

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Trouve un utilisateur par email. Retourne le tableau ou null.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, username, full_name, email, password, genre
             FROM " . TABLE_USERS . "
             WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }

    /**
     * Trouve un utilisateur par son ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, username, full_name, email, genre, pays, date_naiss, created_at
             FROM " . TABLE_USERS . "
             WHERE id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }
}
