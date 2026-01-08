<?php
require_once __DIR__ . "/../includes/db.php";

class Statistiche {

    private $db;

    public function __construct($db = null) {
        $this->db = $db ?? open();
    }

    public function getIncassoOggi() {
        $sql = "SELECT SUM(totale) as totale FROM ordine WHERE DATE(data_ora) = CURDATE()";
        $result = $this->db->query($sql);
        $data = $result->fetch_assoc();
        return $data['totale'] ?? 0;
    }

    public function getMediaPrenotazioni() {
        $sql = "SELECT AVG(persone) as media FROM prenotazione";
        $result = $this->db->query($sql);
        $data = $result->fetch_assoc();
        return round($data['media'], 1) ?? 0;
    }

    public function getTopPiatti() {
        $sql = "SELECT p.nome, COUNT(op.id_piatto) as ordini 
                FROM ordine_piatto op
                JOIN piatto p ON op.id_piatto = p.id_piatto
                GROUP BY p.id_piatto
                ORDER BY ordini DESC 
                LIMIT 10";
        $result = $this->db->query($sql);
        $piatti = [];
        while ($row = $result->fetch_assoc()) {
            $piatti[] = $row;
        }
        return $piatti;
    }

    public function getAffluenzaOraria() {
        $sql = "SELECT fascia_oraria as fascia, COUNT(*) as totale
                FROM prenotazione
                GROUP BY fascia_oraria
                ORDER BY totale DESC"; 
        $result = $this->db->query($sql);
        $fasce = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $fasce[] = $row;
            }
        }
        return $fasce;
    }

    public function getIncassoMese() {
        $sql = "SELECT SUM(totale) as totale FROM ordine
                WHERE MONTH(data_ora) = MONTH(CURDATE()) 
                AND YEAR(data_ora) = YEAR(CURDATE())";
        $result = $this->db->query($sql);
        $data = $result->fetch_assoc();
        return $data['totale'] ?? 0;
    }

    public function getIncassoTotaleStorico() {
        $sql = "SELECT SUM(totale) as totale FROM ordine";
        $result = $this->db->query($sql);
        $data = $result->fetch_assoc();
        return $data['totale'] ?? 0;
    }

    public function getIncassiAnnuali() {
        $sql = "SELECT MONTH(data_ora) as mese, SUM(totale) as totale
                FROM ordine 
                WHERE YEAR(data_ora) = YEAR(CURDATE())
                GROUP BY MONTH(data_ora)
                ORDER BY MONTH(data_ora) ASC";
        $result = $this->db->query($sql);
        $mensili = array_fill(1, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $mensili[(int)$row['mese']] = (float)$row['totale'];
        }
        return $mensili;
    }
}