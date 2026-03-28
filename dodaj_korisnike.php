<?php

require_once 'konekcija.php';

$korisnici = [
    [
        'ime'     => 'Profesor',
        'prezime' => 'Test',
        'email'   => 'profesor@forma_fitness.rs',
        'lozinka' => 'Profesor123!',
        'uloga'   => 'korisnik',
    ],
];

$poruke = [];
$greske = [];

foreach ($korisnici as $k) {
    
    $check = $pdo->prepare('SELECT id FROM korisnici WHERE email = ?');
    $check->execute([$k['email']]);

    if ($check->fetch()) {
        $poruke[] = "⚠️ Korisnik <strong>{$k['email']}</strong> već postoji, preskočen.";
        continue;
    }

    $hash = password_hash($k['lozinka'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('
        INSERT INTO korisnici (ime, prezime, email, lozinka, uloga, aktivan, aktivacioni_token)
        VALUES (?, ?, ?, ?, ?, 1, NULL)
    ');
    $ok = $stmt->execute([$k['ime'], $k['prezime'], $k['email'], $hash, $k['uloga']]);

    if ($ok) {
        $poruke[] = "✅ Dodat korisnik: <strong>{$k['ime']} {$k['prezime']}</strong> ({$k['email']}) — uloga: {$k['uloga']}";
    } else {
        $greske[] = "❌ Greška pri dodavanju: <strong>{$k['email']}</strong>";
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Dodaj korisnike</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #e0e0e0; padding: 30px; }
        h1   { color: #ff6600; margin-bottom: 24px; }
        .ok  { color: #27ae60; margin: 6px 0; }
        .err { color: #e74c3c; margin: 6px 0; }
        .box { background: #1c1c1c; border: 1px solid #333; border-radius: 8px; padding: 20px; max-width: 700px; }
        .info { background: rgba(255,102,0,0.1); border: 1px solid #ff6600; border-radius: 8px; padding: 16px; margin-top: 20px; color: #ff9944; }
        a { color: #ff6600; }
    </style>
</head>
<body>
<div class="box">
    <h1>👤 Dodavanje korisnika</h1>

    <?php foreach ($poruke as $p): ?>
        <p class="ok"><?= $p ?></p>
    <?php endforeach; ?>

    <?php foreach ($greske as $g): ?>
        <p class="err"><?= $g ?></p>
    <?php endforeach; ?>

    <div class="info">
        <strong>Kredencijali za prijavu:</strong><br><br>
        📧 Email: <strong>profesor@forma_fitness.rs</strong><br>
        🔑 Lozinka: <strong>Profesor123!</strong><br>
        👤 Uloga: <strong>korisnik</strong><br><br>
        <strong>⚠️ Obriši ovaj fajl nakon upotrebe!</strong><br><br>
        <a href="/forma_fitness/login.php">→ Idi na prijavu</a>
    </div>
</div>
</body>
</html>
