<?php
require_once __DIR__ . "/../includes/db.php";

class Utente {

    private $db;

    public function __construct() {
        $this->db = open();
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
