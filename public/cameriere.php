<?php
// =========================
//  CARICAMENTO CLASSI
// =========================
require_once "../php/includes/header.php";   // classe Header
require_once "../php/includes/session.php";  // session_start() qui dentro
require_once "../php/class/Ordine.php";
require_once "../php/class/Tavolo.php";
require_once "../php/class/Piatto.php";

// =========================
//  OGGETTO HEADER
// =========================
$header = new Header();

// =========================
//  CONTROLLO ACCESSO
// =========================
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'cameriere') {
    header("Location: login.php");
    exit;
}

// =========================
//  RECUPERO DATI
// =========================

// Tavolo selezionato
$id_tavolo = isset($_GET['tavolo']) ? intval($_GET['tavolo']) : 0;

// Lista tavoli
$tavoloObj = new Tavolo();
$listaTavoli = $tavoloObj->getTavoli();

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

<main class="container waiter-layout">

<!-- =========================
     MENU SINISTRA
========================= -->
<section class="menu-panel">
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


<!-- =========================
     PANEL ORDINE DESTRA
========================= -->
<aside class="order-panel">

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
</main>


<!-- =========================
     SCRIPT JS ORDINAZIONE
========================= -->
<script>
function vaiAlTavolo(select){
    const id = select.value;
    window.location.href = id ? "?tavolo=" + id : "cameriere.php";
}

document.addEventListener("DOMContentLoaded", () => {
    const list = document.getElementById("order-items");
    const total = document.getElementById("order-total");
    const selectTavolo = document.getElementById("select-tavolo");

    let tavolo = selectTavolo.value;

    // Titolo dinamico
    document.getElementById("ordine-titolo").textContent =
        "Ordine Tavolo " + (tavolo || "");

    function aggiornaTotale(){
        let t = 0;
        list.querySelectorAll(".order-item").forEach(el=>{
            t += parseFloat(el.dataset.price) *
                 parseInt(el.querySelector(".qty").textContent);
        });
        total.textContent = t.toFixed(2).replace(".", ",") + " €";
    }
    aggiornaTotale();

    // Aggiunta piatti
    document.querySelectorAll(".dish-add").forEach(btn=>{
        btn.onclick = ()=>{
            const d = btn.closest(".dish");
            const id = d.dataset.id;
            const price = parseFloat(d.dataset.price);
            const title = d.dataset.title;

            let exist = [...list.children].find(el => el.dataset.id === id);
            if (exist) {
                exist.querySelector(".qty").textContent =
                    parseInt(exist.querySelector(".qty").textContent) + 1;
            } else {
                const li = document.createElement("li");
                li.className = "order-item";
                li.dataset.id = id;
                li.dataset.price = price;
                li.innerHTML = `
                    <span class="order-item-name">${title}</span>
                    <div class="qty-controls">
                        <button class="qty-minus">-</button>
                        <span class="qty">1</span>
                        <button class="qty-plus">+</button>
                    </div>
                    <strong class="order-item-price">
                        ${price.toFixed(2).replace(".", ",")} €
                    </strong>
                `;
                list.appendChild(li);
            }
            aggiornaTotale();
        };
    });

    // Delegazione ±
    list.onclick = e => {
        if (!e.target.classList.contains("qty-plus") &&
            !e.target.classList.contains("qty-minus")) return;

        const li = e.target.closest(".order-item");
        let qty = parseInt(li.querySelector(".qty").textContent);

        qty += e.target.classList.contains("qty-plus") ? 1 : -1;

        if (qty <= 0) li.remove();
        else li.querySelector(".qty").textContent = qty;

        aggiornaTotale();
    };

    // Svuota ordine
    document.getElementById("btn-svuota").onclick = ()=>{
        list.innerHTML = "";
        aggiornaTotale();
    };

    // INVIA ORDINE
    document.getElementById("btn-invia").onclick = ()=>{
        tavolo = selectTavolo.value;
        if(!tavolo){ alert("Seleziona un tavolo!"); return; }

        const piatti = [...list.children].map(el => ({
            id: el.dataset.id,
            qty: parseInt(el.querySelector(".qty").textContent)
        }));

        if (!piatti.length) {
            alert("Aggiungi almeno un piatto!");
            return;
        }

        fetch("../php/config/salva_ordine.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify({
                tavolo,
                note: document.getElementById("note-cucina").value,
                piatti
            })
        })
        .then(r=>r.json())
        .then(res=>{
            if(res.success){
                alert("Ordine inviato!");
                location.reload();
            } else {
                alert("Errore: " + res.message);
            }
        })
        .catch(err => console.error("Errore fetch:", err));
    };
});
</script>


<!-- =========================
     NOTIFICHE ORDINI PRONTI
========================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    let ordiniNotificati = [];

    setInterval(() => {
        fetch('../php/config/controlloOrdini.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.ordini.length > 0) {
                    data.ordini.forEach(ordine => {
                        if (!ordiniNotificati.includes(ordine.id_ordine)) {
                            alert(`Ordine pronto! Tavolo ${ordine.id_tavolo}`);
                            ordiniNotificati.push(ordine.id_ordine);
                        }
                    });
                }
            })
            .catch(err => console.error(err));
    }, 10000);
});
</script>

</body>
</html>