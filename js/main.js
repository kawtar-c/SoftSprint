/**
 * Bottone Top
 */
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('scroll', () => {
  const btn = document.getElementById('backToTop');
  if (window.scrollY > 300) {
    btn.classList.add('show');
  } else {
    btn.classList.remove('show');
  }
});


/*
* Animazione immagine sfocata con testo
*/
window.addEventListener("scroll", () => {
  const section = document.querySelector(".hero-blur");
  const content = document.querySelector(".hero-blur-content");
  if (!section || !content) return;

  const rect = section.getBoundingClientRect();
  const scrolledInSection = Math.min(Math.max(-rect.top, 0), 200);
  const shift = scrolledInSection * 0.25; // massimo ~50px

  content.style.transform = `translateY(${shift}px)`;
});

 
/*
* Gestione filtri ordini lato Cuoco
*/
const filtro = document.getElementById('filtro-stato');
const cards = document.querySelectorAll('.order-card');

function applicaFiltro() {
    const valore = filtro.value;
      cards.forEach(card => {
        const stato = card.dataset.stato;
        card.style.display = (valore === 'tutti' || stato === valore) ? '' : 'none';
      });
    }

    function aggiornaStato(card, nuovoStato) {
      card.dataset.stato = nuovoStato;

      const badge = card.querySelector('.order-status');
      badge.classList.remove('status-nuovo', 'status-preparazione', 'status-pronto');

      if (nuovoStato === 'preparazione') {
        badge.textContent = 'In preparazione';
        badge.classList.add('status-preparazione');
      } else if (nuovoStato === 'pronto') {
        badge.textContent = 'Pronto';
        badge.classList.add('status-pronto');
      } else {
        badge.textContent = 'Nuovo';
        badge.classList.add('status-nuovo');
      }

      applicaFiltro();
    }

    filtro.addEventListener('change', applicaFiltro);

    cards.forEach(card => {
      card.addEventListener('click', (e) => {
        if (e.target.classList.contains('state-btn')) {
          const stato = e.target.dataset.state;
          aggiornaStato(card, stato);
        }
      });
    });

    applicaFiltro();


