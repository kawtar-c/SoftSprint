<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../class/Prenotazione.php';

class PrenotazioneTest extends TestCase
{
    private function setDbMock(Prenotazione $prenotazione, $dbMock): void
    {
        $reflection = new ReflectionClass(Prenotazione::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($prenotazione, $dbMock);
    }
    
    public function testAggiungiPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute'])
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                 ->method('bind_param')
                 ->willReturn(true); // Cruciale per superare preparaStatement

        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);
        
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->aggiungiPrenotazione(
            "Mario", "333444555", "2025-01-15", 4, "20:00"
        );

        $this->assertTrue($result);
    }

    public function testGetPrenotazioni()
    {
        $dbMock = $this->createMock(mysqli::class);
        $expectedData = [
            ["id_prenotazione" => 1, "nome" => "Mario"],
            ["id_prenotazione" => 2, "nome" => "Giulia"]
        ];

        $resultMock = $this->getMockBuilder(mysqli_result::class)
                            ->disableOriginalConstructor()
                            ->onlyMethods(['fetch_all'])
                            ->getMock();

        $resultMock->expects($this->once())
                    ->method('fetch_all')
                    ->with(MYSQLI_ASSOC)
                    ->willReturn($expectedData);

        // Cruciale per risolvere "size 0"
        $dbMock->expects($this->once())
                ->method('query')
                ->willReturn($resultMock); 

        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->getPrenotazioni();

        $this->assertCount(2, $result);
    }

    public function testEliminaPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute']) 
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                  ->method('bind_param')
                  ->willReturn(true);

        $stmtMock->expects($this->once())
                  ->method('execute')
                  ->willReturn(true);
        
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->eliminaPrenotazione(10);

        $this->assertTrue($result);
    }

    public function testModificaPrenotazione()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute'])
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                  ->method('bind_param')
                  ->willReturn(true);

        $stmtMock->expects($this->once())
                  ->method('execute')
                  ->willReturn(true);
        
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->modificaPrenotazione(
            7, "Luca", "1234567890", "2025-02-10", 3, "20:00", 5
        );

        $this->assertTrue($result);
    }
    
    public function testGetPrenotazioneById()
    {
        $dbMock = $this->createMock(mysqli::class);
        $expectedData = ["id_prenotazione" => 1, "nome" => "Test User"];

        $resultMock = $this->getMockBuilder(mysqli_result::class)
                            ->disableOriginalConstructor()
                            ->onlyMethods(['fetch_assoc'])
                            ->getMock();

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute', 'get_result'])
                             ->getMock();
        
        $dbMock->expects($this->once())
                ->method('prepare')
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                 ->method('bind_param')
                 ->willReturn(true); 

        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        // Cruciale per risolvere "null is of type array"
        $stmtMock->expects($this->once())
                 ->method('get_result')
                 ->willReturn($resultMock);

        $resultMock->expects($this->once())
                    ->method('fetch_assoc')
                    ->willReturn($expectedData);
        
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->getPrenotazioneById(1);

        $this->assertIsArray($result);
        $this->assertEquals("Test User", $result['nome']);
    }

    public function testAssegnaTavolo()
    {
        $dbMock = $this->createMock(mysqli::class);

        $stmtMock = $this->getMockBuilder(mysqli_stmt::class)
                             ->disableOriginalConstructor()
                             ->onlyMethods(['bind_param', 'execute']) 
                             ->getMock();

        $dbMock->expects($this->once())
                ->method('prepare')
                ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
                  ->method('bind_param')
                  ->willReturn(true);

        $stmtMock->expects($this->once())
                  ->method('execute')
                  ->willReturn(true);

        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->setDbMock($prenotazione, $dbMock);

        $result = $prenotazione->assegnaTavolo(12, 5);

        $this->assertTrue($result);
    }
}