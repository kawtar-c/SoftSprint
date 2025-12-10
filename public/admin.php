<?php
require_once "../php/includes/header.php"; 
require_once "../php/class/Piatto.php";
require_once "../php/class/Utente.php";
require_once "../php/includes/session.php";

if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$utente = new Utente();
$utenti = $utente->getUtenti();

$piatto = new Piatto();
$menu = $piatto->getMenu(); 

$piattiPerCategoria = [];
foreach ($menu as $p) {
    $piattiPerCategoria[$p['categoria']][] = $p;
}

$piattoDaModificare = null;
$utenteDaModificare = null;

//Switch tra le varie azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Gestione piatti
    if (isset($_POST['modifica'])) {
        $id = intval($_POST['modifica']);
        $piattoDaModificare = $piatto->getPiatto($id);
    } elseif (isset($_POST['elimina'])) {
        $id = intval($_POST['elimina']);
        $piatto->eliminaPiatto($id);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif(isset($_POST['nomePiatto'], $_POST['descrizione'], $_POST['prezzo'], $_POST['categoria'], $_POST['img'])) {
        $nome = $_POST['nomePiatto'];
        $descrizione = $_POST['descrizione'];
        $prezzo = floatval($_POST['prezzo']);
        $categoria = $_POST['categoria'];
        $img = $_POST['img'];

        //Controllo se è modifica o aggiunta
        if (!empty($_POST['id_piatto'])) {
            $id_piatto = intval($_POST['id_piatto']);
            $piatto->modificaPiatto($id_piatto, $nome, $descrizione, $prezzo, $categoria, $img);
        } else {
            $piatto->aggiungiPiatto($nome, $descrizione, $prezzo, $categoria, $img);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    //Gestione utenti
    if (isset($_POST['modificaUsr'])) {
        $id = intval($_POST['modificaUsr']);
        $utenteDaModificare = $utente->getUtente($id);
    } elseif (isset($_POST['eliminaUsr'])) {
        $id = intval($_POST['eliminaUsr']);
        $utente->eliminaUtente($id); 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['ruolo'])) {
        // Aggiungi o modifica utente
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $email = $_POST['email'];
        $ruolo = $_POST['ruolo'];
        $password = $_POST['password'] ?? null;

        if (!empty($_POST['id_utente'])) {
            $id_utente = intval($_POST['id_utente']);
            $utente->modificaUtente($id_utente, $email, $password, $ruolo, $nome, $cognome);
        } else {
            $utente->aggiungiUtente($email, $password, $ruolo, $nome, $cognome);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
$header = new Header();
echo $header->render('user');
?>

<!-- TAB BAR CENTRALE -->
<div class="tab-container">
  
    <button class="tab-btn <?php echo (!$piattoDaModificare && !$utenteDaModificare) ? 'active' : ''; ?>" data-tab="menu">Gestione Menu</button>
    <button class="tab-btn" data-tab="Statistiche">Statistiche</button>
    <button class="tab-btn" data-tab="Pagamenti">Pagamenti</button>
    <button class="tab-btn" data-tab="account">Account</button>
</div>

<main class="container">

    <!-- Gestione Menu -->
     <section id="menu" class="tab-content <?php echo (!$piattoDaModificare && !$utenteDaModificare) ? 'active' : ''; ?>">
        <div class="section-header">
            <h2 class="section-title">Gestione Menu</h2>
            <p class="section-subtitle">Aggiungi, modifica o rimuovi piatti dal menù.</p>
        </div>

        <?php foreach ($piattiPerCategoria as $categoria => $piatti) { ?>
        <section id="<?php echo strtolower($categoria); ?>">
            <div class="container">
                <h2 class="section-title"><?php echo $categoria; ?></h2>
                <div class="cards-grid">
                    <?php foreach ($piatti as $p) { ?>
                    <article class="card">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['nome']; ?>">
                        <div class="card-body">
                            <div class="card-title-row">
                                <h3 class="card-title"><?php echo $p['nome']; ?></h3>
                                <span class="card-price">€ <?php echo $p['prezzo']; ?></span>
                            </div>
                            <p class="card-text"><?php echo $p['descrizione']; ?></p>
                            <div class="hero-buttons">
                                <form method="post" action="">
                                    <button type="submit" class="btn-secondary" name="modifica" value="<?php echo $p['id_piatto']; ?>">Modifica</button>
                                    <button type="submit" class="btn-primary" name="elimina" value="<?php echo $p['id_piatto']; ?>">Elimina</button>
                                </form>
                            </div>
                        </div>
                    </article>
                    <?php } ?>
                </div>
            </div>
        </section>
        <?php } ?>
        <div class="section-cta-center"> 
          <button id="addDishBtn" class="btn-primary" data-tab="addDish">+ Aggiungi nuovo piatto</button> 
        </div>
    </section>

    <!-- Aggiungi / Modifica Piatto -->
    <section id="addDish" class="tab-content <?php echo $piattoDaModificare ? 'active' : ''; ?>">
        <div class="login-container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $piattoDaModificare ? 'Modifica Piatto' : 'Aggiungi Nuovo Piatto'; ?></h2>
                <p class="section-subtitle">Compila il modulo per aggiungere o modificare un piatto.</p>
            </div>

            <form method="post" action="admin.php" class="admin-form">
                <input type="hidden" name="id_piatto" value="<?php echo $piattoDaModificare['id_piatto'] ?? ''; ?>">

                <input type="text" id="nome" name="nomePiatto" placeholder="nome del piatto" required
                       value="<?php echo htmlspecialchars($piattoDaModificare['nome'] ?? ''); ?>">

                <textarea id="descrizione" name="descrizione" placeholder="descrizione" required><?php
                       echo htmlspecialchars($piattoDaModificare['descrizione'] ?? ''); ?></textarea>

                <input type="number" id="prezzo" name="prezzo" placeholder="prezzo" required
                       value="<?php echo htmlspecialchars($piattoDaModificare['prezzo'] ?? ''); ?>">

                <select id="categoria" name="categoria" required>
                    <option value="Primi" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Primi') echo 'selected'; ?>>Primi</option>
                    <option value="Secondi" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Secondi') echo 'selected'; ?>>Secondi</option>
                    <option value="Dolci" <?php if (($piattoDaModificare['categoria'] ?? '') === 'Dolci') echo 'selected'; ?>>Dolci</option>
                </select>

                <input type="text" id="img" name="img" accept="image/*" placeholder="indirizzo immagine" required
                       value="<?php echo htmlspecialchars($piattoDaModificare['img'] ?? ''); ?>">

                <div class="form-cta-center">
                    <button type="submit" class="btn-primary">
                        <?php echo $piattoDaModificare ? 'Modifica Piatto' : 'Aggiungi Piatto'; ?>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Statistiche -->
    <section id="Statistiche" class="tab-content">
        <div class="section-header">
            <h2 class="section-title">Statistiche e Report</h2>
            <p class="section-subtitle">Andamento degli ordini, piatti più venduti e report giornalieri.</p>
        </div>
    </section>

    <!-- Pagamenti e Contabilità -->
    <section id="Pagamenti" class="tab-content">
        <div class="section-header">
            <h2 class="section-title">Pagamenti e Contabilità</h2>
            <p class="section-subtitle">Gestisci incassi, pagamenti e metodi di fatturazione.</p>
        </div>
    </section>

    <!-- Account Utenti -->
    <section id="account" class="tab-content">
        <div class="section-header">
            <h2 class="section-title">Lista Utenti</h2>
            <p class="section-subtitle">Crea/Modifica/Elimina credenziali di accesso.</p>
        </div>
        <?php foreach ($utenti as $u) { ?>
        <div class="card" style="max-width: 500px; margin: auto; margin-bottom: 20px;">
            <div class="card-body">
                <div class="card-title-row">
                    <h3 class="card-title"><?php echo htmlspecialchars($u['nome'] . ' ' . $u['cognome']); ?></h3>
                </div>
                <p class="card-text">Email: <?php echo htmlspecialchars($u['email']); ?></p>
                <p class="card-text">Ruolo: <?php echo htmlspecialchars($u['ruolo']); ?></p>
                <div class="hero-buttons">
                    <form method="post" action="">
                      <button type="submit" class="btn-secondary" name="modificaUsr" value="<?php echo $u['id_utente']; ?>">Modifica Dati</button>
                      <button type="submit" class="btn-primary" name="eliminaUsr" value="<?php echo $u['id_utente']; ?>">Elimina</button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="section-cta-center"> 
          <button id="addUsrBtn" class="btn-primary" data-tab="addUsr">+ Aggiungi nuovo utente</button> 
        </div>
    </section>

    <!-- Aggiungi / Modifica Utente -->
    <section id="addUsr" class="tab-content <?php echo $utenteDaModificare ? 'active' : ''; ?>">
        <div class="login-container">
            <div class="section-header">
                <h2 class="section-title"><?php echo $utenteDaModificare ? 'Modifica Utente' : 'Aggiungi Nuovo Utente'; ?></h2>
                <p class="section-subtitle">Compila il modulo per aggiungere o modificare un utente.</p>
            </div>

            <form method="post" action="admin.php" class="admin-form">
                <input type="hidden" name="id_utente" value="<?php echo $utenteDaModificare['id_utente'] ?? ''; ?>">

                <input type="text" id="nome" name="nome" placeholder="nome" required
                       value="<?php echo htmlspecialchars($utenteDaModificare['nome'] ?? ''); ?>">
                <input type="text" id="cognome" name="cognome" placeholder="cognome" required
                       value="<?php echo htmlspecialchars($utenteDaModificare['cognome'] ?? ''); ?>">
                <input type="email" id="email" name="email" placeholder="email" required
                       value="<?php echo htmlspecialchars($utenteDaModificare['email'] ?? ''); ?>">
                
                <!-- Bottone per mostrare l'input -->
                <button type="button" id="btnCambiaPassword" class="btn-primary" onclick="mostraInputPassword()" style="<?php echo $utenteDaModificare ? 'display:block;' : 'display:none;'; ?> border-radius: 12px;">
                    Cambia password
                </button>

                <!-- Input nascosto -->
                <input type="password" id="password" name="password" style="<?php echo $utenteDaModificare ? 'display:none;' : 'display:block;'; ?>" value="<?php echo htmlspecialchars($utenteDaModificare['password'] ?? ''); ?>"
                    placeholder="Nuova password" <?php echo $utenteDaModificare ? '' : 'required'; ?>>
                           
                <select id="ruolo" name="ruolo" required>
                    <option value="admin" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="cuoco" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'cuoco') echo 'selected'; ?>>Cuoco</option>
                    <option value="cameriere" <?php if (($utenteDaModificare['ruolo'] ?? '') === 'cameriere') echo 'selected'; ?>>Cameriere</option>
                </select>

                

                <div class="form-cta-center">
                    <button type="submit" class="btn-primary">
                        <?php echo $utenteDaModificare ? 'Modifica Utente' : 'Aggiungi Utente'; ?>
                    </button>
                </div>
            </form>
        </div>
    </section>

</main>

