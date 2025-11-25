<?php
require_once __DIR__ . "/../includes/db.php";

class Piatto {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    //Ritorna tutti i piatti del menu (cliente + cameriere)
    public function getMenu(): array {
        $query = "SELECT * FROM piatto";
        $req= $this->db->query($query);
        return $req ->fetch_all(MYSQLI_ASSOC);
    }
}
