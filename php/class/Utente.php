<?php
require_once __DIR__ . "/../includes/db.php";

class Utente {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    //Lista utenti (admin)
    public function getUtenti(): array {
        $stmt = $this->db->prepare("SELECT id_utente, email, ruolo, nome, cognome FROM utente");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //Dettagli utente (admin)
    public function getUtente(int $id_utente): ?array {
        $stmt = $this->db->prepare("SELECT id_utente, email, ruolo, nome, cognome, password FROM utente WHERE id_utente = ?");
        $stmt->bind_param("i", $id_utente);
        $stmt->execute();
        $result = $stmt->get_result();
        $ris = $result->fetch_assoc();
        return $ris ? $ris : null;
    }

    //Aggiungi utente (admin)
    public function aggiungiUtente(string $email, string $password, string $ruolo, string $nome, string $cognome): bool {
        $stmt = $this->db->prepare("INSERT INTO utente (email, password, ruolo, nome, cognome) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $password, $ruolo, $nome, $cognome);
        return $stmt->execute();
    }
    //Modifica utente (admin)
    public function modificaUtente(int $id_utente, string $email, string $password, string $ruolo, string $nome, string $cognome): bool {
        $stmt = $this->db->prepare("UPDATE utente SET email = ?, password = ?, ruolo = ?, nome = ?, cognome = ? WHERE id_utente = ?");
        $stmt->bind_param("sssssi", $email, $password, $ruolo, $nome, $cognome, $id_utente);
        return $stmt->execute();
    }
    //Elimina utente (admin)
    public function eliminaUtente(int $id_utente): bool {
        $stmt = $this->db->prepare("DELETE FROM utente WHERE id_utente = ?");
        $stmt->bind_param("i", $id_utente);
        return $stmt->execute();
    }

    //Login e autenticazione
    public function login(string $email, string $password): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utente WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $ris = $result->fetch_assoc(); 


        if ($ris && $password===$ris["password"]) {
            return $ris;
        }
        return null;
    }
}
