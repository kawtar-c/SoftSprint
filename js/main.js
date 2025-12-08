/*
 * main.js - script generale del sito
 */

document.addEventListener("DOMContentLoaded", function () {

    console.log("main.js caricato su:", window.location.pathname);

    /* ==========================
     * 1) BOTTONE TORNA SU
     * ========================== */
    const backToTopBtn = document.getElementById("backToTop");

    // Rendo scrollToTop disponibile in HTML (onclick="scrollToTop()")
    window.scrollToTop = function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    if (backToTopBtn) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add("show");
            } else {
                backToTopBtn.classList.remove("show");
            }
        });
    }

    /* ==========================
     * 2) HERO BLUR (solo se c'è)
     * ========================== */
    const sectionBlur = document.querySelector(".hero-blur");
    const contentBlur = document.querySelector(".hero-blur-content");

    if (sectionBlur && contentBlur) {
        window.addEventListener("scroll", () => {
            const rect = sectionBlur.getBoundingClientRect();
            const scrolledInSection = Math.min(Math.max(-rect.top, 0), 200);
            const shift = scrolledInSection * 0.25; // massimo ~50px
            contentBlur.style.transform = `translateY(${shift}px)`;
        });
    }

    /* ==========================
     * 3) FILTRI ORDINI LATO CUOCO
     * ========================== */
    const filtro = document.getElementById("filtro-stato");
    const cards = document.querySelectorAll(".order-card");

    if (filtro && cards.length > 0) {

        function applicaFiltro() {
            const valore = filtro.value;
            cards.forEach((card) => {
                const stato = card.dataset.stato;
                card.style.display = (valore === "tutti" || stato === valore) ? "" : "none";
            });
        }

        function aggiornaStato(card, nuovoStato) {
            card.dataset.stato = nuovoStato;

            const badge = card.querySelector(".order-status");
            badge.classList.remove("status-nuovo", "status-preparazione", "status-pronto");

            if (nuovoStato === "preparazione") {
                badge.textContent = "In preparazione";
                badge.classList.add("status-preparazione");

            } else if (nuovoStato === "pronto") {
                badge.textContent = "Pronto";
                badge.classList.add("status-pronto");

            } else {
                badge.textContent = "Nuovo";
                badge.classList.add("status-nuovo");
            }

            applicaFiltro();
        }

        filtro.addEventListener("change", applicaFiltro);

        cards.forEach((card) => {
            card.addEventListener("click", (e) => {
                if (e.target.classList.contains("state-btn")) {
                    const stato = e.target.dataset.state;
                    aggiornaStato(card, stato);
                }
            });
        });

        applicaFiltro();
    }

    /* ==========================
     * 4) CALENDARIO PAGINA PRENOTAZIONE + FASCE ORARIE
     * ========================== */

    const calendarContainer = document.getElementById("calendar-container");
    const inputData = document.getElementById("data");
    const slotsNote = document.getElementById("slots-note");
    const slotsContainer = document.getElementById("slots-container");
    const inputOrario = document.getElementById("orario");

    if (!calendarContainer || !inputData) return;

    console.log("Inizializzo calendario prenotazione...");

    const fasceOrarie = ["19:00", "19:30", "20:00", "20:30", "21:00"];

    const oggi = new Date();
    const anno = oggi.getFullYear();
    const mese = oggi.getMonth(); // 0-11

    disegnaCalendario(anno, mese);

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

        html += `</div>`; // chiude calendar-grid

        calendarContainer.innerHTML = html;

        calendarContainer.querySelectorAll(".cal-day-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const dateISO = this.getAttribute("data-date");
                selezionaGiorno(dateISO, this);
            });
        });
    }

    function selezionaGiorno(dateISO, button) {

        inputData.value = dateISO;

        calendarContainer
            .querySelectorAll(".cal-day-btn")
            .forEach((b) => b.classList.remove("cal-selected"));

        button.classList.add("cal-selected");

        disegnaFasceOrarie(dateISO);
    }

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

}); // FINE DOMContentLoaded


/* ==========================
 * INVIO PRENOTAZIONE CON FETCH
 * ========================== */

const bookingForm = document.getElementById("booking-form");
const bookingMsg = document.getElementById("booking-message");

if (bookingForm) {
    
    bookingForm.addEventListener("submit", function (e) {

        e.preventDefault();

        const nome = document.getElementById("nome").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const data = document.getElementById("data").value;
        const ora = document.getElementById("orario").value;
        const persone = document.getElementById("persone").value;
        const note = document.getElementById("note").value.trim();

        if (!ora) {
            bookingMsg.textContent = "Seleziona un orario dal calendario prima di inviare.";
            bookingMsg.style.color = "red";
            return;
        }

        const payload = { nome, telefono, data, ora, persone, note };

        fetch("../php/class/crea_prenotazione.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {

            if (data.success) {

                bookingMsg.textContent = data.message;
                bookingMsg.style.color = "green";

                bookingForm.reset();
                document.getElementById("orario").value = "";

                const selectedSlot = document.querySelector(".slot-button.selected");
                if (selectedSlot) selectedSlot.classList.remove("selected");

            } else {

                bookingMsg.textContent = data.message || "Errore durante l'invio della prenotazione.";
                bookingMsg.style.color = "red";

            }

        })
        .catch(err => {
            console.error(err);
            bookingMsg.textContent = "Errore di comunicazione col server.";
            bookingMsg.style.color = "red";
        });

    });
}
