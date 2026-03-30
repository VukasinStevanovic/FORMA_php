<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'obrisi' && $id > 0) {
    $stmt = $pdo->prepare('SELECT slika FROM treneri WHERE id = ?');
    $stmt->execute([$id]);
    $red = $stmt->fetch();
    if ($red && $red['slika']) {
        $put = ROOT_PATH . 'uploads/' . $red['slika'];
        if (file_exists($put)) unlink($put);
    }
    $pdo->prepare('DELETE FROM treneri WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Trener je obrisan.');
    header('Location: ' . BASE_URL . '/admin/treneri.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime         = trim($_POST['ime']          ?? '');
    $prezime     = trim($_POST['prezime']       ?? '');
    $opis        = trim($_POST['opis']          ?? '');
    $specijalnost = trim($_POST['specijalnost'] ?? '');
    $pol         = in_array($_POST['pol'] ?? '', ['m','z']) ? $_POST['pol'] : 'm';
    $edit_id     = (int)($_POST['edit_id']      ?? 0);

    if (strlen($ime) < 2)     $greske[] = 'Ime je obavezno.';
    if (strlen($prezime) < 2) $greske[] = 'Prezime je obavezno.';

    $nova_slika = null;
    if (!empty($_FILES['slika']['name'])) {
        $nova_slika = upload_sliku($_FILES['slika'], 'uploads');
        if (!$nova_slika) $greske[] = 'Greška pri uploadu slike.';
    }

    if (empty($greske)) {
        if ($edit_id > 0) {
            if ($nova_slika) {
                $old = $pdo->prepare('SELECT slika FROM treneri WHERE id = ?');
                $old->execute([$edit_id]);
                $old_red = $old->fetch();
                if ($old_red['slika']) { $put = ROOT_PATH . 'uploads/' . $old_red['slika']; if (file_exists($put)) unlink($put); }
                $pdo->prepare('UPDATE treneri SET ime=?, prezime=?, slika=?, opis=?, specijalnost=?, pol=? WHERE id=?')
                    ->execute([$ime, $prezime, $nova_slika, $opis, $specijalnost, $pol, $edit_id]);
            } else {
                $pdo->prepare('UPDATE treneri SET ime=?, prezime=?, opis=?, specijalnost=?, pol=? WHERE id=?')
                    ->execute([$ime, $prezime, $opis, $specijalnost, $pol, $edit_id]);
            }
            postaviti_flash('uspeh', 'Trener je ažuriran.');
        } else {
            $pdo->prepare('INSERT INTO treneri (ime, prezime, slika, opis, specijalnost, pol) VALUES (?,?,?,?,?,?)')
                ->execute([$ime, $prezime, $nova_slika, $opis, $specijalnost, $pol]);
            postaviti_flash('uspeh', 'Trener je dodan.');
        }
        header('Location: ' . BASE_URL . '/admin/treneri.php'); exit();
    }
    $akcija = $edit_id > 0 ? 'izmeni' : 'novi';
    $id = $edit_id;
}

$trener = null;
if ($akcija === 'izmeni' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM treneri WHERE id = ?');
    $stmt->execute([$id]);
    $trener = $stmt->fetch();
    if (!$trener) { header('Location: ' . BASE_URL . '/admin/treneri.php'); exit(); }
}

$treneri = $pdo->query('SELECT * FROM treneri ORDER BY ime ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Treneri – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1><?= in_array($akcija, ['novi','izmeni']) ? ($akcija === 'novi' ? 'Novi trener' : 'Izmeni trenera') : '👨‍💼 Treneri' ?></h1>
        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?><div class="flash-poruka flash-greska"><?= e($g) ?></div><?php endforeach; ?>

        <?php if (in_array($akcija, ['novi','izmeni'])): ?>
            <a href="<?= BASE_URL ?>/admin/treneri.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>
            <div class="p-box" style="max-width:500px;">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="edit_id" value="<?= $trener['id'] ?? 0 ?>">
                    <div class="grid-2" style="gap:12px;">
                        <div class="forma-group">
                            <label>Ime *</label>
                            <input type="text" name="ime" value="<?= e($trener['ime'] ?? '') ?>" required>
                        </div>
                        <div class="forma-group">
                            <label>Prezime *</label>
                            <input type="text" name="prezime" value="<?= e($trener['prezime'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="forma-group">
                        <label>Pol</label>
                        <select name="pol">
                            <option value="m" <?= ($trener['pol'] ?? 'm') === 'm' ? 'selected' : '' ?>>Muško</option>
                            <option value="z" <?= ($trener['pol'] ?? '') === 'z' ? 'selected' : '' ?>>Žensko</option>
                        </select>
                    </div>
                    <div class="forma-group">
                        <label>Specijalnost</label>
                        <input type="text" name="specijalnost" value="<?= e($trener['specijalnost'] ?? '') ?>" placeholder="Npr. Yoga, Crossfit, Powerlifting">
                    </div>
                    <div class="forma-group">
                        <label>Opis</label>
                        <textarea name="opis" rows="5"><?= e($trener['opis'] ?? '') ?></textarea>
                    </div>
                    <div class="forma-group">
                        <label>Profilna slika</label>
                        <?php if (!empty($trener['slika'])): ?>
                            <div style="margin-bottom:8px;">
                                <img src="<?= BASE_URL ?>/uploads/<?= e($trener['slika']) ?>" style="height:70px;width:70px;object-fit:cover;border-radius:50%;border:2px solid var(--orange);">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="slika" accept="image/*">
                        <small style="color:var(--text3);">JPG, PNG — max 5MB. Ostaviti prazno za zadržavanje trenutne.</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $trener ? 'Sačuvaj' : 'Dodaj trenera' ?></button>
                </form>
            </div>
        <?php else: ?>
            <div style="margin-bottom:16px;">
                <a href="?akcija=novi" class="btn btn-primary btn-sm">+ Novi trener</a>
            </div>
            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Slika</th><th>Ime i prezime</th><th>Specijalnost</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php if (empty($treneri)): ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--text2);padding:30px;">Nema trenera</td></tr>
                    <?php else: ?>
                        <?php foreach ($treneri as $t): ?>
                        <tr>
                            <td>
                                <?php if ($t['slika']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/<?= e($t['slika']) ?>" style="width:45px;height:45px;object-fit:cover;border-radius:50%;">
                                <?php else: ?>
                                    <div style="width:45px;height:45px;background:var(--bg3);border-radius:50%;display:flex;align-items:center;justify-content:center;">👤</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= e($t['ime'] . ' ' . $t['prezime']) ?></strong></td>
                            <td><?= e($t['specijalnost'] ?: '-') ?></td>
                            <td style="white-space:nowrap;">
                                <a href="?akcija=izmeni&id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Izmeni</a>
                                <a href="?akcija=obrisi&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Obrisati trenera?')">Obriši</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
