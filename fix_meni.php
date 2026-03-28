<?php
require_once 'konekcija.php';

// Ažurira sve URL-ove u meni_stavke koji počinju sa /forma_fitness/
$stmt = $pdo->prepare("UPDATE meni_stavke SET url = REPLACE(url, '/forma_fitness/', '/') WHERE url LIKE '/forma_fitness/%'");
$stmt->execute();
$izmenjeno = $stmt->rowCount();

echo "<pre>Ažurirano redova: $izmenjeno\n\n";

$sve = $pdo->query("SELECT id, naziv, url FROM meni_stavke ORDER BY redosled")->fetchAll();
foreach ($sve as $s) {
    echo "ID {$s['id']}: {$s['naziv']} → {$s['url']}\n";
}
echo "</pre>";
echo "<br><strong style='color:red'>OBRIŠITE ovaj fajl posle pokretanja!</strong>";
