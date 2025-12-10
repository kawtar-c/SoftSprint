<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../class/Utente.php";

class UtenteTest extends TestCase
{
    // Funzione helper per creare un mock dell'oggetto Utente con DB iniettato
    private function createMockUtenteWithDB($mockDB)
    {
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

        return $utente;
    }

    /**
     * Test di successo per il metodo login()
     */
    public function test_login()
    {
        $fakeUser = [
            "id_utente" => 1,
            "email" => "test@example.com",
            "password" => "12345",
            "nome" => "Mario"
        ];

        // Configurazione Mock per fetch_assoc
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakeUser);

        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->login("test@example.com", "12345");

        $this->assertEquals($fakeUser, $result);
    }
    
    /**
     * Test di successo per il metodo getUtenti()
     */
    public function test_getUtenti()
    {
        $fakeUsers = [
            ["id_utente" => 1, "email" => "a@a.com", "ruolo" => "admin", "nome" => "A", "cognome" => "A"],
            ["id_utente" => 2, "email" => "b@b.com", "ruolo" => "user", "nome" => "B", "cognome" => "B"]
        ];

        // Configurazione Mock per fetch_all
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_all')->willReturn($fakeUsers); 

        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->getUtenti();

        $this->assertEquals($fakeUsers, $result);
    }
    
    /**
     * Test di successo per il metodo getUtente(int $id_utente)
     */
    public function test_getUtente()
    {
        $fakeUser = [
            "id_utente" => 1,
            "email" => "admin@example.com",
            "ruolo" => "admin",
            "nome" => "Admin",
            "cognome" => "User",
            "password" => "hashed_password"
        ];

        // Configurazione Mock per fetch_assoc
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn($fakeUser);

        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('get_result')->willReturn($mockResult);

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->getUtente(1);

        $this->assertEquals($fakeUser, $result);
    }
    
    /**
     * Test di successo per il metodo aggiungiUtente()
     */
    public function test_aggiungiUtente()
    {
        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->aggiungiUtente("new@example.com", "securepass", "user", "Nuovo", "Utente");

        $this->assertTrue($result);
    }
    
    /**
     * Test di successo per il metodo modificaUtente()
     */
    public function test_modificaUtente()
    {
        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->modificaUtente(1, "mod@example.com", "newpass", "admin", "NomeMod", "CogMod");

        $this->assertTrue($result);
    }
    
    /**
     * Test di successo per il metodo eliminaUtente()
     */
    public function test_eliminaUtente()
    {
        // Configurazione Mock per statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true); // Esecuzione ha successo

        // Configurazione Mock per DB
        $mockDB = $this->createMock(mysqli::class);
        $mockDB->method('prepare')->willReturn($mockStmt);

        $utente = $this->createMockUtenteWithDB($mockDB);

        $result = $utente->eliminaUtente(1);

        $this->assertTrue($result);
    }
}
