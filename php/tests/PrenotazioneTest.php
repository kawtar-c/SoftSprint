<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/Prenotazione.php";

class PrenotazioneTest extends TestCase
{
    private function createMockPrenotazioneWithDB($mockDB)
    {
        $prenotazione = $this->getMockBuilder(Prenotazione::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['preparaStatement'])
                               ->getMock();

        $prenotazione->method('preparaStatement')->willReturn(true);

        $ref = new ReflectionClass(Prenotazione::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($prenotazione, $mockDB);

        return $prenotazione;
    }

    public function test_getPrenotazioneById()
    {
        $fakeReservation = [
            "id_prenotazione" => 5,
            "nome" => "Mario Rossi",
            "persone" => 4
        ];

        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakeReservation);

        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->getPrenotazioneById(5);

        $this->assertEquals($fakeReservation, $result);
    }

    public function test_getPrenotazioni()
    {
        $fakeReservations = [
            ["id_prenotazione" => 1, "nome" => "A"],
            ["id_prenotazione" => 2, "nome" => "B"]
        ];

        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')->willReturn($fakeReservations); 

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('query')->willReturn($mockResult);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->getPrenotazioni();

        $this->assertEquals($fakeReservations, $result);
    }

    public function test_aggiungiPrenotazione()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('execute')->willReturn(true); 

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->aggiungiPrenotazione("Nuovo", "111222333", "2026-01-01", 3, "21:00");

        $this->assertTrue($result);
    }
    
    public function test_eliminaPrenotazione()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->eliminaPrenotazione(10);

        $this->assertTrue($result);
    }

    public function test_modificaPrenotazione()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); 

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->modificaPrenotazione(
            1, "Modificato", "999888777", "2026-01-02", 5, "22:00", 3
        );

        $this->assertTrue($result);
    }

    public function test_assegnaTavolo()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);

        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $prenotazione = $this->createMockPrenotazioneWithDB($mockDB);

        $result = $prenotazione->assegnaTavolo(1, 5);

        $this->assertTrue($result);
    }
}