<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/Piatto.php";

class PiattoTest extends TestCase
{
    public function test_get_menu()
    {
        // Finto risultato che il DB dovrebbe restituire
        $fakeMenu = [
            ["id_piatto" => 1, "nome" => "Pasta", "prezzo" => 10],
            ["id_piatto" => 2, "nome" => "Pizza", "prezzo" => 8],
            ["id_piatto" => 3, "nome" => "Insalata", "prezzo" => 5]
        ];

        // Mock del risultato della query
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')->willReturn($fakeMenu);

        // Mock del database
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('query')->willReturn($mockResult);

        // Crea oggetto Piatto senza costruttore reale
        $piatto = $this->getMockBuilder(Piatto::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods([])
                       ->getMock();

        // Inserisci il DB mock nella proprietà privata
        $ref = new ReflectionClass(Piatto::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($piatto, $mockDB);

        // Esegui il metodo
        $result = $piatto->getMenu();

        // Verifica
        $this->assertEquals($fakeMenu, $result);
    }
}
