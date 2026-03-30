<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'obrisi' && $id > 0) {
    $pdo->prepare('DELETE FROM casovi WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Trening je obrisan.');
    header('Location: ' . BASE_URL . '/admin/treninzi.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv     = trim($_POST['naziv']     ?? '');
    $trener_id = (int)($_POST['trener_id'] ?? 0);
    $opis      = trim($_POST['opis']      ?? '');
    $dan       = trim($_POST['dan_u_nedelji'] ?? '');
    $vreme     = trim($_POST['vreme']     ?? '');
    $kapacitet = (int)($_POST['kapacitet'] ?? 20);
    $aktivan   = isset($_POST['aktivan']) ? 1 : 0;
    $edit_id   = (int)($_POST['edit_id']  ?? 0);

    if (strlen($naziv) < 2) $greske[] = 'Naziv mora imati najmanje 2 karaktera.';
    if (empty($dan))        $greske[] = 'Izaberite dan u nedelji.';
    if (empty($vreme))      $greske[] = 'Unesite vreme treninga.';

    if (empty($greske)) {
        $trener_id = $trener_id > 0 ? $trener_id : null;
        if ($edit_id > 0) {
            $pdo->prepare('UPDATE casovi SET naziv=?, trener_id=?, opis=?, dan_u_nedelji=?, vreme=?, kapacitet=?, aktivan=? WHERE id=?')
                ->execute([$naziv, $trener_id, $opis, $dan, $vreme, $kapacitet, $aktivan, $edit_id]);
            postaviti_flash('uspeh', 'Trening je ažuriran.');
        } else {
            $pdo->prepare('INSERT INTO casovi (naziv, trener_id, opis, dan_u_nedelji, vreme, kapacitet, aktivan) VALUES (?,?,?,?,?,?,?)')
                ->execute([$naziv, $trener_id, $opis, $dan, $vreme, $kapacitet, $aktivan]);
            postaviti_flash('uspeh', 'Trening "' . $naziv . '" je dodan.');
        }
        header('Location: ' . BASE_URL . '/admin/treninzi.php'); exit();
    }
    $akcija = $edit_id > 0 ? 'izmeni' : 'novi';
    $id = $edit_id;
}

$cas = null;
if ($akcija === 'izmeni' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM casovi WHERE id = ?');
    $stmt->execute([$id]);
    $cas = $stmt->fetch();
    if (!$cas) { postaviti_flash('greska', 'Trening nije pronađen.'); header('Location: ' . BASE_URL . '/admin/treninzi.php'); exit(); }
}

$casovi  = $pdo->query('SELECT c.*, CONCAT(t.ime, " ", t.prezime) AS trener_ime FROM casovi c LEFT JOIN treneri t ON c.trener_id = t.id ORDER BY FIELD(c.dan_u_nedelji,"Ponedeljak","Utorak","Sreda","Četvrtak","Petak","Subota","Nedelja"), c.vreme')->fetchAll();
$treneri = $pdo->query('SELECT id, CONCAT(ime, " ", prezime) AS ime FROM treneri ORDER BY ime')->fetchAll();
$dani    = ['Ponedeljak','Utorak','Sreda','Četvrtak','Petak','Subota','Nedelja'];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treninzi – Admin | Forma Fitness</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1><?= in_array($akcija, ['novi','izmeni']) ? ($akcija === 'novi' ? 'Novi trening' : 'Izmeni trening') : '<i class="fa-solid fa-calendar-days si"></i> Treninzi' ?></h1>
        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?><div class="flash-poruka flash-greska"><?= e($g) ?></div><?php endforeach; ?>

        <?php if (in_array($akcija, ['novi', 'izmeni'])): ?>
            <a href="<?= BASE_URL ?>/admin/treninzi.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>
            <div class="p-box" style="max-width:500px;">
                <form method="POST">
                    <input type="hidden" name="edit_id" value="<?= $cas['id'] ?? 0 ?>">
                    <div class="forma-group">
                        <label>Naziv treninga *</label>
                        <input type="text" name="naziv" value="<?= e($cas['naziv'] ?? '') ?>" required>
                    </div>
                    <div class="forma-group">
                        <label>Trener</label>
                        <select name="trener_id">
                            <option value="0">-- Bez trenera --</option>
                            <?php foreach ($treneri as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($cas['trener_id'] ?? 0) == $t['id'] ? 'selected' : '' ?>>
                                    <?= e($t['ime']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="forma-group">
                        <label>Opis</label>
                        <textarea name="opis" rows="4"><?= e($cas['opis'] ?? '') ?></textarea>
                    </div>
                    <div class="grid-2" style="gap:12px;">
                        <div class="forma-group">
                            <label>Dan u nedelji *</label>
                            <select name="dan_u_nedelji" required>
                                <option value="">-- Izaberi --</option>
                                <?php foreach ($dani as $d): ?>
                                    <option value="<?= $d ?>" <?= ($cas['dan_u_nedelji'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="forma-group">
                            <label>Vreme *</label>
                            <input type="time" name="vreme" value="<?= e(substr($cas['vreme'] ?? '', 0, 5)) ?>" required>
                        </div>
                    </div>
                    <div class="forma-group">
                        <label>Kapacitet (mesta)</label>
                        <input type="number" name="kapacitet" value="<?= e($cas['kapacitet'] ?? 20) ?>" min="1" max="100">
                    </div>
                    <div class="forma-group" style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="aktivan" id="aktivan" value="1" <?= ($cas['aktivan'] ?? 1) ? 'checked' : '' ?>>
                        <label for="aktivan" style="margin:0;">Aktivan trening</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $cas ? 'Sačuvaj' : 'Dodaj trening' ?></button>
                </form>
            </div>
        <?php else: ?>
            <div style="margin-bottom:16px;">
                <a href="?akcija=novi" class="btn btn-primary btn-sm">+ Novi trening</a>
            </div>
            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Naziv</th><th>Trener</th><th>Dan</th><th>Vreme</th><th>Kapacitet</th><th>Status</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php if (empty($casovi)): ?>
                        <tr><td colspan="7" style="text-align:center;color:var(--text2);padding:30px;">Nema treninga</td></tr>
                    <?php else: ?>
                        <?php foreach ($casovi as $c): ?>
                        <tr>
                            <td><strong><?= e($c['naziv']) ?></strong></td>
                            <td><?= e($c['trener_ime'] ?: '-') ?></td>
                            <td><?= e($c['dan_u_nedelji']) ?></td>
                            <td><?= substr($c['vreme'], 0, 5) ?>h</td>
<td><?= $c['kapacitet'] ?> mesta</td>
                            <td><?= $c['aktivan'] ? '<span class="badge badge-lako">Aktivan</span>' : '<span class="badge" style="background:var(--bg3);color:var(--text3);">Neaktivan</span>' ?></td>
                            <td style="white-space:nowrap;">
                                <a href="?akcija=izmeni&id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Izmeni</a>
                                <a href="?akcija=obrisi&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Obrisati trening?')">Obriši</a>
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
