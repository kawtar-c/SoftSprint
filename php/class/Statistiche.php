<?php
require_once __DIR__ . "/../includes/db.php";

class Statistiche {

    private $db;

    public function __construct($db=null) {
        $this->db = $db ?? open();
    }

    // 1. Totale Incassi Giornalieri
    public function getIncassoOggi() {
        $sql = "SELECT SUM(totale) as totale FROM ordine WHERE DATE(data_ora) = CURDATE()";
        $result = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['totale'] ?? 0;
    }

    // 2. Media delle prenotazioni
    public function getMediaPrenotazioni() {
        $sql = "SELECT AVG(persone) as media FROM prenotazione";
        $result = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($result);
        return round($data['media'], 1) ?? 0;
    }

    // 3. Top 10 Piatti (Più/Meno ordinati)
    public function getTopPiatti() {
        $sql = "SELECT p.nome, COUNT(op.id_piatto) as ordini 
                FROM ordine_piatto op
                JOIN piatto p ON op.id_piatto = p.id_piatto
                GROUP BY p.id_piatto
                ORDER BY ordini DESC 
                LIMIT 10";
        $result = mysqli_query($this->db, $sql);
        $piatti = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $piatti[] = $row;
        }
        return $piatti;
    }

    // 4. Affluenza Fasce Orarie
    public function getAffluenzaOraria() {
        // Selezioniamo direttamente la colonna stringa e contiamo le occorrenze
        $sql = "SELECT fascia_oraria as fascia, COUNT(*) as totale
                FROM prenotazione
                GROUP BY fascia_oraria
                ORDER BY totale DESC"; 

        $result = mysqli_query($this->db, $sql);
        $fasce = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $fasce[] = $row;
            }
        }
        return $fasce;
    }

    //Incasso del giorno corrente
    public function getIncassoGiorno() {
        $sql = "SELECT SUM(totale) as totale FROM ordine
                WHERE DATE(data_ora) = CURDATE()";
        $result = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['totale'] ?? 0;
    }

    // Incasso del mese corrente
    public function getIncassoMese() {
        $sql = "SELECT SUM(totale) as totale FROM ordine
                WHERE MONTH(data_ora) = MONTH(CURDATE()) 
                AND YEAR(data_ora) = YEAR(CURDATE())";
        $result = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['totale'] ?? 0;
    }

    // Incasso totale storico del ristorante
    public function getIncassoTotaleStorico() {
        $sql = "SELECT SUM(totale) as totale FROM ordine";
        $result = mysqli_query($this->db, $sql);
        $data = mysqli_fetch_assoc($result);
        return $data['totale'] ?? 0;
    }

    // Incassi mensili per il grafico annuale (restituisce 12 mesi)
    public function getIncassiAnnuali() {
        $sql = "SELECT MONTH(data_ora) as mese, SUM(totale) as totale
                FROM ordine 
                WHERE YEAR(data_ora) = YEAR(CURDATE())
                GROUP BY MONTH(data_ora)
                ORDER BY MONTH(data_ora) ASC";
        $result = mysqli_query($this->db, $sql);
        $mensili = array_fill(1, 12, 0); // Inizializza tutti i mesi a 0
        while ($row = mysqli_fetch_assoc($result)) {
            $mensili[(int)$row['mese']] = (float)$row['totale'];
        }
        return $mensili;
    }
}
