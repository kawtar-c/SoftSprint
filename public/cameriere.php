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

$id_tavolo = isset($_GET['tavolo']) ? intval($_GET['tavolo']) : 0;

$tavoloObj = new Tavolo();
$listaTavoli = $tavoloObj->getTavoli();

$prenotazioni = new Prenotazione();
$prenotazioneDaModificare = null;
$prenotazioni_list = $prenotazioni->getPrenotazioni();

$id_prenotazione_da_modificare = isset($_GET['modifica_id']) ? intval($_GET['modifica_id']) : 0;

if ($id_prenotazione_da_modificare > 0) {
    $prenotazioneDaModificare = $prenotazioni->getPrenotazioneById($id_prenotazione_da_modificare);
}

$piattoObj = new Piatto();
$menu = $piattoObj->getMenu();

$piattiPerCategoria = [];
foreach ($menu as $p) {
    $piattiPerCategoria[$p['categoria']][] = $p;
}

$ordine = new Ordine();
$piattiOrdine = $ordine->getPiattiDaOrdine($id_tavolo);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $pre=new Prenotazione();
    if (isset($_POST['assegna'])) {
        $id_prenotazione = intval($_POST['id_prenotazione']);
        $id_tavolo_assegnato = intval($_POST['assegna']);
        $pre=$pre->assegnaTavolo($id_prenotazione, $id_tavolo_assegnato);
        header("Location: " . $_SERVER['PHP_SELF'] . "#GestioneSalaContainer");
        exit;
    } elseif (isset($_POST['modifica'])) {
        $id = intval($_POST['modifica']);
        header("Location: " . $_SERVER['PHP_SELF'] . "?modifica_id=" . $id . "#GestioneSalaContainer");
        exit;
    } elseif(isset($_POST['elimina'])) {
        $id = intval($_POST['elimina']);
        $pre=$pre->eliminaPrenotazione($id);
        header("Location: " . $_SERVER['PHP_SELF'] . "#GestioneSalaContainer");
        exit;
    } elseif(isset($_POST['ModificaPrenotazione'])) {
        $id_prenotazione = intval($_POST['id_prenotazione'] ?? 0);
        $nome = $_POST['nome_cliente'] ?? '';
        $telefono = $_POST['telefono_cliente'] ?? '';
        $data = $_POST['data_prenotazione'] ?? '';
        $ora = $_POST['ora_prenotazione'] ?? '';
        $persone = intval($_POST['persone'] ?? 1);
        $pre=$pre->modificaPrenotazione($id_prenotazione, $nome, $telefono, $data, $persone, $ora);
        header("Location: " . $_SERVER['PHP_SELF'] . "#GestioneSalaContainer");
        exit;
    } elseif(isset($_POST['AggiungiPrenotazione'])) {
        $nome = $_POST['nome_cliente'] ?? '';
        $telefono = $_POST['telefono_cliente'] ?? '';
        $data = $_POST['data_prenotazione'] ?? '';
        $ora = $_POST['ora_prenotazione'] ?? '';
        $persone = intval($_POST['persone'] ?? 1);
        $pre=$pre->aggiungiPrenotazione($nome, $telefono, $data, $persone, $ora);
        header("Location: " . $_SERVER['PHP_SELF'] . "#GestioneSalaContainer");
        exit;
    }
}
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

        <section id="gestionePrenotazioni" class="menu-panel" style="max-height: calc(100vh - 40px);">

            <header class="order-header">
                <h2>Gestione Prenotazioni</h2>
            </header>
            
            <ul class="order-items">
                <?php foreach ($prenotazioni_list as $p): 
                    $prenotazione_id = htmlspecialchars($p['id_prenotazione']);
                    $dropdown_id = "dropdownTavoliContent_" . $prenotazione_id;
                ?>
                    <li value="<?= $p['id_prenotazione'] ?>">
                        <h3>Prenotazione #<?= $prenotazione_id; ?></h3>
                        <p>Cliente: <strong><?= htmlspecialchars($p['nome']); ?></strong></p>
                        <p>Telefono: <strong><?= htmlspecialchars($p['telefono']); ?></strong></p>
                        <p>Data: <strong><?= htmlspecialchars($p['data']); ?></strong> Ora: <strong><?= htmlspecialchars($p['fascia_oraria']); ?></strong></p>
                        
                        <div class="order-actions" style="margin-top: 10px;">
                            <form action="cameriere.php" method="post">
                                <button name="modifica" value="<?= $prenotazione_id; ?>" class="btn-primary">Modifica</button>
                                <button name="elimina" value="<?= $prenotazione_id; ?>" class="btn-primary">Elimina</button>
                            </form>
                        </div>

                        <div class="custom-dropdown-container" style="margin-top: 10px;">
                            
                            <button class="dropdown-toggle btn-secondary" id="dropdownTavoliBtn_<?= $prenotazione_id; ?>" 
                                    onclick="toggleDropdown('<?= $dropdown_id; ?>')">
                                Assegna Tavolo ▾
                            </button>

                            <div class="dropdown-menu" id="<?= $dropdown_id; ?>" style="display: none;">
                                <ul style="list-style-type: none; padding: 0; margin: 0;">
                                    <li style="margin: 5px;">
                                        <form action="cameriere.php" method="post">
                                            <input type="hidden" name="id_prenotazione" value="<?= $prenotazione_id; ?>">
                                            <?php foreach ($listaTavoli as $t): ?>
                                                <button 
                                                    name="assegna"
                                                    class="dropdown-item tavolo-btn btn-secondary" 
                                                    value="<?= htmlspecialchars($t['id_tavolo']); ?>">
                                                    Tavolo <?= htmlspecialchars($t['numero']); ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </form>
                                    </li>
                                </ul> 
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <aside id="modPren" >
            <div class="login-container" style="margin-top: 0px; width: 100%;">
                <div class="section-header">
                    <h2 class="section-title"><?php echo $prenotazioneDaModificare ? 'Modifica Prenotazione' : 'Aggiungi Nuova Prenotazione'; ?></h2>
                    <p class="section-subtitle">Compila il modulo per aggiungere o modificare una prenotazione.</p>
                </div>

                <form method="post" action="cameriere.php" class="admin-form">
                    <input type="hidden" name="id_prenotazione" value="<?php echo htmlspecialchars($prenotazioneDaModificare['id_prenotazione'] ?? ''); ?>">

                    <input type="text" id="nome_cliente" name="nome_cliente" placeholder="Nome Cliente" required
                         value="<?php echo htmlspecialchars($prenotazioneDaModificare['nome'] ?? ''); ?>">

                    <input type="tel" id="telefono_cliente" name="telefono_cliente" placeholder="Telefono Cliente" required
                         value="<?php echo htmlspecialchars($prenotazioneDaModificare['telefono'] ?? ''); ?>">

                    <input type="date" id="data_prenotazione" name="data_prenotazione" placeholder="Data" required
                         value="<?php echo htmlspecialchars($prenotazioneDaModificare['data'] ?? ''); ?>">

                    <input type="time" id="ora_prenotazione" name="ora_prenotazione" placeholder="Ora" required
                         value="<?php echo htmlspecialchars($prenotazioneDaModificare['fascia_oraria'] ?? ''); ?>">

                    <input type="number" id="persone" name="persone" placeholder="Numero Persone" required min="1"
                         value="<?php echo htmlspecialchars($prenotazioneDaModificare['numero_persone'] ?? ''); ?>">
                    
                    <select id="Tavolo" name="id_tavolo">
                        <option value="">-- Seleziona Tavolo (Opzionale) --</option>
                        <?php 
                        
                        foreach ($listaTavoli as $t): 
                            $selected = (($prenotazioneDaModificare['id_tavolo'] ?? '') == $t['id_tavolo']) ? 'selected' : '';
                        ?>
                            <option value="<?= $t['id_tavolo'] ?>" <?= $selected ?>>
                                Tavolo <?= htmlspecialchars($t['numero']); ?> (<?= htmlspecialchars($t['stato']); ?>)
                            </option>
                        <?php endforeach; ?>

                    </select>
                    
                    <div class="form-cta-center">
                        <button type="submit" class="btn-primary" name="<?php echo $prenotazioneDaModificare ? 'ModificaPrenotazione' : 'AggiungiPrenotazione'; ?>">
                            <?php echo $prenotazioneDaModificare ? 'Salva Modifiche' : 'Aggiungi Prenotazione'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </aside>
    </div>

</main>


<script>
function vaiAlTavolo(selectElement) {
    if (selectElement.value) {
        window.location.href = 'cameriere.php?tavolo=' + selectElement.value;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    
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

function toggleDropdown(contentId) {
    const content = document.getElementById(contentId); 
    
    if (content) {
        if (content.style.display === "block") {
            content.style.display = "none";
        } else {
            const dropdowns = document.querySelectorAll(".dropdown-menu");
            dropdowns.forEach(c => {
                if (c.id !== contentId) {
                     c.style.display = "none";
                }
            });
            
            content.style.display = "block";
        }
    }
}

document.addEventListener("click", (event) => {
    const containers = document.querySelectorAll(".custom-dropdown-container");
    
    containers.forEach(container => {
        const toggleBtn = container.querySelector('.dropdown-toggle');
        const content = container.querySelector('.dropdown-menu');
        
        if (content && content.style.display === "block" && !toggleBtn.contains(event.target) && !content.contains(event.target)) {
            content.style.display = "none";
        }
    });
});
</script>

</body>
</html>