<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/Utente.php";

class UtenteTest extends TestCase
{
    public function test_login_success()
    {
        // Finto risultato utente
        $fakeUser = [
            "id_utente" => 1,
            "email" => "test@example.com",
            "password" => "12345",
            "nome" => "Mario"
        ];

        // MOCK RESULT
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakeUser);

        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        // CREA UTENTE SENZA COSTRUTTORE
        $utente = $this->getMockBuilder(Utente::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods([])
                       ->getMock();

        // INIETTA DB
        $ref = new ReflectionClass(Utente::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($utente, $mockDB);

        // ESEGUE LOGIN
        $result = $utente->login("test@example.com", "12345");

        // VERIFICA
        $this->assertEquals($fakeUser, $result);
    }

    public function test_login_wrong_password()
    {
        // Finto utente ma password errata
        $fakeUser = [
            "id_utente" => 1,
            "email" => "test@example.com",
            "password" => "12345" // password vera
        ];

        // MOCK RESULT
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakeUser);

        // MOCK STATEMENT
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // MOCK DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        // CREA UTENTE SENZA COSTRUTTORE
        $utente = $this->getMockBuilder(Utente::class)
                       ->disableOriginalConstructor()
                       ->onlyMethods([])
                       ->getMock();

        // INIETTA DB
        $ref = new ReflectionClass(Utente::class);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($utente, $mockDB);

        // ESEGUE LOGIN CON PASSWORD SBAGLIATA
        $result = $utente->login("test@example.com", "00000");

        // VERIFICA
        $this->assertNull($result);
    }
}
