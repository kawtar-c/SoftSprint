<?php 
session_start();

require_once "../php/includes/header.php";   // usa sempre lo stesso nome (minuscolo)
require_once "../php/class/Utente.php";
require_once "../php/config/conf.php";

$header = new Header();

// Se utente già loggato, reindirizza subito
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['ruolo'] === "cameriere") {
        header("Location: ./cameriere.php");
        exit;
    } elseif ($_SESSION['ruolo'] === "cuoco") {
        header("Location: ./cuoco.php");
        exit;
    } elseif ($_SESSION['ruolo'] === "admin") {
        header("Location: ./admin.php");
        exit;
    } else {
        header("Location: ../index.php");
        exit;
    }
}

// Gestione login
if (isset($_POST['login'])) {
    $u = new Utente();
    $utente = $u->login($_POST['email'], $_POST['password']);

    if ($utente) {
        $_SESSION['user_id'] = $utente['id_utente'];
        $_SESSION['email'] = $utente['email'];
        $_SESSION['ruolo'] = $utente['ruolo'];

        if ($utente['ruolo'] === "cameriere") {
            header("Location: ./cameriere.php");
        } elseif ($utente['ruolo'] === "cuoco") {
            header("Location: ./cuoco.php");
        } elseif ($utente['ruolo'] === "admin") {
            header("Location: ./admin.php");
        } else {
            header("Location: ../index.php");
        }

        exit;
    } else {
        $errore_login = "Email o password errati";
    }
}
?>

<?php echo $header->render('simple'); ?>

<!-- LOGIN CARD -->
<main>
  <div class="login-container">
    <h2>Accedi</h2>
    <p class="login-subtitle">Accedi al tuo spazio personale</p>

    <?php if (!empty($errore_login)) : ?>
      <p style="color:red"><?php echo $errore_login; ?></p>
    <?php endif; ?>

    <form action="./login.php" method="POST" class="login-form">
      <input type="text" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <button type="submit" name="login" class="login-submit">Accedi</button>
    </form>

  </div>
</main>

</body>
</html>