<?php 
  
  require_once "../php/class/Utente.php";
  require_once "../php/config/conf.php";
  session_start();
  
  if(isset($_SESSION['user_id'])){
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
    //echo "<script> alert('Utente loggato ".$_SESSION['email']."'); </script>";
  }

  if (isset($_POST['login'])) {
    $u = new Utente();
    $utente = $u->login($_POST['email'], $_POST['password']);

    if ($utente) {
        $_SESSION['user_id'] = $utente['id_utente'];
        $_SESSION['email'] = $utente['email'];
        $_SESSION['ruolo'] = $utente['ruolo'];

        if ($utente['ruolo'] === "cameriere") header("Location: ./cameriere.php");
        elseif ($utente['ruolo'] === "cuoco") header("Location: ./cuoco.php");
        elseif ($utente['ruolo'] === "admin") header("Location: ./admin.php");
        else header("Location: ../index.php");

        exit;
    } else {
        echo "<p style='color:red'> Email o password errati</p>";
    }
  }
?>


<?php include "../php/includes/header.php"; header3()?>
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
