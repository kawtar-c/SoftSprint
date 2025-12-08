<?php 
require_once "../php/includes/header.php";
$header = new Header();
?>

<?php echo $header->render('guest'); ?>
<?php
  require_once "../php/class/Piatto.php";

  $piatto= new Piatto();
  $menu = $piatto->getMenu(); 

  $piattiPerCategoria = [];
  foreach ($menu as $piatto) {
      $piattiPerCategoria[$piatto['categoria']][] = $piatto;
  }
 
?>
<main>
<!-- IMMAGINE SFOCATA CON TITOLO -->
<section class="hero-blur">
  <div class="container hero-blur-inner">
  <div class="hero-blur-content">
  <div class="section-eyebrow">Il nostro menu</div>
  <h1 class="section-title" style="color:white;">Sapori tradizionali abruzzesi</h1>
  <p class="section-subtitle" style="color:rgba(255,255,255,0.85);">
    Piatti preparati secondo ricette autentiche, tramandate da generazioni.
  </p>
</div>
  </div>
</section>

<?php foreach ($piattiPerCategoria as $categoria => $piatti) { ?>
  <!-- Una sola sezione per categoria -->
  <section id="<?php echo strtolower($categoria); ?>">

    <div class="container">
      <h2 class="section-title"><?php echo $categoria; ?></h2>

      <div class="cards-grid">

        <?php foreach ($piatti as $piatto) { ?>
          <article class="card">  
            <img src="<?php echo $piatto['img']; ?>" alt="<?php echo $piatto['nome']; ?>">

            <div class="card-body">
              <div class="card-title-row">
                <h3 class="card-title"><?php echo $piatto['nome']; ?></h3>
                <span class="card-price">€ <?php echo $piatto['prezzo']; ?></span>
              </div>  

              <p class="card-text">
                <?php echo $piatto['descrizione']; ?>
              </p>

            </div>
          </article>
        <?php } ?>

      </div>
    </div>

  </section>
<?php } ?>

</main>
<?php include "../php/includes/footer.php"; ?>
