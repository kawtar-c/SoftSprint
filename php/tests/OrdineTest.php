<?php

use PHPUnit\Framework\TestCase;

// includiamo la classe Ordine
require_once __DIR__ . "/../class/Ordine.php";

class OrdineTest extends TestCase
{
    public function test_istanza_classe()
    {
        // Mock del db: sostituiamo open() con una funzione finta
        $ordine = $this->getMockBuilder(Ordine::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods([])
                       ->getMock();

        $this->assertInstanceOf(Ordine::class, $ordine);
    }

    public function test_cambia_stato()
    {
        //  1. Mock del prepared statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('execute')->willReturn(true);

        //  2. Mock del database mysqli
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        //  3. Creiamo Ordine senza costruttore reale
        $ordine = $this->getMockBuilder(Ordine::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Inject del DB mock nella proprieta privata $db
        $ref = new ReflectionClass(Ordine::class);
        $dbProp = $ref->getProperty('db');
        $dbProp->setAccessible(true);
        $dbProp->setValue($ordine, $mockDB);

        //  4. Eseguiamo il metodo reale
        $result = $ordine->cambiaStato(5, 'pronto');

        //  5. Verifica (assert)
        $this->assertTrue($result);
    }

    public function test_get_ordini_attivi()
    {
    //  1. Finto array che il DB dovrebbe restituire
    $fakeResult = [
        ["id_ordine" => 1, "stato" => "inviato"],
        ["id_ordine" => 2, "stato" => "pronto"]
    ];

    //  2. Mock del risultato della query (mysqli_result)
    $mockResult = $this->createMock(mysqli_result::class);
    $mockResult->method('fetch_all')->willReturn($fakeResult);

    //  3. Mock della connessione al DB
    $mockDB = $this->createMock(mysqli::class);
    $mockDB->method('query')->willReturn($mockResult);

    //  4. Creiamo Ordine senza costruttore vero
    $ordine = $this->getMockBuilder(Ordine::class)
        ->disableOriginalConstructor()
        ->onlyMethods([])
        ->getMock();

    //  5. Inseriamo il DB mock nella proprieta privata $db
    $ref = new ReflectionClass(Ordine::class);
    $dbProp = $ref->getProperty('db');
    $dbProp->setAccessible(true);
    $dbProp->setValue($ordine, $mockDB);

    //  6. Eseguiamo il metodo
    $result = $ordine->getOrdiniAttivi();

    //  7. Verifica che il risultato sia esattamente uguale a quello finto
    $this->assertEquals($fakeResult, $result);
    }

    public function test_crea_ordine()
{
    // Simula utente loggato
    $_SESSION['user_id'] = 7;

    // Mock dello statement
    $mockStmt = $this->createMock(mysqli_stmt::class);
    $mockStmt->method('bind_param')->willReturn(true);
    $mockStmt->method('execute')->willReturn(true);

    // Mock del database
    $mockDB = $this->createMock(mysqli::class);
    $mockDB->method('prepare')->willReturn($mockStmt);

    // Mock OrdinePiatto
    $mockOP = $this->createMock(OrdinePiatto::class);
    $mockOP->expects($this->exactly(2))
           ->method('aggiungiPiatto');

    // Mock della classe Ordine OVERRIDANDO getInsertId()
    $ordine = $this->getMockBuilder(Ordine::class)
                   ->setConstructorArgs([$mockDB, $mockOP])
                   ->onlyMethods(['getInsertId'])
                   ->getMock();

    // Simuliamo ID ordine = 55
    $ordine->method('getInsertId')->willReturn(55);

    // Inject del DB mock
    $ref = new ReflectionClass(Ordine::class);
    $propDb = $ref->getProperty('db');
    $propDb->setAccessible(true);
    $propDb->setValue($ordine, $mockDB);

    // Piatti di test
    $piatti = [
        ["id" => 1, "qty" => 2],
        ["id" => 4, "qty" => 1]
    ];

    // Eseguiamo il metodo
    $result = $ordine->creaOrdine(3, $piatti, "niente sale");

    // Verifica
    $this->assertEquals(55, $result);
    }
}

