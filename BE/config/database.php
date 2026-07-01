<?php

$envpath = __DIR__ . '/../.env';

if (!file_exists($envpath)) {
    die("File .env cant found");
}

$env = parse_ini_file($envpath);
$db_host = $env['DB_HOST'];
$db_port = $env['DB_PORT'];
$db_user = $env['DB_USER'] ;
$db_password = $env['DB_PASSWORD'];

$dsn = "pgsql:host=$db_host;port=$db_port;dbname={$env['DB_NAME']}";


$options =[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_password, $options);
}catch(\PDOException $e){
    error_log("Database error: " . $e->getMessage());
    die("Database connection failed. Please check the logs for more details.". $e->getMessage());
};