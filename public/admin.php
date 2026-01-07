<?php
require_once "../php/includes/header.php"; 
require_once "../php/class/Piatto.php";
require_once "../php/class/Utente.php";
require_once "../php/includes/session.php";
require_once "../php/class/Tavolo.php";
require_once "../php/class/Statistiche.php";

if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.php");
    exit;
}



$st = new Statistiche(); 
$incassoOggi = $st->getIncassoOggi() ?: 0;
$incassoMese = $st->getIncassoMese() ?: 0;

$datiGrafico = $st->getIncassiAnnuali();
$nomiMesi = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
$incassiAnnuali = array_fill(0, 12, 0);
if (!empty($datiGrafico)) {
    foreach ($datiGrafico as $mese => $valore) {
        $incassiAnnuali[$mese - 1] = $valore;
    }
}

$incassoTotaleStorico = $st->getIncassoTotaleStorico() ?: 0;
$mediaPrenotazioni = $st->getMediaPrenotazioni();
$piattiDalDB = $st->getTopPiatti();
$affluenzaRaw = $st->getAffluenzaOraria();

$fasceOrarie = [];
$maxPrenotazioni = 0;
foreach($affluenzaRaw as $a) {
    if($a['totale'] > $maxPrenotazioni) $maxPrenotazioni = $a['totale'];
}

foreach($affluenzaRaw as $a) {
    $percentuale = ($maxPrenotazioni > 0) ? ($a['totale'] / $maxPrenotazioni) * 100 : 0;
    $fasceOrarie[] = [
        'ora' => $a['fascia'],
        'carico' => round($percentuale)
    ];
}
$piattiPopolari = !empty($piattiDalDB) ? $piattiDalDB : [];

$utente = new Utente();
$utenti = $utente->getUtenti();

$piatto = new Piatto();
$menu = $piatto->getMenu(); 

$tavoloObj = new Tavolo();
$tavoli = $tavoloObj->getTavoli();

$piattiPerCategoria = [];
foreach ($menu as $p) {
    $piattiPerCategoria[$p['categoria']][] = $p;
}

$piattoDaModificare = null;
$utenteDaModificare = null;
$tavoloDaModificare = null;

