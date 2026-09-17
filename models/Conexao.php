<?php
require_once __DIR__ . '/../config/database.php';

class Conexao
{
    private static ?PDO $unica = null;
    private function __construct() {}
    public static function conectar(): PDO
    {
        if (self::$unica === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_DATABASE . ';charset=' . DB_CHARSET;
            self::$unica = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$unica;
    }
}

function conectarBanco(): PDO
{
    return Conexao::conectar();
}
