<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/OrdinePiatto.php";

class OrdinePiattoTest extends TestCase
{
    public function test_aggiungi_piatto()
    {
        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);

        // bind_param deve essere chiamato UNA VOLTA
        $mockStmt->expects($this->once())
                 ->method('bind_param')
                 ->with(
                     $this->equalTo("iii"),
                     $this->equalTo(10),   // id ordine
                     $this->equalTo(5),    // id piatto
                     $this->equalTo(3)     // quantità
                 );

        // execute deve essere chiamato UNA VOLTA
        $mockStmt->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        // MOCK DATABASE
        $mockDB = $this->createMock(mysqli::class);

        // prepare() deve restituire lo statement mock
        $mockDB->method('prepare')->willReturn($mockStmt);

        // CREA OrdinePiatto SENZA COSTRUTTORE
        $op = $this->getMockBuilder(OrdinePiatto::class)
                   ->disableOriginalConstructor()
                   ->onlyMethods([])
                   ->getMock();

        // INIETTA DB MOCK
        $ref = new ReflectionClass(OrdinePiatto::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($op, $mockDB);

        // ESEGUE METODO
        $op->aggiungiPiatto(10, 5, 3);

        // Se non lancia eccezioni → test passato
        $this->assertTrue(true);
    }

    public function test_get_piatti()
    {
        // RISULTATO FALSO CHE IL DB DOVREBBE RESTITUIRE
        $fakePiatti = [
            ["id_piatto" => 1, "nome" => "Pasta", "prezzo" => 10, "quantita" => 2],
            ["id_piatto" => 5, "nome" => "Pizza", "prezzo" => 7, "quantita" => 1]
        ];

        // MOCK RESULT
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')->willReturn($fakePiatti);

        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        // CREA Oggetto SENZA COSTRUTTORE
        $op = $this->getMockBuilder(OrdinePiatto::class)
                   ->disableOriginalConstructor()
                   ->onlyMethods([])
                   ->getMock();

        // INIETTA DB FALSO
        $ref = new ReflectionClass(OrdinePiatto::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($op, $mockDB);

        // ESEGUE METODO
        $result = $op->getPiatti(10);

        // VERIFICA
        $this->assertEquals($fakePiatti, $result);
    }
}
