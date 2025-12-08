<?php

class Header
{
    public function render($type = 'guest')
    {
        return $this->buildHeader($type);
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
                    <a href="./login.php" class="nav-cta">Logout</a>
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
