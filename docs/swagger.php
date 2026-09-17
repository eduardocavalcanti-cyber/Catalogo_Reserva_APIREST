<?php
require_once __DIR__ . '/../vendor/autoload.php';
$openapi = \OpenApi\Generator::scan([__DIR__]);
header('Content-Type: application/json; charset=utf-8');
echo $openapi->toJson();
