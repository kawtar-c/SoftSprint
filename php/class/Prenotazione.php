<?php
require_once __DIR__ . "/../includes/db.php";


class Prenotazione {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    // Ottieni tutte le prenotazioni
    public function getPrenotazioni(): array {
        $result = $this->db->query("SELECT * FROM prenotazione");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Aggiungi prenotazione
    public function aggiungiPrenotazione(string $nome, string $telefono, string $data, int $persone, string $fascia_oraria): bool {
        $stmt = $this->db->prepare("INSERT INTO prenotazione (nome, telefono, data, persone, fascia_oraria) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $nome, $telefono, $data, $persone, $fascia_oraria);
        return $stmt->execute();
    }

    // Elimina prenotazione
    public function eliminaPrenotazione(int $id_prenotazione): bool {
        $stmt = $this->db->prepare("DELETE FROM prenotazione WHERE id_prenotazione = ?");
        $stmt->bind_param("i", $id_prenotazione);
        return $stmt->execute();
    }

    // Modifica prenotazione
    public function modificaPrenotazione(int $id_prenotazione, string $nome, string $telefono, string $data, int $persone, string $fascia_oraria): bool {
        $stmt = $this->db->prepare("UPDATE prenotazione SET nome = ?, telefono = ?, data = ?, persone = ?, fascia_oraria = ? WHERE id_prenotazione = ?");
        $stmt->bind_param("sssisi", $nome, $telefono, $data, $persone, $fascia_oraria, $id_prenotazione);
        return $stmt->execute();
    }

    // Assegna tavolo a prenotazione
    public function assegnaTavolo(int $id_prenotazione, int $id_tavolo): bool {
        $stmt = $this->db->prepare("UPDATE prenotazione SET id_tavolo = ? WHERE id_prenotazione = ?");
        $stmt->bind_param("ii", $id_tavolo, $id_prenotazione);
        return $stmt->execute();
    }   
}