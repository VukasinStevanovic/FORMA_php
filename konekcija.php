<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST',    'gondola.proxy.rlwy.net');
define('DB_PORT',    '31986');
define('DB_NAME',    'railway');
define('DB_USER',    'root');
define('DB_PASS',    'gfCjSoYuYgNjwqYVlqfOMyvIebHWYfUg');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', '');

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    $opcije = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcije);

    // Migracija: dodati pol kolonu u treneri ako ne postoji
    $pdo->exec("ALTER TABLE treneri ADD COLUMN IF NOT EXISTS pol ENUM('m','z') NOT NULL DEFAULT 'm'");

    // Migracija: ažurirati meni stavku za casovi -> treninzi
    $pdo->exec("UPDATE meni_stavke SET naziv='Treninzi', url='/treninzi.php' WHERE url LIKE '%casovi%'");

} catch (PDOException $e) {
    error_log('Forma Fitness DB greška: ' . $e->getMessage());
    die('
    <div style="background:#1a1a1a;color:#ff6600;font-family:sans-serif;padding:40px;text-align:center;">
        <h2>&#9888; Greška konekcije</h2>
        <p style="color:#ccc;">Nije moguće povezati se sa bazom podataka.<br>
        Proverite da li je baza dostupna.</p>
        <small style="color:#666;">Detalji greške su zapisani u error log.</small>
    </div>');
}