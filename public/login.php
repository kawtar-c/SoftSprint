<?php 
  session_start();
  require_once "../php/class/Utente.php";
  require_once "../php/config/conf.php";

  if (isset($_POST['login'])) {
    $u = new Utente();
    $utente = $u->login($_POST['email'], $_POST['password']);

    if ($utente) {
        $_SESSION['user_id'] = $utente['id_utente'];
        $_SESSION['email'] = $utente['email'];
        $_SESSION['ruolo'] = $utente['ruolo'];

        if ($utente['ruolo'] === "cameriere") header("Location: ./cameriere.php");
        elseif ($utente['ruolo'] === "cuoco") header("Location: ./cuoco.php");
        else header("Location: ../index.php");

        exit;
    } else {
        echo "<p style='color:red'> Email o password errati</p>";
    }
  }
?>


<?php include "../php/includes/header.php"; ?>
  <!-- LOGIN CARD -->
  <main>
    <div class="login-container">
      <h2>Accedi</h2>
      <p class="login-subtitle">Accedi al tuo spazio personale</p>

      <form action="./login.php" method="POST" class="login-form">
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login" class="login-submit">Accedi</button>
      </form>

    </div>
  </main>

</body>
</html>