<?php
require_once "../php/includes/header.php"; 
require_once "../php/includes/session.php";
require_once "../php/class/Statistiche.php";

// Controllo sicurezza
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- DATI (Simulati, pronti per essere sostituiti dalle tue chiamate DB) ---
$incassoOggi = 845.50; 
$incassoMese = 12450.00; 
$incassoTotaleStorico = 342500.75; 
$mediaPrenotazioni = 24.5;

$incassiAnnuali = [10200, 9800, 15500, 13200, 15600, 18900, 22400, 24500, 17200, 14100, 12800, 21000];
$nomiMesi = ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic"];

$piattiPopolari = [
    ['nome' => 'Carbonara', 'ordini' => 150],
    ['nome' => 'Tiramisù', 'ordini' => 120],
    ['nome' => 'Amatriciana', 'ordini' => 95],
    ['nome' => 'Lasagna', 'ordini' => 88],
    ['nome' => 'Gricia', 'ordini' => 70]
];

$fasceOrarie = [
    ['ora' => 'Pranzo', 'carico' => 75],
    ['ora' => 'Cena', 'carico' => 100],
    ['ora' => 'Aperitivo', 'carico' => 40]
];

$header = new Header();
echo $header->render('user');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
    .stat-card { text-align: center; border-top: 5px solid #3498db; }
    .stat-value { font-size: 2.8rem; font-weight: 800; color: #2c3e50; margin: 10px 0; }
    
    /* Tabella Top 10 */
    .top-table { width: 100%; border-collapse: collapse; }
    .top-table td, .top-table th { padding: 12px; border-bottom: 1px solid #f0f0f0; }
    .rank-badge { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 0.8rem; }
    .gold { background: #FFD700; color: #7d6608; }
    .silver { background: #E0E0E0; color: #555; }
    .bronze { background: #EABE8E; color: #7d4d1e; }
    .neutral { background: #f0f0f0; color: #999; }

    /* Container grafico */
    .chart-box { position: relative; height: 350px; width: 100%; }

    /* Barre Affluenza */
    .p-bar-bg { background: #eee; border-radius: 20px; height: 10px; margin-top: 8px; overflow: hidden; }
    .p-bar-fill { background: #3498db; height: 100%; }
</style>

<div class="tab-container">
    <button class="tab-btn active" onclick="openTab(event, 'Statistiche')">Statistiche</button>
    <button class="tab-btn" onclick="openTab(event, 'Pagamenti')">Pagamenti</button>
</div>

<main class="container">

    <section id="Statistiche" class="tab-content active">
        <div class="stats-grid">
            <div class="card stat-card">
                <h3>Media Coperti</h3>
                <div class="stat-value"><?php echo $mediaPrenotazioni; ?></div>
                <p class="text-muted">Clienti medi per tavolo</p>
            </div>
            <div class="card">
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

        <div class="card">
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

    <section id="Pagamenti" class="tab-content" style="display:none">
        <div class="stats-grid">
            <div class="card-gr stat-card" style="border-top-color:#10b981">
                <h3>Incasso Oggi</h3>
                <div class="stat-value" style="color:#10b981">€ <?php echo number_format($incassoOggi, 2, ',', '.'); ?></div>
            </div>
            <div class="card stat-card" style="border-top-color:#3b82f6">
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

        <div class="card stat-card" style="border-top-color:#f59e0b">
            <h3>Volume d'Affari Totale</h3>
            <div class="stat-value" style="color:#f59e0b; font-size:3.5rem">€ <?php echo number_format($incassoTotaleStorico, 2, ',', '.'); ?></div>
            <button class="btn-primary" onclick="window.print()">Stampa Report</button>
        </div>
    </section>

</main>

<script>
    // Gestione Tab
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.classList.add("active");
    }

    // Configurazione Chart.js
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