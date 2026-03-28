<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST',    'localhost');
define('DB_NAME',    'forma_fitness_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', '/forma_fitness');

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST, DB_NAME, DB_CHARSET
    );

    $opcije = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcije);

} catch (PDOException $e) {
    error_log('Forma Fitness DB greška: ' . $e->getMessage());
    die('
    <div style="background:#1a1a1a;color:#ff6600;font-family:sans-serif;padding:40px;text-align:center;">
        <h2>&#9888; Greška konekcije</h2>
        <p style="color:#ccc;">Nije moguće povezati se sa bazom podataka.<br>
        Proverite da li je XAMPP pokrenut i da li je baza <strong>forma_fitness_db</strong> kreirana.</p>
        <small style="color:#666;">Detalji greške su zapisani u error log.</small>
    </div>');
}
