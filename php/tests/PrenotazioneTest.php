<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../class/Prenotazione.php';

class PrenotazioneTest extends TestCase
{
    public function testAggiungiPrenotazione()
    {
        // Mock connessione mysqli (mock del database)
        $dbMock = $this->createMock(mysqli::class);

        // Mock mysqli_stmt (solo execute)
        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                         ->disableOriginalConstructor()
                         ->onlyMethods(['execute'])
                         ->getMock();

        // prepare deve tornare lo statement mockato
        $dbMock->expects($this->once())
               ->method('prepare')
               ->with("INSERT INTO prenotazione (nome, telefono, data, persone, fascia_oraria) VALUES (?, ?, ?, ?, ?)")
               ->willReturn($stmtMock);

        // execute deve essere chiamato e tornare true
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        // Mock della classe Prenotazione ma SENZA mockare i metodi
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['preparaStatement']) // mock del wrapper
                             ->getMock();

        // mocka il wrapper per evitare bind_param reale
        $prenotazione->expects($this->once())
                     ->method('preparaStatement')
                     ->willReturn(true);

        // Inserisco il DB mock nella classe
        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);

        // Chiamo il metodo reale
        $result = $prenotazione->aggiungiPrenotazione(
            "Mario", "333444555", "2025-01-15", 4, "20:00"
        );

        // Test: deve tornare true
        $this->assertTrue($result);
    }


    public function testGetPrenotazioni()
    {
    // Mock della connessione mysqli
    $dbMock = $this->createMock(mysqli::class);

    // Mock del risultato della query
    $resultMock = $this->getMockBuilder(mysqli_result::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods(['fetch_all'])
                       ->getMock();

    // query() deve restituire il result mock
    $dbMock->expects($this->once())
           ->method('query')
           ->with("SELECT * FROM prenotazione")
           ->willReturn($resultMock);

    // fetch_all deve restituire questo array
    $resultMock->expects($this->once())
               ->method('fetch_all')
               ->with(MYSQLI_ASSOC)
               ->willReturn([
                   ["id_prenotazione" => 1, "nome" => "Mario"],
                   ["id_prenotazione" => 2, "nome" => "Giulia"]
               ]);

    // Mock della classe Prenotazione
    $prenotazione = $this->getMockBuilder(Prenotazione::class)
                         ->disableOriginalConstructor()
                         ->onlyMethods([])
                         ->getMock();

    // Inserisco il DB mockato
    $reflection = new ReflectionClass(Prenotazione::class);
    $property = $reflection->getProperty('db');
    $property->setAccessible(true);
    $property->setValue($prenotazione, $dbMock);

    // Chiamo il metodo
    $result = $prenotazione->getPrenotazioni();

    // Verifico che il risultato corrisponde
    $this->assertCount(2, $result);
    $this->assertEquals("Mario", $result[0]["nome"]);
    $this->assertEquals("Giulia", $result[1]["nome"]);
    }

    public function testEliminaPrenotazione()
    {
    // Mock della connessione mysqli
    $dbMock = $this->createMock(mysqli::class);

    // Mock dello statement mysqli_stmt
    $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                     ->disableOriginalConstructor()
                     ->onlyMethods(['bind_param', 'execute'])
                     ->getMock();

    // prepare deve essere chiamato con la query corretta
    $dbMock->expects($this->once())
           ->method('prepare')
           ->with("DELETE FROM prenotazione WHERE id_prenotazione = ?")
           ->willReturn($stmtMock);

    // bind_param deve essere chiamato con il valore corretto
    $stmtMock->expects($this->once())
             ->method('bind_param')
             ->with("i", 10);

    // execute deve ritornare true
    $stmtMock->expects($this->once())
             ->method('execute')
             ->willReturn(true);

    // Mock della classe Prenotazione
    $prenotazione = $this->getMockBuilder(Prenotazione::class)
                         ->disableOriginalConstructor()
                         ->onlyMethods([])
                         ->getMock();

    // Inserisco il DB mockato
    $reflection = new ReflectionClass(Prenotazione::class);
    $property = $reflection->getProperty('db');
    $property->setAccessible(true);
    $property->setValue($prenotazione, $dbMock);

    // Chiamo il metodo
    $result = $prenotazione->eliminaPrenotazione(10);

    // Il risultato deve essere true
    $this->assertTrue($result);
    }

    public function testModificaPrenotazione()
    {
    // Mock della connessione mysqli
    $dbMock = $this->createMock(mysqli::class);

    // Mock dello statement mysqli_stmt
    $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                     ->disableOriginalConstructor()
                     ->onlyMethods(['bind_param', 'execute'])
                     ->getMock();

    // prepare deve essere chiamato con la query corretta
    $dbMock->expects($this->once())
           ->method('prepare')
           ->with("UPDATE prenotazione SET nome = ?, telefono = ?, data = ?, persone = ?, fascia_oraria = ? WHERE id_prenotazione = ?")
           ->willReturn($stmtMock);

    // bind_param deve ricevere i parametri nell'ordine corretto
    $stmtMock->expects($this->once())
             ->method('bind_param')
             ->with(
                 "sssisi",
                 "Luca",          // nome
                 "1234567890",    // telefono
                 "2025-02-10",    // data
                 3,               // persone
                 "20:00",         // fascia oraria
                 7                // id_prenotazione
             );

    // execute deve essere chiamato e restituire true
    $stmtMock->expects($this->once())
             ->method('execute')
             ->willReturn(true);

    // Mock della classe Prenotazione
    $prenotazione = $this->getMockBuilder(Prenotazione::class)
                         ->disableOriginalConstructor()
                         ->onlyMethods([])
                         ->getMock();

    // Inserisco il DB mockato tramite reflection
    $reflection = new ReflectionClass(Prenotazione::class);
    $property = $reflection->getProperty('db');
    $property->setAccessible(true);
    $property->setValue($prenotazione, $dbMock);

    // Chiamo il metodo reale
    $result = $prenotazione->modificaPrenotazione(
        7,
        "Luca",
        "1234567890",
        "2025-02-10",
        3,
        "20:00"
    );

    // Verifico che il metodo ritorni true
    $this->assertTrue($result);
    }
}
