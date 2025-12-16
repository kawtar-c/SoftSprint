<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../class/Prenotazione.php';

class PrenotazioneTest extends TestCase
{
    public function testAggiungiPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['execute', 'bind_param', 'close'])
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->with(
                    "INSERT INTO prenotazione (nome, telefono, data, persone, fascia_oraria) VALUES (?, ?, ?, ?, ?)"
                )
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                 ->method('bind_param')
                 ->with(
                     "sssis", 
                     "Mario", 
                     "333444555", 
                     "2025-01-15", 
                     4, 
                     "20:00"
                 )
                 ->willReturn(true);

        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);
        
        $stmtMock->expects($this->once())
                 ->method('close');

        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);

        $result = $prenotazione->aggiungiPrenotazione(
            "Mario",
            "333444555",
            "2025-01-15",
            4,
            "20:00"
        );

        $this->assertTrue($result);
    }

    public function testGetPrenotazioni()
    {
        $dbMock = $this->createMock(mysqli::class);

        $resultMock = $this->getMockBuilder(mysqli_result::class)
                            ->disableOriginalConstructor()
                            ->onlyMethods(['fetch_all'])
                            ->getMock();

        $dbMock->expects($this->once())
                ->method('query')
                ->with("SELECT * FROM prenotazione")
                ->willReturn($resultMock);

        $resultMock->expects($this->once())
                    ->method('fetch_all')
                    ->with(MYSQLI_ASSOC)
                    ->willReturn([
                        ["id_prenotazione" => 1, "nome" => "Mario"],
                        ["id_prenotazione" => 2, "nome" => "Giulia"]
                    ]);

        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);

        $result = $prenotazione->getPrenotazioni();

        $this->assertCount(2, $result);
        $this->assertEquals("Mario", $result[0]["nome"]);
        $this->assertEquals("Giulia", $result[1]["nome"]);
    }

    public function testEliminaPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute', 'close']) 
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->with("DELETE FROM prenotazione WHERE id_prenotazione = ?")
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                  ->method('bind_param')
                  ->with("i", 10)
                  ->willReturn(true);

        $stmtMock->expects($this->once())
                  ->method('execute')
                  ->willReturn(true);
        
        $stmtMock->expects($this->once())
                 ->method('close');

        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);

        $result = $prenotazione->eliminaPrenotazione(10);

        $this->assertTrue($result);
    }

    public function testModificaPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute', 'close'])
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->with(
                    "UPDATE prenotazione SET nome = ?, telefono = ?, data = ?, persone = ?, fascia_oraria = ?, id_tavolo = ? WHERE id_prenotazione = ?;"
                )
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                  ->method('bind_param')
                  ->with(
                      "sssisii",
                      "Luca",
                      "1234567890",
                      "2025-02-10",
                      3,
                      "20:00",
                      5,
                      7
                  )
                  ->willReturn(true);

        $stmtMock->expects($this->once())
                  ->method('execute')
                  ->willReturn(true);
        
        $stmtMock->expects($this->once())
                 ->method('close');


        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);

        $result = $prenotazione->modificaPrenotazione(
            7,
            "Luca",
            "1234567890",
            "2025-02-10",
            3,
            "20:00",
            5
        );

        $this->assertTrue($result);
    }
}