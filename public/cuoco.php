<?php
require_once "../php/includes/session.php";
require_once "../php/class/Ordine.php";

if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'cuoco') {
    header("Location: login.php");
    exit;
}

// Prendi tutti gli ordini non completati
$ordineObj = new Ordine();
$ordini = $ordineObj->getOrdiniAttivi();
?>

<?php include "../php/includes/header.php"; ?>

<header class="kitchen-topbar">
    <div class="container kitchen-topbar-inner">
        <div class="kitchen-brand">
            <span class="kitchen-icon">👨‍🍳</span>
            <span class="kitchen-title">Cucina – Ordini in arrivo</span>
        </div>

        <div class="kitchen-filters">
            <label for="filtro-stato">Stato:</label>
            <select id="filtro-stato">
                <option value="tutti">Tutti</option>
                <option value="nuovo" selected>Nuovi</option>
                <option value="preparazione">In preparazione</option>
                <option value="pronto">Pronti</option>
            </select>
        </div>
    </div>
</header>

<main class="container kitchen-layout">
    <section class="orders-panel" id="orders-panel">
        <?php foreach ($ordini as $ord): ?>
            <?php
                $piatti = $ordineObj->getPiattiDaOrdine($ord['id_ordine']); 
            ?>
            <article class="order-card" data-stato="<?= htmlspecialchars($ord['stato']) ?>" data-id="<?= $ord['id_ordine'] ?>">
                <header class="order-card-header">
                    <div>
                        <h2>Tavolo <?= htmlspecialchars($ord['id_tavolo']) ?></h2>
                    </div>
                    <span class="order-status status-<?= htmlspecialchars($ord['stato']) ?>">
                        <?= ucfirst(htmlspecialchars($ord['stato'])) ?>
                    </span>
                </header>

                <ul class="order-card-items">
                    <?php foreach ($piatti as $p): ?>
                        <li>
                            <span class="item-qty"><?= $p['quantita'] ?>×</span> 
                            <span class="item-name"><?= htmlspecialchars($p['nome']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (!empty($ord['note'])): ?>
                <div class="order-card-notes">
                    <strong>Note:</strong> <?= htmlspecialchars($ord['note']) ?>
                </div>
                <?php endif; ?>

                <div class="order-card-footer">
                    <span class="order-time">Ricevuto alle <?= date("H:i", strtotime($ord['data_ora'])) ?></span>
                    <div class="order-actions">
                        <?php if ($ord['stato'] !== 'in preparazione'): ?>
                        <button class="btn-secondary state-btn" data-state="in preparazione">In preparazione</button>
                        <?php endif; ?>
                        <?php if ($ord['stato'] !== 'pronto'): ?>
                        <button class="btn-primary state-btn" data-state="pronto">Pronto</button>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filtro = document.getElementById('filtro-stato');
    const panel = document.getElementById('orders-panel');

    // Filtra ordini in base allo stato selezionato
    filtro.addEventListener('change', () => {
        const statoSelezionato = filtro.value;
        panel.querySelectorAll('.order-card').forEach(card => {
            if (statoSelezionato === 'tutti' || card.dataset.stato === statoSelezionato) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Cambia stato ordine
    panel.addEventListener('click', (e) => {
        if (!e.target.classList.contains('state-btn')) return;

        const card = e.target.closest('.order-card');
        const nuovoStato = e.target.dataset.state;
        const ordineId = card.dataset.id;

        fetch('../php/config/cambia_stato.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: ordineId, stato: nuovoStato})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                
                card.dataset.stato = nuovoStato;
                const statusEl = card.querySelector('.order-status');
                statusEl.textContent = nuovoStato.charAt(0).toUpperCase() + nuovoStato.slice(1);
                statusEl.className = 'order-status status-' + nuovoStato;
            } else {
                alert("Errore nell'aggiornamento dello stato: " + data.message);
            }
        })
        .catch(err => console.error(err));
    });
});
</script>

</body>
</html>
