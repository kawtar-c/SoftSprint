<?php
require_once "../php/includes/Header.php";
$header = new Header();
?>


<?php

require_once "../php/class/Prenotazione.php";
if (isset($_POST['booking-form'])) {
  $prenotazione = new Prenotazione();
  $nome = $_POST['nome']; 
  $telefono = $_POST['telefono'];
  $data = $_POST['data'];
  $persone = intval($_POST['persone']);
  $fascia_oraria = $_POST['orario'];
  echo "<script>alert('Prenotazione inviata con successo!');</script>";
  $prenotazione->aggiungiPrenotazione($nome, $telefono, $data, $persone, $fascia_oraria);
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
  }
  
?>



<!-- NAVBAR / HEADER GENERICO -->
<?php echo $header->render('simple'); ?>

<main>
    <!-- SEZIONE PRENOTAZIONE -->
    <section id="prenotazione" class="booking-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Prenota il tuo tavolo</h2>
                <p class="section-subtitle">
                    Scegli data, numero di persone e fascia oraria. La richiesta verrà inviata al ristorante.
                </p>
            </div>

            <div class="booking-grid">

                <!-- RIQUADRO BIANCO DATI PRENOTAZIONE -->
                <div class="order-panel">
                    <header class="order-header">
                        <h2>Dati prenotazione</h2>
                    </header>

                    <form id="booking-form" class="booking-form" action="#" method="POST">
                        <!-- Riga 1: nome + telefono -->
                        <div class="form-group">
                            <label for="nome">Nome e cognome</label>
                            <input type="text" id="nome" name="nome" placeholder="Es. Mario Rossi" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Telefono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="Es. 333 1234567" required>
                        </div>

                        <!-- Riga 2: data + persone -->
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" id="data" name="data" required readonly>
                        </div>

                        <div class="form-group">
                            <label for="persone">Numero di persone</label>
                            <input type="number" id="persone" name="persone" min="1" max="20" required>
                        </div>

                        <input type="hidden" id="orario" name="orario">

                        <div class="form-group">
                            <label for="note">Note (opzionale)</label>
                            <textarea id="note" name="note" rows="3" placeholder="Es. intolleranze, richiesta seggiolone, compleanno..."></textarea>
                        </div>

                        <div class="order-actions booking-actions">
                            <button name="booking-form" type="submit" class="btn-primary" id="btn-prenota">Invia richiesta di prenotazione</button>
                        </div>

                        <p id="booking-message" class="booking-message"></p>
                    </form>
                </div>

                <!-- SECONDO RIQUADRO: CALENDARIO + FASCE ORARIE -->
                <aside class="order-panel booking-slots">
                    <h3>Calendario disponibilità</h3>
                    <p class="booking-slots-info">
                        Scegli il giorno e l'orario dal calendario.
                    </p>

                    <div id="calendar-container" class="calendar-container">
                        <!-- il calendario viene generato via JavaScript -->
                    </div>

                    <div id="slots-container" class="slots-container">
                        <!-- qui compariranno i bottoni orario -->
                    </div>

                    <p id="slots-note" class="slots-note"></p>
                </aside>

            </div>
        </div>
    </section>
</main>

</body>
</html>
