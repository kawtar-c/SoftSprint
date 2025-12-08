<!-- HEADER -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <title>Ristorante di Softsprint</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

  <!-- CSS  -->
 <link rel="stylesheet" href="css/style.css">

  <!-- JS -->
  <script src="js/main.js"></script>
</head>

<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="container navbar-inner">
      <div class="logo">Ristorante Softsprint</div>
      <nav class="nav-links">
        <a href="#chi-siamo">Chi siamo</a>
        <a href="#menu">Specialità</a>
        <a href="#punti-forza">Perché noi</a>
        <a href="#contatti">Contatti</a>
      </nav>
      <a href="public/login.php" class="nav-cta">Login</a>
    </div>
  </header>
  <main>
    <!-- HERO -->
    <section class="hero">
      <div class="container hero-inner">
        <div>
          <div class="hero-tag">
            <span>Dal 1985</span> • Cucina di famiglia
          </div>
          <h1 class="hero-title">Sapori autentici,</p> come a casa.</h1>
          <p class="hero-subtitle">
            Un ristorante a conduzione familiare dove ogni piatto racconta una storia,
            preparato con ingredienti freschi e ricette tramandate da generazioni.
          </p>
         <div class="hero-buttons">
    <a href="./public/menu.php" class="btn-primary">Guarda il menu</a>
    <a href="./public/prenotazione.php" class="btn-primary">Prenota ora</a>
</div>
          <br>
          <p class="hero-info">
            <span>Pranzo e cena</span> • Aperto dal martedì alla domenica
          </p>
        </div>

        <div class="hero-image-wrapper">
          <img class="hero-main-image" src="https://i.pinimg.com/1200x/4a/30/cc/4a30ccde920c9453275fa7b8d64af60b.jpg" alt="Sala del ristorante" />
      
        </div>
      </div>
    </section>

    <!-- CHI SIAMO -->
    <section id="chi-siamo">
      <div class="container">
        <div class="section-header">
          <div class="section-eyebrow">La nostra storia</div>
          <h2 class="section-title">Una famiglia, una cucina.</h2>
          <p class="section-subtitle">
            Da una piccola trattoria di quartiere a punto di riferimento per chi cerca
            piatti genuini e un’accoglienza sincera.
          </p>
        </div>

        <div class="about">
          <div class="about-text">
            <p>
              Il <strong>Ristorante di Famiglia</strong> nasce dall’idea di condividere con gli
              altri le stesse ricette che da anni riuniscono la nostra famiglia attorno alla tavola.
            </p>
            <p>
              Ogni piatto è preparato con ingredienti freschi e selezionati, privilegiando
              produttori locali e stagionalità.
            </p>
            <div class="about-highlight">
              “Per noi non sei solo un cliente, ma un ospite. Vogliamo che ti senta
              come a casa, fin dal primo assaggio.”
            </div>
          </div>

          <div class="about-image">
            <img src="https://i.pinimg.com/1200x/6b/e1/fa/6be1fa3e73cf1172608f3e12cb92e84b.jpg" alt="Famiglia in cucina" />
          </div>
        </div>
      </div>
    </section>

    <!-- MENU -->
    <section id="menu">
      <div class="container">
        <div class="section-header">
          <div class="section-eyebrow">Il nostro menu</div>
          <h2 class="section-title">Piatti della casa</h2>
          <p class="section-subtitle">
            Una selezione dei nostri piatti più amati.
          </p>
        </div>

        <div class="cards-grid">

     <article class="card">
  <img src="https://i.pinimg.com/736x/05/42/38/054238558776a09b37fa67edddf633f3.jpg" alt="Chitarra alla teramana" />
  <div class="card-body">
    <div class="card-title-row">
      <h3 class="card-title">Chitarra alla teramana</h3>
      <span class="card-price">€ 10</span>
    </div>
    <p class="card-text">
      Pasta all'uovo tirata alla chitarra con ragù di carne mista e
      pallottine: manzo e maiale macinati, pomodoro, sedano, carota,
      cipolla, vino bianco e pecorino abruzzese.
    </p>
    <span class="card-tag">Primo piatto</span>
  </div>
