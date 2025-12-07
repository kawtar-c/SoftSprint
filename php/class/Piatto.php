<?php
require_once __DIR__ . "/../includes/db.php";

class Piatto {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    //Ritorna un piatto specifico per ID
    public function getPiatto(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM piatto WHERE id_piatto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $piatto = $result->fetch_assoc();
        $stmt->close();
        return $piatto ? $piatto : null;
    }

    //Ritorna tutti i piatti del menu (cliente + cameriere)
    public function getMenu(): array {
        $query = "SELECT * FROM piatto";
        $req= $this->db->query($query);
        return $req ->fetch_all(MYSQLI_ASSOC);
    }

    //Aggiungi un nuovo piatto al menu (admin)
    public function aggiungiPiatto(string $nome, string $descrizione, float $prezzo, string $categoria, string $img): void {
        $stmt = $this->db->prepare("INSERT INTO piatto (nome, descrizione, prezzo, categoria, img) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $nome, $descrizione, $prezzo, $categoria, $img);
        $stmt->execute();
        $stmt->close();
    }

    //Modifica un piatto esistente nel menu (admin)
    public function modificaPiatto(int $id, string $nome, string $descrizione, float $prezzo, string $categoria, string $img): void {
        $stmt = $this->db->prepare("UPDATE piatto SET nome = ?, descrizione = ?, prezzo = ?, categoria = ?, img = ? WHERE id_piatto = ?");
        $stmt->bind_param("ssdssi", $nome, $descrizione, $prezzo, $categoria, $img, $id);
        $stmt->execute();
        $stmt->close();
    }

    //Elimina un piatto dal menu (admin)
    public function eliminaPiatto(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM piatto WHERE id_piatto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
