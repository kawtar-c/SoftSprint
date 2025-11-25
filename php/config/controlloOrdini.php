<?php
require_once "../includes/session.php";
require_once "../class/Ordine.php";

header('Content-Type: application/json');

if (ob_get_contents()) ob_clean();

// controllo sessione
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'cameriere') {
    echo json_encode([
        'success' => false,
        'message' => 'Non autorizzato',
        'ordini' => []
    ]);
    exit;
}

$ordine = new Ordine();

$ordiniPronti = $ordine->getOrdiniPronti();

echo json_encode([
    'success' => true,
    'ordini' => $ordiniPronti
]);
exit;
?>