/*Funzione globale per lo scorrimento all'inizio della pagina*/
window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: "smooth" }); // Scorri in cima con animazione fluida
};

/**
 * Reindirizza alla pagina del cameriere con il tavolo selezionato.
 * @param {HTMLSelectElement} select - Elemento select del tavolo.
 */
window.vaiAlTavolo = function (select) {
    const id = select.value; // Ottieni l'ID del tavolo selezionato
    window.location.href = id ? "?tavolo=" + id : "cameriere.php"; // Reindirizza con o senza parametro
};

/**
 * Funzione segnaposto per cambiare lo stato di un tavolo (richiede implementazione Fetch API).
 * @param {number} id - ID del tavolo.
 * @param {string} stato - Nuovo stato.
 */
window.cambiaStato = function (id, stato) {
    alert("Hai cliccato per cambiare lo stato del tavolo " + id + " a: " + stato); // Messaggio di debug/segnaposto
};


// ==========================
// LISTENER PRINCIPALE (DOMContentLoaded)
// ==========================
document.addEventListener("DOMContentLoaded", function () {

    console.log("main.js caricato su:", window.location.pathname);

    /*BOTTONE TORNA SU E HERO BLUR*/
    const backToTopBtn = document.getElementById("backToTop"); // Seleziona il bottone "Torna Su"

    if (backToTopBtn) { // Verifica se il bottone esiste nella pagina
        window.addEventListener("scroll", () => { // Aggiungi un listener sull'evento scroll
            if (window.scrollY > 300) { // Se lo scorrimento verticale è oltre i 300px
                backToTopBtn.classList.add("show"); // Mostra il bottone
            } else {
                backToTopBtn.classList.remove("show"); // Nascondi il bottone
            }
        });
    }

    const sectionBlur = document.querySelector(".hero-blur"); // Seleziona la sezione per l'effetto blur
    const contentBlur = document.querySelector(".hero-blur-content"); // Seleziona il contenuto da spostare

    if (sectionBlur && contentBlur) { // Se gli elementi Hero esistono
        window.addEventListener("scroll", () => { // Aggiungi un listener sull'evento scroll
            const rect = sectionBlur.getBoundingClientRect(); // Ottieni la posizione della sezione
            const scrolledInSection = Math.min(Math.max(-rect.top, 0), 200); // Calcola lo scorrimento nella sezione (max 200)
            const shift = scrolledInSection * 0.25; // Calcola lo spostamento (25% dello scorrimento)
            contentBlur.style.transform = `translateY(${shift}px)`; // Applica la traslazione per l'effetto parallasse
        });
    }


    /*GESTIONE ORDINI LATO CUOCO*/
    const filtroStatoOrdini = document.getElementById("filtro-stato"); // Elemento select per il filtro
    const ordersPanel = document.getElementById('orders-panel') || document.querySelector('.orders-list-container'); // Contenitore degli ordini
    const orderCards = document.querySelectorAll(".order-card"); // Tutte le schede ordine

    if (filtroStatoOrdini && orderCards.length > 0) {

        /*Applica il filtro sulle schede ordine*/
        function applicaFiltroOrdini() {
            const valore = filtroStatoOrdini.value;
            orderCards.forEach((card) => {
                const stato = card.dataset.stato;
                card.style.display = (valore === "tutti" || stato === valore) ? "" : "none";
            });
        }

        /** Aggiorna lo stato di una singola card visivamente */
        function aggiornaStatoVisivamente(card, nuovoStato) {
            card.dataset.stato = nuovoStato;
            const badge = card.querySelector(".order-status");

            if (badge) {
                badge.classList.remove("status-nuovo", "status-preparazione", "status-pronto");

                if (nuovoStato === "preparazione") {
                    badge.textContent = "In preparazione";
                    badge.classList.add("status-preparazione");
                } else if (nuovoStato === "pronto") {
                    badge.textContent = "Pronto";
                    badge.classList.add("status-pronto"); 
                } else {
                    badge.textContent = "Inviato";
                    badge.classList.add("status-nuovo");
                }
            }
            applicaFiltroOrdini();
        }

        filtroStatoOrdini.addEventListener("change", applicaFiltroOrdini);

        if (ordersPanel) {
            ordersPanel.addEventListener('click', (e) => {
                if (!e.target.classList.contains('state-btn')) return;

                const card = e.target.closest('.order-card');
                const nuovoStato = e.target.dataset.state;
                const ordineId = card.dataset.id;

                //Chiamata file php per cambiare lo stato
                fetch('../php/config/cambia_stato.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: ordineId, stato: nuovoStato })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            aggiornaStatoVisivamente(card, nuovoStato);
                        } else {
                            alert("Errore nell'aggiornamento dello stato: " + (data.message || "Errore sconosciuto."));
                        }
                    })
                    .catch(err => console.error("Errore fetch cambio stato:", err));
            });
        }
        applicaFiltroOrdini();
    }


    /*GESTIONE CALENDARIO E PRENOTAZIONI*/
    const calendarContainer = document.getElementById("calendar-container"); // Contenitore del calendario
    const inputData = document.getElementById("data"); // Input nascosto per la data
    const slotsNote = document.getElementById("slots-note"); // Elemento per il messaggio di selezione
    const slotsContainer = document.getElementById("slots-container"); // Contenitore per i bottoni orari
    const inputOrario = document.getElementById("orario"); // Input nascosto per l'orario

    if (calendarContainer && inputData) {

        console.log("Inizializzo calendario prenotazione...");

        const fasceOrarie = ["12:30", "13:30", "19:00", "19:30", "20:00", "20:30", "21:00"]; // Orari di prenotazione disponibili

        const oggi = new Date(); // Data corrente
        const annoIniziale = oggi.getFullYear();
        const meseIniziale = oggi.getMonth();

        disegnaCalendario(annoIniziale, meseIniziale);

        /** Disegna l'HTML del calendario per un dato mese/anno. */
        function disegnaCalendario(anno, mese) {

            const firstDay = new Date(anno, mese, 1);
            const lastDay = new Date(anno, mese + 1, 0).getDate();
            const nomeMese = firstDay.toLocaleString("it-IT", { month: "long" });

            let html = `
                <div class="calendar-header">
                    <h3>${nomeMese.charAt(0).toUpperCase() + nomeMese.slice(1)} ${anno}</h3>
                </div>

                <div class="calendar-grid">
                    <div class="cal-day-name">Lun</div>
                    <div class="cal-day-name">Mar</div>
                    <div class="cal-day-name">Mer</div>
                    <div class="cal-day-name">Gio</div>
                    <div class="cal-day-name">Ven</div>
                    <div class="cal-day-name">Sab</div>
                    <div class="cal-day-name">Dom</div>
            `;

            let startWeekDay = firstDay.getDay();
            if (startWeekDay === 0) startWeekDay = 7;

            for (let i = 1; i < startWeekDay; i++) { 
                html += `<div class="cal-cell cal-empty"></div>`;
            }

            const oggiStr = new Date().toISOString().split("T")[0];

            for (let day = 1; day <= lastDay; day++) {

                const dateStr = `${anno}-${String(mese + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
                const isToday = dateStr === oggiStr;

                html += `
                    <div class="cal-cell">
                        <button
                            class="cal-day-btn${isToday ? " cal-today" : ""}"
                            data-date="${dateStr}"
                            type="button">
                            ${day}
                        </button>
                    </div>
                `;
            }

            html += `</div>`;
            calendarContainer.innerHTML = html;

            calendarContainer.querySelectorAll(".cal-day-btn").forEach((btn) => {
                btn.addEventListener("click", function () {
                    const dateISO = this.getAttribute("data-date");
                    selezionaGiorno(dateISO, this);
                });
            });
        }

        /*Gestione date*/
        function selezionaGiorno(dateISO, button) {

            inputData.value = dateISO;

            calendarContainer
                .querySelectorAll(".cal-day-btn")
                .forEach((b) => b.classList.remove("cal-selected"));

            button.classList.add("cal-selected");

            disegnaFasceOrarie(dateISO);
        }

        /*Gestione orari*/
        function disegnaFasceOrarie(dateISO) {

            if (!slotsContainer) return;

            slotsContainer.innerHTML = "";

            fasceOrarie.forEach((ora) => {

                const btn = document.createElement("button");
                btn.type = "button";
                btn.className = "slot-button";
                btn.textContent = ora;
                btn.dataset.ora = ora;
                btn.dataset.data = dateISO;

                btn.addEventListener("click", function () {
                    selezionaOrario(this.dataset.data, this.dataset.ora, this);
                });

                slotsContainer.appendChild(btn);
            });

            if (slotsNote) {
                slotsNote.textContent = `Scegli l'orario per il ${dateISO}.`;
            }
        }

        /*Selezione di un orario specifico. */
        function selezionaOrario(dataISO, ora, button) {

            if (inputOrario) {
                inputOrario.value = ora;
            }

            slotsContainer
                .querySelectorAll(".slot-button")
                .forEach((b) => b.classList.remove("selected"));

            button.classList.add("selected");

            if (slotsNote) {
                slotsNote.textContent = `Hai scelto il ${dataISO} alle ${ora}.`;
            }
        }
    }

    /*GESTIONE ORDINE LATO CAMERIERE*/
    const orderList = document.getElementById("order-items"); // Lista degli elementi ordinati
    const orderTotal = document.getElementById("order-total"); // Elemento per il totale
    const selectTavolo = document.getElementById("select-tavolo"); // Select per la scelta del tavolo
    const btnSvuota = document.getElementById("btn-svuota"); // Bottone per svuotare l'ordine
    const btnInvia = document.getElementById("btn-invia"); // Bottone per inviare l'ordine
    const dishAddBtns = document.querySelectorAll(".dish-add"); // Bottoni per aggiungere piatti

    if (selectTavolo && orderList && orderTotal) {

        let tavoloSelezionato = selectTavolo.value;

        // Aggiorna il titolo dell'ordine con il tavolo selezionato
        document.getElementById("ordine-titolo").textContent =
            "Ordine Tavolo " + (tavoloSelezionato || "");

        /*Calcola e aggiorna il totale dell'ordine*/
        function aggiornaTotale() {
            let t = 0; // Inizializza totale a zero
            orderList.querySelectorAll(".order-item").forEach(el => { 
                t += parseFloat(el.dataset.price) *
                    parseInt(el.querySelector(".qty").textContent);
            });
            orderTotal.textContent = t.toFixed(2).replace(".", ",") + " €";
        }
        aggiornaTotale(); 

        /*Aggiunta piatti*/
        dishAddBtns.forEach(btn => { 
            btn.onclick = () => {
                const d = btn.closest(".dish");
                const id = d.dataset.id; 
                const price = parseFloat(d.dataset.price);
                const title = d.dataset.title;

                let exist = [...orderList.children].find(el => el.dataset.id === id); // Controlla se il piatto è già in lista

                if (exist) { // Se il piatto è già presente incrementa la quantità
                    exist.querySelector(".qty").textContent = 
                        parseInt(exist.querySelector(".qty").textContent) + 1;
                } else { 
                    // Se il piatto è nuovo crea il nuovo elemento nella lista
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
                    orderList.appendChild(li);
                }
                aggiornaTotale();
            };
        });

        /*Incremetnto Decremento piatti Ordine*/
        orderList.onclick = e => { 
            if (!e.target.classList.contains("qty-plus") && // Controlla se il click è su + o -
                !e.target.classList.contains("qty-minus")) return;

            const li = e.target.closest(".order-item");
            let qty = parseInt(li.querySelector(".qty").textContent);

            qty += e.target.classList.contains("qty-plus") ? 1 : -1;

            if (qty <= 0) li.remove();
            else li.querySelector(".qty").textContent = qty;

            aggiornaTotale();
        };

        /*Svuota ordine*/
        if (btnSvuota) {
            btnSvuota.onclick = () => {
                orderList.innerHTML = "";
                aggiornaTotale(); 
            };
        }

        /*INVIA ORDINE*/
        if (btnInvia) {
            btnInvia.onclick = () => {
                tavoloSelezionato = selectTavolo.value;
                if (!tavoloSelezionato) { alert("Seleziona un tavolo!"); return; }

                // Mappa gli elementi della lista in un array di oggetti {id, qty}
                const piatti = [...orderList.children].map(el => ({
                    id: el.dataset.id,
                    qty: parseInt(el.querySelector(".qty").textContent)
                }));

                if (!piatti.length) {
                    alert("Aggiungi almeno un piatto!");
                    return;
                }

                // Invia l'ordine al file php
                fetch("../php/config/salva_ordine.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        tavolo: tavoloSelezionato,
                        note: document.getElementById("note-cucina").value,
                        piatti
                    })
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            alert("Ordine inviato!");
                            location.reload();
                        } else {
                            alert("Errore: " + res.message); 
                        }
                    })
                    .catch(err => console.error("Errore fetch:", err));
            };
        }
    }


    /*Notifiche PER CAMERIERE*/
    let ordiniNotificati = []; // Array per tenere traccia degli ordini già notificati

    setInterval(() => { 
        fetch('../php/config/controlloOrdini.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.ordini && data.ordini.length > 0) { 
                    data.ordini.forEach(ordine => {
                        if (!ordiniNotificati.includes(ordine.id_ordine)) { 
                            alert(`Ordine pronto! Tavolo ${ordine.id_tavolo}`); 
                            ordiniNotificati.push(ordine.id_ordine);
                        }
                    });
                }
            })
            .catch(err => console.error("Errore polling ordini:", err));
    }, 10000000000000); // Intervallo di 10 secondi



    // Gestione delle schermate
    const tabButtons = document.querySelectorAll(".tab-btn, #addDishBtn, #addUsrBtn");
    const tabContents = document.querySelectorAll(".tab-content, #addDish, #addUsr");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const tab = btn.dataset.tab;
            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            tabContents.forEach(c => c.classList.remove("active"));
            document.getElementById(tab).classList.add("active");
        });
    });


});
