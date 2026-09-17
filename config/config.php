<?php
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_DATABASE') ?: getenv('DB_NAME') ?: 'catalogo_reserva');
define('DB_DATABASE', DB_NAME);
define('DB_USER', getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'root');
define('DB_USERNAME', DB_USER);
define('DB_PASS', getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '');
define('DB_PASSWORD', DB_PASS);
define('DB_CHARSET', 'utf8mb4');
