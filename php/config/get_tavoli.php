<?php
header("Content-Type: application/json");

// Dati fake temporanei
echo json_encode([
    "tavoli" => [
        ["id_tavolo" => 1, "numero" => 1, "stato" => "libero"],
        ["id_tavolo" => 2, "numero" => 2, "stato" => "occupato"],
        ["id_tavolo" => 3, "numero" => 3, "stato" => "in_preparazione"]
    ]
]);