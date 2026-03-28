<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'obrisi' && $id > 0) {
    $pdo->prepare('DELETE FROM clanarine WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Članarina je obrisana.');
    header('Location: ' . BASE_URL . '/admin/clanarine.php'); exit();
}

if ($akcija === 'toggle' && $id > 0) {
    $pdo->prepare('UPDATE clanarine SET aktivan = NOT aktivan WHERE id = ?')->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/clanarine.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv         = trim($_POST['naziv']          ?? '');
    $cena          = (float)str_replace(',', '.', $_POST['cena'] ?? '0');
    $trajanje_dana = (int)($_POST['trajanje_dana'] ?? 30);
    $opis          = trim($_POST['opis']           ?? '');
    $aktivan       = isset($_POST['aktivan'])       ? 1 : 0;
    $edit_id       = (int)($_POST['edit_id']        ?? 0);

    if (strlen($naziv) < 2) $greske[] = 'Naziv je obavezan.';
    if ($cena <= 0)          $greske[] = 'Cena mora biti veća od 0.';
    if ($trajanje_dana <= 0) $greske[] = 'Trajanje mora biti veće od 0.';

    if (empty($greske)) {
        if ($edit_id > 0) {
            $pdo->prepare('UPDATE clanarine SET naziv=?, cena=?, trajanje_dana=?, opis=?, aktivan=? WHERE id=?')
                ->execute([$naziv, $cena, $trajanje_dana, $opis, $aktivan, $edit_id]);
            postaviti_flash('uspeh', 'Članarina je ažurirana.');
        } else {
            $pdo->prepare('INSERT INTO clanarine (naziv, cena, trajanje_dana, opis, aktivan) VALUES (?,?,?,?,?)')
                ->execute([$naziv, $cena, $trajanje_dana, $opis, $aktivan]);
            postaviti_flash('uspeh', 'Članarina je dodana.');
        }
        header('Location: ' . BASE_URL . '/admin/clanarine.php'); exit();
    }
    $akcija = $edit_id > 0 ? 'izmeni' : 'nova';
    $id = $edit_id;
}

$clanarina = null;
if ($akcija === 'izmeni' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM clanarine WHERE id = ?');
    $stmt->execute([$id]);
    $clanarina = $stmt->fetch();
    if (!$clanarina) { header('Location: ' . BASE_URL . '/admin/clanarine.php'); exit(); }
}

$clanarine = $pdo->query('SELECT * FROM clanarine ORDER BY cena ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Članarine – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1><?= in_array($akcija,['nova','izmeni']) ? ($akcija==='nova' ? 'Nova članarina' : 'Izmeni članarinu') : '💳 Članarine' ?></h1>
        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?><div class="flash-poruka flash-greska"><?= e($g) ?></div><?php endforeach; ?>

        <?php if (in_array($akcija,['nova','izmeni'])): ?>
            <a href="<?= BASE_URL ?>/admin/clanarine.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>
            <div class="p-box" style="max-width:500px;">
                <form method="POST">
                    <input type="hidden" name="edit_id" value="<?= $clanarina['id'] ?? 0 ?>">
                    <div class="forma-group">
                        <label>Naziv *</label>
                        <input type="text" name="naziv" value="<?= e($clanarina['naziv'] ?? '') ?>" required>
                    </div>
                    <div class="grid-2" style="gap:12px;">
                        <div class="forma-group">
                            <label>Cena (RSD) *</label>
                            <input type="number" name="cena" value="<?= e($clanarina['cena'] ?? '') ?>" step="0.01" min="0" required>
                        </div>
                        <div class="forma-group">
                            <label>Trajanje (dana) *</label>
                            <input type="number" name="trajanje_dana" value="<?= e($clanarina['trajanje_dana'] ?? 30) ?>" min="1" required>
                        </div>
                    </div>
                    <div class="forma-group">
                        <label>Opis</label>
                        <textarea name="opis" rows="4"><?= e($clanarina['opis'] ?? '') ?></textarea>
                    </div>
                    <div class="forma-group" style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="aktivan" id="aktivan" <?= ($clanarina['aktivan'] ?? 1) ? 'checked' : '' ?>>
                        <label for="aktivan" style="margin:0;">Aktivna</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $clanarina ? 'Sačuvaj' : 'Dodaj' ?></button>
                </form>
            </div>
        <?php else: ?>
            <div style="margin-bottom:16px;"><a href="?akcija=nova" class="btn btn-primary btn-sm">+ Nova članarina</a></div>
            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Naziv</th><th>Cena</th><th>Trajanje</th><th>Status</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php if (empty($clanarine)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text2);padding:30px;">Nema članarina</td></tr>
                    <?php else: ?>
                        <?php foreach ($clanarine as $c): ?>
                        <tr>
                            <td><strong><?= e($c['naziv']) ?></strong></td>
                            <td><?= number_format($c['cena'], 0, ',', '.') ?> RSD</td>
                            <td><?= $c['trajanje_dana'] ?> dana</td>
                            <td>
                                <a href="?akcija=toggle&id=<?= $c['id'] ?>" style="text-decoration:none;">
                                    <?= $c['aktivan'] ? '<span class="badge badge-lako">Aktivna</span>' : '<span class="badge" style="background:var(--bg3);color:var(--text3);">Neaktivna</span>' ?>
                                </a>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="?akcija=izmeni&id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Izmeni</a>
                                <a href="?akcija=obrisi&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Obrisati?')">Obriši</a>
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
