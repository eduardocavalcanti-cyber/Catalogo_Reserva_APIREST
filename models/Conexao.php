<?php
function conectarBanco(): PDO
{
    $c = require __DIR__ . '/../config/database.php';
    $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset={$c['charset']}";
    return new PDO($dsn, $c['username'], $c['password'], $c['options']);
}