//Switch tra le varie azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Gestione tavoli
    if (isset($_POST['modificaTavolo'])) {
        $id = intval($_POST['modificaTavolo']);
        $tavoloDaModificare = $tavoloObj->getTavolo($id); 
    } elseif (isset($_POST['eliminaTavolo'])) {
        $id = intval($_POST['eliminaTavolo']);
        $tavoloObj->eliminaTavolo($id);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['numeroTavolo'], $_POST['capacita_max'], $_POST['stato'])) {
        $numero = intval($_POST['numeroTavolo']);
        $capacita = intval($_POST['capacita_max']);
        $stato = $_POST['stato'];

        if (!empty($_POST['id_tavolo'])) {
            $id_tavolo = intval($_POST['id_tavolo']);
            $tavoloObj->modificaTavolo($id_tavolo, $numero, $capacita, $stato);
        } else {
            $tavoloObj->aggiungiTavolo($numero, $capacita, $stato);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    //Gestione piatti
    if (isset($_POST['modifica'])) {
        $id = intval($_POST['modifica']);
        $piattoDaModificare = $piatto->getPiatto($id);
    } elseif (isset($_POST['elimina'])) {
        $id = intval($_POST['elimina']);
        $piatto->eliminaPiatto($id);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif(isset($_POST['nomePiatto'], $_POST['descrizione'], $_POST['prezzo'], $_POST['categoria'], $_POST['img'])) {
        $nome = $_POST['nomePiatto'];
        $descrizione = $_POST['descrizione'];
        $prezzo = floatval($_POST['prezzo']);
        $categoria = $_POST['categoria'];
        $img = $_POST['img'];

        if (!empty($_POST['id_piatto'])) {
            $id_piatto = intval($_POST['id_piatto']);
            $piatto->modificaPiatto($id_piatto, $nome, $descrizione, $prezzo, $categoria, $img);
        } else {
            $piatto->aggiungiPiatto($nome, $descrizione, $prezzo, $categoria, $img);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    //Gestione utenti 
    if (isset($_POST['modificaUsr'])) {
        $id = intval($_POST['modificaUsr']);
        $utenteDaModificare = $utente->getUtente($id);
    } elseif (isset($_POST['eliminaUsr'])) {
        $id = intval($_POST['eliminaUsr']);
        $utente->eliminaUtente($id); 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['ruolo'])) {
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $email = $_POST['email'];
        $ruolo = $_POST['ruolo'];
        $password = $_POST['password'] ?? null;

        if (!empty($_POST['id_utente'])) {
            $id_utente = intval($_POST['id_utente']);
            $utente->modificaUtente($id_utente, $email, $password, $ruolo, $nome, $cognome);
        } else {
            $utente->aggiungiUtente($email, $password, $ruolo, $nome, $cognome);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
$header = new Header();
echo $header->render('user');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="tab-container">
    <button class="tab-btn <?php echo (!$piattoDaModificare && !$utenteDaModificare && !$tavoloDaModificare) ? 'active' : ''; ?>" data-tab="tavoli">Gestione Tavoli</button>
    <button class="tab-btn <?php echo ($piattoDaModificare) ? 'active' : ''; ?>" data-tab="menu">Gestione Menu</button>
    <button class="tab-btn" data-tab="Statistiche">Statistiche</button>
    <button class="tab-btn" data-tab="Pagamenti">Incassi</button>
    <button class="tab-btn <?php echo ($utenteDaModificare) ? 'active' : ''; ?>" data-tab="account">Account</button>
</div>

<main class="container">

    <!-- GESTIONE TAVOLI -->
    <div id="tavoli" class="tab-content <?php echo (!$piattoDaModificare && !$utenteDaModificare && !$tavoloDaModificare) ? 'active' : ''; ?>">
        <div class="section-header">
            <h2 class="section-title">Gestione Tavoli</h2>
            <p class="section-subtitle">Configura il numero, la capacità e lo stato dei tavoli.</p>
        </div>

        <?php foreach ($tavoli as $t) { ?>
        <div class="card" style="max-width: 500px; margin: auto; margin-bottom: 20px;">
            <div class="card-body">
                <div class="card-title-row">
                    <h3 class="card-title">Tavolo n. <?php echo htmlspecialchars($t['numero']); ?></h3>
                </div>
                <p class="card-text">Capacità Max: <?php echo htmlspecialchars($t['capacita_max']); ?> persone</p>
                <p class="card-text">Stato: <?php echo htmlspecialchars($t['stato']); ?></p>
                <div class="hero-buttons">
                    <form method="post" action="">
                      <button type="submit" class="btn-secondary" name="modificaTavolo" value="<?php echo $t['id_tavolo']; ?>">Modifica</button>
                      <button type="submit" class="btn-primary" name="eliminaTavolo" value="<?php echo $t['id_tavolo']; ?>">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="section-cta-center"> 
          <button id="addTavoloBtn" class="btn-primary" data-tab="addTavolo">+ Aggiungi nuovo tavolo</button> 
        </div>
    </div>

    <section id="addTavolo" class="tab-content <?php echo $tavoloDaModificare ? 'active' : ''; ?>">
        <div class="login-container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $tavoloDaModificare ? 'Modifica Tavolo' : 'Aggiungi Nuovo Tavolo'; ?></h2>
                <p class="section-subtitle">Compila il modulo per gestire il tavolo.</p>
            </div>

            <form method="post" action="admin.php" class="admin-form">
                <input type="hidden" name="id_tavolo" value="<?php echo $tavoloDaModificare['id_tavolo'] ?? ''; ?>">

                <input type="number" name="numeroTavolo" placeholder="numero tavolo" required
                       value="<?php echo htmlspecialchars($tavoloDaModificare['numero'] ?? ''); ?>">

                <input type="number" name="capacita_max" placeholder="capacità massima" required
                       value="<?php echo htmlspecialchars($tavoloDaModificare['capacita_max'] ?? ''); ?>">

                <select name="stato" required>
                    <option value="Libero" <?php if (($tavoloDaModificare['stato'] ?? '') === 'Libero') echo 'selected'; ?>>libero</option>
                    <option value="Prenotato" <?php if (($tavoloDaModificare['stato'] ?? '') === 'Prenotato') echo 'selected'; ?>>prenotato</option>
                </select>

                <div class="form-cta-center">
                    <button type="submit" class="btn-primary">
                        <?php echo $tavoloDaModificare ? 'Modifica Tavolo' : 'Aggiungi Tavolo'; ?>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- GESTIONE MENU -->
    <section id="menu" class="tab-content <?php echo ($piatto->getMenu() && !$piattoDaModificare && !$tavoloDaModificare && !$utenteDaModificare && isset($_GET['tab']) && $_GET['tab'] == 'menu') ? 'active' : ''; ?>">
        <div class="section-header">
            <h2 class="section-title">Gestione Menu</h2>
            <p class="section-subtitle">Aggiungi, modifica o rimuovi piatti dal menù.</p>
        </div>

        <?php foreach ($piattiPerCategoria as $categoria => $piatti) { ?>
        <section id="<?php echo strtolower($categoria); ?>">
            <div class="container">
                <h2 class="section-title"><?php echo $categoria; ?></h2>
                <div class="cards-grid">
                    <?php foreach ($piatti as $p) { ?>
                    <article class="card">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['nome']; ?>">
                        <div class="card-body">
                            <div class="card-title-row">
                                <h3 class="card-title"><?php echo $p['nome']; ?></h3>
                                <span class="card-price">€ <?php echo $p['prezzo']; ?></span>
                            </div>
                            <p class="card-text"><?php echo $p['descrizione']; ?></p>
                            <div class="hero-buttons">
                                <form method="post" action="">
                                    <button type="submit" class="btn-secondary" name="modifica" value="<?php echo $p['id_piatto']; ?>">Modifica</button>
                                    <button type="submit" class="btn-primary" name="elimina" value="<?php echo $p['id_piatto']; ?>">Elimina</button>
                                </form>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
            </div>
        </section>
        <?php } ?>
        <div class="section-cta-center"> 
          <button id="addDishBtn" class="btn-primary" data-tab="addDish">+ Aggiungi nuovo piatto</button> 
        </div>
    </section>

    <section id="addDish" class="tab-content <?php echo $piattoDaModificare ? 'active' : ''; ?>">
        <div class="login-container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $piattoDaModificare ? 'Modifica Piatto' : 'Aggiungi Nuovo Piatto'; ?></h2>
                <p class="section-subtitle">Compila il modulo per aggiungere o modificare un piatto.</p>
            </div>

            <form method="post" action="admin.php" class="admin-form">
                <input type="hidden" name="id_piatto" value="<?php echo $piattoDaModificare['id_piatto'] ?? ''; ?>">
                <input type="text" id="nome" name="nomePiatto" placeholder="nome del piatto" required value="<?php echo htmlspecialchars($piattoDaModificare['nome'] ?? ''); ?>">
                <textarea id="descrizione" name="descrizione" placeholder="descrizione" required><?php echo htmlspecialchars($piattoDaModificare['descrizione'] ?? ''); ?></textarea>
                <input type="number" id="prezzo" name="prezzo" placeholder="prezzo" required value="<?php echo htmlspecialchars($piattoDaModificare['prezzo'] ?? ''); ?>">
                <select id="categoria" name="categoria" required>
                    <option value="Primi" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Primi') echo 'selected'; ?>>Primi</option>
                    <option value="Secondi" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Secondi') echo 'selected'; ?>>Secondi</option>
                    <option value="Dolci" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Dolci') echo 'selected'; ?>>Dolci</option>
                </select>
                <input type="text" id="img" name="img" accept="image/*" placeholder="indirizzo immagine" required value="<?php echo htmlspecialchars($piattoDaModificare['img'] ?? ''); ?>">
                <div class="form-cta-center">
                    <button type="submit" class="btn-primary"><?php echo $piattoDaModificare ? 'Modifica Piatto' : 'Aggiungi Piatto'; ?></button>
                </div>
            </form>
        </div>
    </section>

    <!-- GESTIONE STATISTICHE -->
    <section id="Statistiche" class="tab-content">
        <div class="stats-grid">
            <div class="card-gr stat-card">
                <h3>Media Coperti</h3>
                <div class="stat-value"><?php echo $mediaPrenotazioni; ?></div>
                <p class="text-muted">Clienti medi per tavolo</p>
            </div>
            <div class="card-gr">
                <h3>Affluenza Oraria</h3>
                <?php foreach($fasceOrarie as $f): ?>
                <div style="margin-bottom:15px">
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem">
                        <strong><?php echo $f['ora']; ?></strong><span><?php echo $f['carico']; ?>%</span>
                    </div>
                    <div class="p-bar-bg"><div class="p-bar-fill" style="width:<?php echo $f['carico']; ?>%"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card-gr">
            <h3>Top Piatti più Venduti</h3>
            <table class="top-table">
                <?php $i=1; foreach($piattiPopolari as $p): 
                    $c = ($i==1)?'gold':(($i==2)?'silver':(($i==3)?'bronze':'neutral')); ?>
                <tr>
                    <td width="50"><span class="rank-badge <?php echo $c; ?>"><?php echo $i; ?></span></td>
                    <td><strong><?php echo $p['nome']; ?></strong></td>
                    <td style="text-align:right"><strong><?php echo $p['ordini']; ?></strong> ordini</td>
                </tr>
                <?php $i++; endforeach; ?>
            </table>
        </div>
    </section>

    <!-- GESTIONE INCASSI -->
    <section id="Pagamenti" class="tab-content">
        <div class="stats-grid">
            <div class="card-gr stat-card" style="border-top-color:#10b981">
                <h3>Incasso Oggi</h3>
                <div class="stat-value" style="color:#10b981">€ <?php echo number_format($incassoOggi, 2, ',', '.'); ?></div>
            </div>
            <div class="card-gr stat-card" style="border-top-color:#3b82f6">
                <h3>Incasso Mese</h3>
                <div class="stat-value" style="color:#3b82f6">€ <?php echo number_format($incassoMese, 2, ',', '.'); ?></div>
            </div>
        </div>

        <div class="card">
            <h3>Andamento Fatturato Mensile</h3>
            <div class="chart-box">
                <canvas id="incassiChart"></canvas>
            </div>
        </div>

        <div class="card-gr stat-card" style="margin-top: 20px; border-top-color:#f59e0b">
            <h3>Volume d'Affari Totale</h3>
            <div class="stat-value" style="color:#f59e0b; font-size:3.5rem">€ <?php echo number_format($incassoTotaleStorico, 2, ',', '.'); ?></div>
            <button class="btn-primary" onclick="window.print()">Stampa Report</button>
        </div>
    </section>


    <!-- GESTIONE ACCOUNT UTENTI -->
    <section id="account" class="tab-content <?php echo ($utenti && !$utenteDaModificare && !$piattoDaModificare && !$tavoloDaModificare && isset($_GET['tab']) && $_GET['tab'] == 'account') ? 'active' : ''; ?>">
        <div class="section-header">
            <h2 class="section-title">Lista Utenti</h2>
            <p class="section-subtitle">Crea/Modifica/Elimina credenziali di accesso.</p>
        </div>
        <?php foreach ($utenti as $u) { ?>
        <div class="card" style="max-width: 500px; margin: auto; margin-bottom: 20px;">
            <div class="card-body">
                <div class="card-title-row">
                    <h3 class="card-title"><?php echo htmlspecialchars($u['nome'] . ' ' . $u['cognome']); ?></h3>
                </div>
                <p class="card-text">Email: <?php echo htmlspecialchars($u['email']); ?></p>
                <p class="card-text">Ruolo: <?php echo htmlspecialchars($u['ruolo']); ?></p>
                <div class="hero-buttons">
                    <form method="post" action="">
                      <button type="submit" class="btn-secondary" name="modificaUsr" value="<?php echo $u['id_utente']; ?>">Modifica Dati</button>
                      <button type="submit" class="btn-primary" name="eliminaUsr" value="<?php echo $u['id_utente']; ?>">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="section-cta-center"> 
          <button id="addUsrBtn" class="btn-primary" data-tab="addUsr">+ Aggiungi nuovo utente</button> 
        </div>
    </section>

    <section id="addUsr" class="tab-content <?php echo $utenteDaModificare ? 'active' : ''; ?>">
        <div class="login-container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $utenteDaModificare ? 'Modifica Utente' : 'Aggiungi Nuovo Utente'; ?></h2>
                <p class="section-subtitle">Compila il modulo per aggiungere o modificare un utente.</p>
            </div>
            <form method="post" action="admin.php" class="admin-form">
                <input type="hidden" name="id_utente" value="<?php echo $utenteDaModificare['id_utente'] ?? ''; ?>">
                <input type="text" id="nome" name="nome" placeholder="nome" required value="<?php echo htmlspecialchars($utenteDaModificare['nome'] ?? ''); ?>">
                <input type="text" id="cognome" name="cognome" placeholder="cognome" required value="<?php echo htmlspecialchars($utenteDaModificare['cognome'] ?? ''); ?>">
                <input type="email" id="email" name="email" placeholder="email" required value="<?php echo htmlspecialchars($utenteDaModificare['email'] ?? ''); ?>">
                
                <button type="button" id="btnCambiaPassword" class="btn-primary" onclick="mostraInputPassword()" style="<?php echo $utenteDaModificare ? 'display:block;' : 'display:none;'; ?> border-radius: 12px;">Cambia password</button>
                <input type="password" id="password" name="password" style="<?php echo $utenteDaModificare ? 'display:none;' : 'display:block;'; ?>" value="<?php echo htmlspecialchars($utenteDaModificare['password'] ?? ''); ?>" placeholder="Nuova password" <?php echo $utenteDaModificare ? '' : 'required'; ?>>
                           
                <select id="ruolo" name="ruolo" required>
                    <option value="admin" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="cuoco" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'cuoco') echo 'selected'; ?>>Cuoco</option>
                    <option value="cameriere" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'cameriere') echo 'selected'; ?>>Cameriere</option>
                </select>

                <div class="form-cta-center">
                    <button type="submit" class="btn-primary"><?php echo $utenteDaModificare ? 'Modifica Utente' : 'Aggiungi Utente'; ?></button>
                </div>
            </form>
        </div>
    </section>

</main>

<script>
    document.querySelectorAll('[data-tab]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab');
            
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            const targetElement = document.getElementById(target);
            if (targetElement) {
                targetElement.classList.add('active');
            }

            if(btn.classList.contains('tab-btn')) {
                btn.classList.add('active');
            } else {
                let mainTab = "";
                if(target === "addTavolo") mainTab = "tavoli";
                if(target === "addDish") mainTab = "menu";
                if(target === "addUsr") mainTab = "account";
                
                if(mainTab) {
                    document.querySelector(`[data-tab="${mainTab}"]`).classList.add('active');
                }
            }
        });
    });

    function mostraInputPassword() {
        document.getElementById("password").style.display = "block";
        document.getElementById("btnCambiaPassword").style.display = "none";
    }

    // Grafico Incassi Annuali
    const ctx = document.getElementById('incassiChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nomiMesi); ?>,
            datasets: [{
                label: 'Incassi (€)',
                data: <?php echo json_encode(array_values($incassiAnnuali)); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 2,
                borderRadius: 5,
                hoverBackgroundColor: 'rgba(52, 152, 219, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '€' + value.toLocaleString('it-IT');
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Incasso: €' + context.parsed.y.toLocaleString('it-IT');
                        }
                    }
                }
            }
        }
    });
</script>