</article>

         <article class="card">
  <img src="https://i.pinimg.com/736x/4c/a4/e0/4ca4e091c989a72f94814da385b10815.jpg" alt="Mazzarelle teramane" />
  <div class="card-body">
    <div class="card-title-row">
      <h3 class="card-title">Mazzarelle </h3>
      <span class="card-price">€ 5 (3pz)</span>
    </div>
    <p class="card-text">
      Involtini tradizionali di interiora d’agnello
      avvolti in foglie di indivia o cicoria e legati con budello naturale.
      Lenti cottura in tegame con olio, vino bianco, aglio e aromi.
    </p>
    <span class="card-tag">Secondo tipico</span>
  </div>
</article>

          <article class="card">
  <img src="https://i.pinimg.com/1200x/2c/cf/cb/2ccfcb40536564326c424e2fd1b62401.jpg" alt="Bocconotti" />
  <div class="card-body">
    <div class="card-title-row">
      <h3 class="card-title">Bocconotti </h3>
      <span class="card-price">€ 5</span>
    </div>
    <p class="card-text">
      Frolla friabile che racchiude un ripieno ricco e profumato a base di
      cacao, mandorle tritate, mosto cotto e un tocco di cannella. Uno dei
      dolci più autentici della tradizione teramana.
    </p>
    <span class="card-tag">Dessert tipico</span>
  </div>
</article>

        </div>

      
      </div>
    </section>

    <!-- PUNTI DI FORZA -->
    <section id="punti-forza">
      <div class="container">
        <div class="section-header">
          <div class="section-eyebrow">Perché sceglierci</div>
          <h2 class="section-title">I nostri valori in tavola</h2>
        </div>

        <div class="features-grid">
          <div class="feature-item">
            <h3 class="feature-title">Cucina di tradizione</h3>
            <p class="feature-text">Ricette tramandate da generazioni.</p>
          </div>
          <div class="feature-item">
            <h3 class="feature-title">Ingredienti locali</h3>
            <p class="feature-text">Prodotti freschi e del territorio.</p>
          </div>
          <div class="feature-item">
            <h3 class="feature-title">Atmosfera accogliente</h3>
            <p class="feature-text">Perfetto per famiglie e ricorrenze.</p>
          </div>
          <div class="feature-item">
            <h3 class="feature-title">Gestione familiare</h3>
            <p class="feature-text">Sempre presenti, sempre disponibili.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTATTI & MAPPA -->
    <section id="contatti">
      <div class="container">
        <div class="section-header">
          <div class="section-eyebrow">Vieni a trovarci</div>
          <h2 class="section-title">Contatti & Orari</h2>
        </div>

        <div class="contact-map">
          <div class="contact-card">
            <h3>Prenotazioni</h3>
            <p><strong>Telefono:</strong> +39 0123 456789</p>
            <p><strong>Email:</strong> info@ristoranteSoftsprint.it</p>
            <p><strong>Indirizzo:</strong> Via Roma 123, 00100 Città</p>

            <p><strong>Orari:</strong></p>
            <ul class="opening-hours">
              <li>Mar–Ven: 12:00–15:00 • 19:30–23:00</li>
              <li>Sab–Dom: 12:00–15:30 • 19:30–23:30</li>
              <li>Lunedì: Chiuso</li>
            </ul>

            

          </div>

          <div class="map-wrapper">
          <iframe
  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2965.4214441920784!2d13.699248375922358!3d42.65995571574873!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1331a7b609a1c39d%3A0x28b1f1f471f00159!2sTeramo%2C%20TE!5e0!3m2!1sit!2sit!4v1700000000001!5m2!1sit!2sit"
  width="600"
  height="450"
  style="border:0;"
  allowfullscreen=""
  loading="lazy"
  referrerpolicy="no-referrer-when-downgrade">
</iframe>
          </div>
        </div>
      </div>
    </section>

  </main>

<?php include "./php/includes/footer.php"; ?>
