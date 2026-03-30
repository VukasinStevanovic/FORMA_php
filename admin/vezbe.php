<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'obrisi' && $id > 0) {
    $stmt = $pdo->prepare('SELECT slika FROM vezbe WHERE id = ?');
    $stmt->execute([$id]);
    $red = $stmt->fetch();
    if ($red && $red['slika']) {
        $put = ROOT_PATH . 'uploads/' . $red['slika'];
        if (file_exists($put)) unlink($put);
    }

    $pdo->prepare('DELETE FROM vezbe WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Vežba je obrisana.');
    header('Location: ' . BASE_URL . '/admin/vezbe.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv      = trim($_POST['naziv']      ?? '');
    $opis       = trim($_POST['opis']       ?? '');
    $grupa      = trim($_POST['grupa_misica'] ?? '');
    $tip        = trim($_POST['tip_vezbe']  ?? '');
    $tezina     = $_POST['tezina'] ?? 'srednje';
    $edit_id    = (int)($_POST['edit_id'] ?? 0);

    if (strlen($naziv) < 2) $greske[] = 'Naziv mora imati najmanje 2 karaktera.';
    if (strlen($opis)  < 5) $greske[] = 'Opis mora imati najmanje 5 karaktera.';

    $nova_slika = null;
    if (!empty($_FILES['slika']['name'])) {
        $nova_slika = upload_sliku($_FILES['slika'], 'uploads');
        if (!$nova_slika) {
            $greske[] = 'Greška pri uploadu slike. Dozvoljeni formati: JPG, PNG, GIF, WEBP (max 5MB).';
        }
    }

    if (empty($greske)) {
        if ($edit_id > 0) {
            if ($nova_slika) {
                $old = $pdo->prepare('SELECT slika FROM vezbe WHERE id = ?');
                $old->execute([$edit_id]);
                $old_red = $old->fetch();
                if ($old_red['slika']) {
                    $put = ROOT_PATH . 'uploads/' . $old_red['slika'];
                    if (file_exists($put)) unlink($put);
                }
                $stmt = $pdo->prepare('UPDATE vezbe SET naziv=?, opis=?, slika=?, grupa_misica=?, tip_vezbe=?, tezina=? WHERE id=?');
                $stmt->execute([$naziv, $opis, $nova_slika, $grupa, $tip, $tezina, $edit_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE vezbe SET naziv=?, opis=?, grupa_misica=?, tip_vezbe=?, tezina=? WHERE id=?');
                $stmt->execute([$naziv, $opis, $grupa, $tip, $tezina, $edit_id]);
            }
            postaviti_flash('uspeh', 'Vežba "' . $naziv . '" je ažurirana.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO vezbe (naziv, opis, slika, grupa_misica, tip_vezbe, tezina) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$naziv, $opis, $nova_slika, $grupa, $tip, $tezina]);
            postaviti_flash('uspeh', 'Vežba "' . $naziv . '" je dodana.');
        }
        header('Location: ' . BASE_URL . '/admin/vezbe.php');
        exit();
    }
    $akcija = $edit_id > 0 ? 'izmeni' : 'nova';
    $id = $edit_id;
}

$vezba = null;
if (($akcija === 'izmeni') && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM vezbe WHERE id = ?');
    $stmt->execute([$id]);
    $vezba = $stmt->fetch();
    if (!$vezba) {
        postaviti_flash('greska', 'Vežba nije pronađena.');
        header('Location: ' . BASE_URL . '/admin/vezbe.php');
        exit();
    }
}

$vezbe = $pdo->query('SELECT id, naziv, grupa_misica, tezina, tip_vezbe, datum_dodavanja FROM vezbe ORDER BY naziv ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vežbe – Admin | Forma Fitness</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">

        <h1><?= ($akcija === 'nova' || $akcija === 'izmeni') ? ($akcija === 'nova' ? 'Nova vežba' : 'Izmeni vežbu') : '<i class="fa-solid fa-dumbbell si"></i> Vežbe' ?></h1>

        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?>
            <div class="flash-poruka flash-greska"><?= e($g) ?></div>
        <?php endforeach; ?>

        <?php if ($akcija === 'nova' || $akcija === 'izmeni'): ?>

            <a href="<?= BASE_URL ?>/admin/vezbe.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>

            <div class="p-box" style="max-width:600px;">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="edit_id" value="<?= $vezba['id'] ?? 0 ?>">

                    <div class="forma-group">
                        <label>Naziv vežbe *</label>
                        <input type="text" name="naziv" value="<?= e($vezba['naziv'] ?? $_POST['naziv'] ?? '') ?>" required>
                    </div>

                    <div class="forma-group">
                        <label>Opis / Tehnika *</label>
                        <textarea name="opis" rows="6" required><?= e($vezba['opis'] ?? $_POST['opis'] ?? '') ?></textarea>
                    </div>

                    <div class="grid-2" style="gap:12px;">
                        <div class="forma-group">
                            <label>Grupa mišića</label>
                            <input type="text" name="grupa_misica" value="<?= e($vezba['grupa_misica'] ?? $_POST['grupa_misica'] ?? '') ?>" placeholder="Npr. Grudi, Leđa...">
                        </div>
                        <div class="forma-group">
                            <label>Tip vežbe</label>
                            <input type="text" name="tip_vezbe" value="<?= e($vezba['tip_vezbe'] ?? $_POST['tip_vezbe'] ?? '') ?>" placeholder="Slobodni tegovi, Sprave...">
                        </div>
                    </div>

                    <div class="forma-group">
                        <label>Težina</label>
                        <select name="tezina">
                            <option value="lako"    <?= ($vezba['tezina'] ?? '') === 'lako'    ? 'selected' : '' ?>>Lako</option>
                            <option value="srednje" <?= ($vezba['tezina'] ?? 'srednje') === 'srednje' ? 'selected' : '' ?>>Srednje</option>
                            <option value="tesko"   <?= ($vezba['tezina'] ?? '') === 'tesko'   ? 'selected' : '' ?>>Teško</option>
                        </select>
                    </div>

                    <div class="forma-group">
                        <label>Slika <?= $vezba ? '(ostaviti prazno da bi se zadržala trenutna)' : '' ?></label>
                        <?php if (!empty($vezba['slika'])): ?>
                            <div style="margin-bottom:8px;">
                                <img src="<?= BASE_URL ?>/uploads/<?= e($vezba['slika']) ?>" style="height:80px;border-radius:4px;">
                                <small style="display:block;color:var(--text3);margin-top:4px;"><?= e($vezba['slika']) ?></small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="slika" accept="image/*">
                        <small style="color:var(--text3);">JPG, PNG, GIF, WEBP — max 5MB</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $vezba ? 'Sačuvaj izmene' : 'Dodaj vežbu' ?>
                    </button>
                </form>
            </div>

        <?php else: ?>

            <div style="margin-bottom:16px;">
                <a href="?akcija=nova" class="btn btn-primary btn-sm">+ Dodaj novu vežbu</a>
            </div>

            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead>
                        <tr><th>Naziv</th><th>Grupa mišića</th><th>Tip</th><th>Težina</th><th>Datum</th><th>Akcije</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($vezbe)): ?>
                        <tr><td colspan="6" style="text-align:center;color:var(--text2);padding:30px;">Nema vežbi</td></tr>
                    <?php else: ?>
                        <?php foreach ($vezbe as $v): ?>
                        <tr>
                            <td><strong><?= e($v['naziv']) ?></strong></td>
                            <td><?= e($v['grupa_misica'] ?: '-') ?></td>
                            <td><?= e($v['tip_vezbe'] ?: '-') ?></td>
                            <td><span class="badge <?= tezina_klasa($v['tezina']) ?>"><?= tezina_labela($v['tezina']) ?></span></td>
                            <td><?= formatirati_datum($v['datum_dodavanja'], 'd.m.Y.') ?></td>
                            <td style="white-space:nowrap;">
                                <a href="?akcija=izmeni&id=<?= $v['id'] ?>" class="btn btn-outline btn-sm">Izmeni</a>
                                <a href="?akcija=obrisi&id=<?= $v['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Obrisati vežbu <?= e(addslashes($v['naziv'])) ?>?')">Obriši</a>
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
