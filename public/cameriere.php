<?php
require_once "../php/includes/header.php";
require_once "../php/includes/session.php"; 
require_once "../php/class/Ordine.php";
require_once "../php/class/Tavolo.php";
require_once "../php/class/Piatto.php";
require_once "../php/class/Prenotazione.php";


$header = new Header();


if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'cameriere') {
    header("Location: login.php");
    exit;
}

// Tavolo selezionato
$id_tavolo = isset($_GET['tavolo']) ? intval($_GET['tavolo']) : 0;

// Lista tavoli
$tavoloObj = new Tavolo();
$listaTavoli = $tavoloObj->getTavoli();

// Lista prenotazioni
$prenotazioni = new Prenotazione();
$prenotazioni = $prenotazioni->getPrenotazioni();

// Menu Piatti
$piattoObj = new Piatto();
$menu = $piattoObj->getMenu();

// Raggruppo piatti per categoria
$piattiPerCategoria = [];
foreach ($menu as $p) {
    $piattiPerCategoria[$p['categoria']][] = $p;
}

// Piatti già ordinati (se ci sono)
$ordine = new Ordine();
$piattiOrdine = $ordine->getPiattiDaOrdine($id_tavolo);
?>

<?php echo $header->render('user'); ?> 

<header class="waiter-topbar">
    <div class="container topbar-inner">
        <strong class="cameriere-title">🍽️ Cameriere: <?= htmlspecialchars($_SESSION['email']); ?></strong>
        <div class="topbar-center">
            <strong>Nuovo Ordine</strong><br>
            <select id="select-tavolo" onchange="vaiAlTavolo(this)">
                <option value="">Seleziona Tavolo</option>
                <?php foreach ($listaTavoli as $t): ?>
                    <option value="<?= $t['id_tavolo'] ?>" <?= $id_tavolo == $t['id_tavolo'] ? 'selected' : '' ?>>
                        Tavolo <?= htmlspecialchars($t['numero']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</header>

<div class="tab-container">
    <button class="tab-btn active" data-tab="OrdiniContainer">Ordini</button>
    <button class="tab-btn" data-tab="GestioneSalaContainer">Gestione Sala</button>
</div>

<main class="container waiter-layout">

    <div id="OrdiniContainer" class="tab-content active" >
    
        <section id="MenuPanel" class="menu-panel">
            <h2>Menu</h2>
        
            <?php foreach ($piattiPerCategoria as $categoria => $piatti): ?>
                <div class="menu-category">
                    <h3><?= htmlspecialchars($categoria) ?></h3>
                    <ul>
                        <?php foreach ($piatti as $p): 
                            $id = intval($p['id_piatto']);
                            $price = floatval($p['prezzo']);
                            $title = htmlspecialchars($p['nome'], ENT_QUOTES);
                        ?>
                        <li class="dish" data-id="<?= $id ?>" data-price="<?= $price ?>" data-title="<?= $title ?>">
                            <strong><?= $title ?></strong>
                            <small><?= number_format($price, 2, ',', '') ?> €</small>
                            <button class="dish-add">+</button>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </section>


        <aside id="OrdineRiepilogo" class="order-panel">

            <header class="order-header">
                <h2 id="ordine-titolo">Ordine Tavolo <?= $id_tavolo > 0 ? $id_tavolo : '' ?></h2>
            </header>

            <ul class="order-items" id="order-items">
            <?php if (!empty($piattiOrdine)): ?>
                <?php foreach ($piattiOrdine as $p): 
                    $id = $p['id_piatto'];
                    $price = $p['prezzo'];
                    $title = $p['nome'];
                    $qty = $p['quantita'];
                    if ($id <= 0) continue;
                ?>
                <li class="order-item" data-id="<?= $id ?>" data-price="<?= $price ?>">
                    <span class="order-item-name"><?= htmlspecialchars($title, ENT_QUOTES) ?></span>
                    <div class="qty-controls">
                        <button class="qty-minus">-</button>
                        <span class="qty"><?= $qty ?></span>
                        <button class="qty-plus">+</button>
                    </div>
                    <strong class="order-item-price"><?= number_format($price, 2, ',', '') ?> €</strong>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>

            <div>
                <label for="note-cucina">Note per la cucina:</label>
                <textarea id="note-cucina" rows="3" placeholder="Es. senza sale, più cottura..."></textarea>
            </div>

            <div class="order-footer">
                <div class="order-total">
                    <span>Totale stimato</span>
                    <strong id="order-total">0 €</strong>
                </div>

                <div class="order-actions">
                    <button class="btn-secondary" id="btn-svuota">Svuota</button>
                    <button class="btn-primary" id="btn-invia">Invia in cucina</button>
                </div>
            </div>
        </aside>
    </div>
    
    <div id="GestioneSalaContainer" class="tab-content waiter-layout-gestion" style="display: none;">

        <section id="gestionePrenotazioni" class="menu-panel">

            <header class="order-header">
                <h2>Gestione Prenotazioni</h2>
            </header>
            
            <ul class="order-items">
                <?php foreach ($prenotazioni as $p): ?>
                    <li value="<?= $p['id_prenotazione'] ?>">
                        <h3>Prenotazione #<?= htmlspecialchars($p['id_prenotazione']); ?></h3>
                        <p>Cliente: <strong><?= htmlspecialchars($p['nome']); ?></strong></p>
                        <p>Data: <strong><?= htmlspecialchars($p['data']); ?></strong> Ora: <strong><?= htmlspecialchars($p['fascia_oraria']); ?></strong></p>
                        <div class="order-actions">
                            <button class="btn-secondary">Assegna</button>
                            <button class="btn-primary">Modifica</button>
                            <button class="btn-primary">Elimina</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>


        <aside id="gestioneTavoli" class="order-panel">

            <header class="order-header">
                <h2>Gestione Tavoli</h2>
            </header>
            
            <div id="tavoli-container">
                <?php foreach ($listaTavoli as $t): ?>
                    <div class="tavolo-card" data-id="<?= $t['id_tavolo'] ?>">
                        <h3>Tavolo <?= htmlspecialchars($t['numero']); ?></h3>
                        <p>Stato: <strong><?= htmlspecialchars($t['stato']); ?></strong></p>
                        <div class="order-actions">
                            <button class="btn-secondary" onclick="cambiaStato(<?= $t['id_tavolo'] ?>, 'libero')">Libero</button>
                            <button class="btn-primary" onclick="cambiaStato(<?= $t['id_tavolo'] ?>, 'occupato')">Occupato</button>
                            <button class="btn-primary" onclick="cambiaStato(<?= $t['id_tavolo'] ?>, 'Sparecchiato')">Sparecchiato</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </aside>
    </div>

</main>


<script>
document.addEventListener("DOMContentLoaded", function () {
    
    // Gestione delle schermate
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const targetTabId = btn.dataset.tab;

            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            tabContents.forEach(c => c.classList.remove("active"));
            tabContents.forEach(c => c.style.display = 'none');

            const targetContent = document.getElementById(targetTabId);
            if (targetContent) {
                targetContent.classList.add("active"); 
                targetContent.style.display = 'flex';
            }
        });
    });

    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
        const initialTarget = document.getElementById(activeTab.dataset.tab);
        if (initialTarget) {
            initialTarget.style.display = 'flex';
        }
    }

});
</script>

</body>
</html>