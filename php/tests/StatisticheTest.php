<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../class/Statistiche.php';

class StatisticheTest extends TestCase
{
    private $dbMock;
    private $statistiche;

    protected function setUp(): void
    {
        // Creiamo il mock di mysqli
        $this->dbMock = $this->getMockBuilder(mysqli::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['query', 'close']) // Aggiungiamo close per sicurezza
            ->getMock();

        // Evitiamo che il mock risulti "chiuso" durante le chiamate procedurali
        $this->statistiche = new Statistiche($this->dbMock);
    }

    public function testGetIncassoOggi()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        // mysqli_query() chiama internamente $db->query()
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')->willReturn(['totale' => 150.50]);

        $result = $this->statistiche->getIncassoOggi();
        $this->assertEquals(150.50, $result);
    }

    public function testGetMediaPrenotazioni()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')->willReturn(['media' => 4.22]);

        $result = $this->statistiche->getMediaPrenotazioni();
        $this->assertEquals(4.2, $result);
    }

    public function testGetTopPiatti()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(
                ['nome' => 'Pasta', 'ordini' => 10],
                ['nome' => 'Pizza', 'ordini' => 8],
                null
            );

        $result = $this->statistiche->getTopPiatti();
        $this->assertCount(2, $result);
        $this->assertEquals('Pasta', $result[0]['nome']);
    }

    public function testGetAffluenzaOraria()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(
                ['fascia' => '20:00', 'totale' => 5],
                null
            );

        $result = $this->statistiche->getAffluenzaOraria();
        $this->assertCount(1, $result);
        $this->assertEquals('20:00', $result[0]['fascia']);
    }

    public function testGetIncassoMese()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')->willReturn(['totale' => 3000.00]);

        $result = $this->statistiche->getIncassoMese();
        $this->assertEquals(3000.00, $result);
    }

    public function testGetIncassoTotaleStorico()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')->willReturn(['totale' => 50000]);

        $result = $this->statistiche->getIncassoTotaleStorico();
        $this->assertEquals(50000, $result);
    }

    public function testGetIncassiAnnuali()
    {
        $resultMock = $this->createMock(mysqli_result::class);
        
        $this->dbMock->expects($this->once())
                     ->method('query')
                     ->willReturn($resultMock);

        $resultMock->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(
                ['mese' => 1, 'totale' => 1000],
                ['mese' => 5, 'totale' => 2000],
                null
            );

        $result = $this->statistiche->getIncassiAnnuali();
        
        $this->assertCount(12, $result);
        $this->assertEquals(1000, $result[1]);
        $this->assertEquals(2000, $result[5]);
        $this->assertEquals(0, $result[12]);
    }
}