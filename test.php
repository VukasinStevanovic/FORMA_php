<?php
try {
    $pdo = new PDO(
        'mysql:host=gondola.proxy.rlwy.net;port=31986;dbname=railway;charset=utf8mb4',
        'root',
        'gfCjSoYuYgNjwqYVlqfOMyvIebHWYfUg'
    );
    echo 'Konekcija OK!';
} catch (PDOException $e) {
    echo 'Greška: ' . $e->getMessage();
}