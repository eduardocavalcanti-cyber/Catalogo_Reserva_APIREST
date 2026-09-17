<?php
function carregarConfiguracoes(): void
{
    $arquivo = __DIR__ . '/../.env';
    if (!file_exists($arquivo)) return;
    foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) continue;
        [$nome, $valor] = explode('=', $linha, 2);
        if (getenv(trim($nome)) === false) putenv(trim($nome) . '=' . trim($valor));
    }
}
function conectarBanco(): PDO
{
    carregarConfiguracoes();
    $dsn = 'mysql:host=' . (getenv('DB_HOST') ?: '127.0.0.1') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . (getenv('DB_DATABASE') ?: 'catalogo_reserva') . ';charset=utf8mb4';
    return new PDO($dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
}
