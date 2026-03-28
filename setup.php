<?php
require_once 'konekcija.php';

$poruke = [];
$greske = [];

$admin_lozinka = 'Admin123!';
$hash = password_hash($admin_lozinka, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('UPDATE korisnici SET lozinka = ? WHERE email = ? AND uloga = ?');
$ok = $stmt->execute([$hash, 'admin@forma_fitness.rs', 'admin']);

if ($ok && $stmt->rowCount() > 0) {
    $poruke[] = '✅ Admin lozinka je uspešno postavljena za: admin@forma_fitness.rs';
} elseif ($ok && $stmt->rowCount() === 0) {
    $greske[] = '⚠️ Admin nalog nije pronađen. Proverite da li je baza.sql pravilno importovana.';
} else {
    $greske[] = '❌ Greška pri ažuriranju admin lozinke.';
}

$uploads_folder = __DIR__ . '/uploads';
if (!is_dir($uploads_folder)) {
    if (mkdir($uploads_folder, 0755, true)) {
        $poruke[] = '✅ Folder /uploads/ je kreiran.';
    } else {
        $greske[] = '❌ Nije moguće kreirati /uploads/ folder. Kreirajte ga ručno.';
    }
} else {
    $poruke[] = '✅ Folder /uploads/ već postoji.';
}

$tabele = ['korisnici', 'meni_stavke', 'treneri', 'vezbe', 'casovi', 'clanarine', 'kontakt_poruke', 'ankete', 'anketa_odgovori', 'anketa_glasovi'];
$stmt = $pdo->query("SHOW TABLES");
$postojece = array_column($stmt->fetchAll(), 'Tables_in_forma_fitness_db');

foreach ($tabele as $tabela) {
    if (in_array($tabela, $postojece)) {
        $poruke[] = "✅ Tabela <strong>{$tabela}</strong> postoji.";
    } else {
        $greske[] = "❌ Tabela <strong>{$tabela}</strong> ne postoji! Importujte baza.sql.";
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Forma Fitness Setup</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #e0e0e0; padding: 30px; }
        h1   { color: #ff6600; margin-bottom: 24px; }
        .ok  { color: #27ae60; margin: 6px 0; }
        .err { color: #e74c3c; margin: 6px 0; }
        .box { background: #1c1c1c; border: 1px solid #333; border-radius: 8px; padding: 20px; max-width: 700px; }
        .upozorenje { background: rgba(255,102,0,0.1); border: 1px solid #ff6600; border-radius: 8px; padding: 16px; margin-top: 20px; color: #ff9944; }
        a { color: #ff6600; }
    </style>
</head>
<body>
<div class="box">
    <h1>🔧 Forma Fitness - Setup</h1>

    <?php foreach ($poruke as $p): ?>
        <p class="ok"><?= $p ?></p>
    <?php endforeach; ?>

    <?php foreach ($greske as $g): ?>
        <p class="err"><?= $g ?></p>
    <?php endforeach; ?>

    <?php if (empty($greske)): ?>
        <div class="upozorenje">
            <strong>⚠️ Napomena:</strong> Setup je završen uspešno!<br>
            <strong>Odmah obrišite ili preimenujte ovaj fajl</strong> radi bezbednosti.<br><br>
            Admin kredencijali:<br>
            📧 Email: <strong>admin@forma_fitness.rs</strong><br>
            🔑 Lozinka: <strong>Admin123!</strong><br><br>
            <a href="/forma_fitness/index.php">→ Idi na početnu stranicu</a><br>
            <a href="/forma_fitness/login.php">→ Prijavi se kao admin</a>
        </div>
    <?php else: ?>
        <div class="upozorenje">
            <strong>❌ Ima grešaka!</strong><br>
            Proverite da li ste importovali <strong>baza.sql</strong> u phpMyAdmin.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
