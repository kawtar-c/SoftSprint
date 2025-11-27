<?php

use PHPUnit\Framework\TestCase;

// includiamo la classe Tavolo
require_once __DIR__ . "/../class/Tavolo.php";

class TavoloTest extends TestCase
{
    /** Test: istanza della classe */
    public function test_istanza_classe()
    {
        $mockDB = $this->createMock(mysqli::class);
        $tavolo = new Tavolo($mockDB); // → mock nel costruttore!
        $this->assertInstanceOf(Tavolo::class, $tavolo);
    }

    /** Test: getTavoli */
    public function test_get_tavoli()
    {
        // MOCK del risultato della query
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')
                   ->willReturn([
                       ["id" => 1, "numero" => 1, "stato" => "libero"],
                       ["id" => 2, "numero" => 2, "stato" => "occupato"]
                   ]);

        // MOCK del DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('query')
               ->willReturn($mockResult);

        // oggetto Tavolo con DB mockato
        $tavolo = new Tavolo($mockDB);

        $lista = $tavolo->getTavoli();

        $this->assertCount(2, $lista);
        $this->assertEquals("libero", $lista[0]["stato"]);
    }

    /** Test: setStato */
    public function test_set_stato()
    {
        // MOCK dello statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);

        // MOCK del DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')
               ->willReturn($mockStmt);

        // Oggetto reale con mock nel costruttore
        $tavolo = new Tavolo($mockDB);

        $this->assertTrue($tavolo->setStato(3, "occupato"));
    }

    /** Test: getStato */
    public function test_get_stato()
    {
        // MOCK risultato della query
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')
                   ->willReturn(["stato" => "libero"]);

        // MOCK dello statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // MOCK del DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')
               ->willReturn($mockStmt);

        $tavolo = new Tavolo($mockDB);

        $stato = $tavolo->getStato(1);

        $this->assertEquals("libero", $stato);
    }
}


