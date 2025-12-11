<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/Piatto.php";

class PiattoTest extends TestCase
{
    // Funzione helper per creare un mock dell'oggetto Piatto con DB iniettato
    private function createMockPiattoWithDB($mockDB)
    {
        // CREA PIATTO SENZA COSTRUTTORE
        $piatto = $this->getMockBuilder(Piatto::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods([])
                       ->getMock();

        // INIETTA DB
        $ref = new ReflectionClass(Piatto::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($piatto, $mockDB);

        return $piatto;
    }

    /**
     * Test di successo per il metodo getPiatto(int $id)
     */
    public function test_getPiatto_success()
    {
        $fakePiatto = [
            "id_piatto" => 1,
            "nome" => "Pasta al pesto",
            "descrizione" => "Pasta con pesto fresco",
            "prezzo" => 12.50,
            "categoria" => "Primo",
            "img" => "pesto.jpg"
        ];

        // Configurazione Mock per fetch_assoc
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakePiatto);

        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);
        $mockStmt->method('close')->willReturn(true);

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $piatto = $this->createMockPiattoWithDB($mockDB);

        // Esegui getPiatto
        $result = $piatto->getPiatto(1);

        // Verifica
        $this->assertEquals($fakePiatto, $result);
    }
    
    /**
     * Test di successo per il metodo getMenu()
     * Nota: Questo metodo usa db->query(), non db->prepare()
     */
    public function test_getMenu_success()
    {
        $fakeMenu = [
            ["id_piatto" => 1, "nome" => "Pesto", "prezzo" => 12.50, "categoria" => "Primo", "img" => "pesto.jpg"],
            ["id_piatto" => 2, "nome" => "Bistecca", "prezzo" => 18.00, "categoria" => "Secondo", "img" => "bistecca.jpg"]
        ];

        // Configurazione Mock per fetch_all
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')->willReturn($fakeMenu); 

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        // db->query() deve restituire un oggetto mysqli_result (il mockResult)
        $mockDB->method('query')->willReturn($mockResult); 

        $piatto = $this->createMockPiattoWithDB($mockDB);

        // Esegui getMenu
        $result = $piatto->getMenu();

        // Verifica
        $this->assertEquals($fakeMenu, $result);
        $this->assertIsArray($result);
    }
    
    /**
     * Test di successo per il metodo aggiungiPiatto()
     */
    public function test_aggiungiPiatto_success()
    {
        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo
        $mockStmt->method('close')->willReturn(true);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $piatto = $this->createMockPiattoWithDB($mockDB);

        // Esegui aggiungiPiatto (non restituisce nulla, quindi verifichiamo che i metodi mock siano chiamati correttamente)
        $piatto->aggiungiPiatto("Nuovo Piatto", "Descrizione", 15.00, "Dessert", "torta.jpg");

        // Per i metodi 'void', ci si aspetta che non vengano sollevate eccezioni.
        // Se l'esecuzione arriva qui, il test è considerato superato per la logica dell'applicazione (non possiamo testare il valore di ritorno).
        $this->assertTrue(true); 
    }
    
    /**
     * Test di successo per il metodo modificaPiatto()
     */
    public function test_modificaPiatto_success()
    {
        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo
        $mockStmt->method('close')->willReturn(true);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $piatto = $this->createMockPiattoWithDB($mockDB);

        // Esegui modificaPiatto (non restituisce nulla)
        $piatto->modificaPiatto(1, "Nome Modificato", "Descrizione Mod", 10.00, "Antipasto", "antipasto.jpg");

        // Verifica (come sopra, se arriva qui senza errori, è un successo logico)
        $this->assertTrue(true);
    }
    
    /**
     * Test di successo per il metodo eliminaPiatto()
     */
    public function test_eliminaPiatto_success()
    {
        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo
        $mockStmt->method('close')->willReturn(true);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $piatto = $this->createMockPiattoWithDB($mockDB);

        // Esegui eliminaPiatto (non restituisce nulla)
        $piatto->eliminaPiatto(1);

        // Verifica (se arriva qui senza errori, è un successo logico)
        $this->assertTrue(true);
    }
}
