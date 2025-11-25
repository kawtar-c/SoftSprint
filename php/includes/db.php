<?php
    require_once(__DIR__ . '/../config/conf.php');
    //Connessione al DB
    function open(){
        $connDB = new mysqli(dbhost, dbuser, dbpass, db);
        return $connDB;
    }
     
    //Disconnessione dal DB
    function close($con){
        $con -> close();
    }

?>