<?php

class Header
{
    public function render($type = 'guest')
    {
        return '<!-- HEADER -->
                <!DOCTYPE html>
                <html lang="it">
                <head>
                    <meta charset="UTF-8" />
                    <title>Ristorante di Softsprint</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

                    <!-- Google Fonts -->
                    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

                    <!-- CSS  -->
                    <link rel="stylesheet" href="../css/style.css">

                    <!-- JS -->
                    <script src="../js/main.js"></script>
                </head>

                <body>' . $this->buildHeader($type) ;
    }

    private function buildHeader($type)
    {
        $buttons = $this->getButtons($type);

        return '
        <header class="navbar">
            <div class="container navbar-inner">
                <div class="logo">Ristorante Softsprint</div>
                <nav class="nav-links"></nav>
                <div class="nav-right-buttons">
                    ' . $buttons . '
                </div>
            </div>
        </header>
        ';
    }

    private function getButtons($type)
    {
        switch ($type) {

            case "guest":    // Home + Login
                return '
                    <a href="../index.php" class="nav-cta">Home</a>
                    <a href="./login.php" class="nav-cta">Login</a>
                ';

            case "user":     // Home + Logout
                return '
                    <a href="../index.php" class="nav-cta">Home</a>
                    <a href="./logout.php" class="nav-cta">Logout</a>
                ';

            case "simple":   // Solo Home
                return '
                    <a href="../index.php" class="nav-cta">Home</a>
                ';

            default:
                return '
                    <a href="../index.php" class="nav-cta">Home</a>
                    <a href="./login.php" class="nav-cta">Login</a>
                ';
        }
    }
}

?>
