<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'obrisi' && $id > 0) {
    $pdo->prepare('DELETE FROM meni_stavke WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Stavka menija je obrisana.');
    header('Location: ' . BASE_URL . '/admin/meni.php'); exit();
}

if ($akcija === 'toggle' && $id > 0) {
    $pdo->prepare('UPDATE meni_stavke SET aktivan = NOT aktivan WHERE id = ?')->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/meni.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv    = trim($_POST['naziv']    ?? '');
    $url      = trim($_POST['url']      ?? '');
    $pozicija = (int)($_POST['pozicija'] ?? 0);
    $aktivan  = isset($_POST['aktivan']) ? 1 : 0;
    $edit_id  = (int)($_POST['edit_id'] ?? 0);

    if (strlen($naziv) < 1) $greske[] = 'Naziv je obavezan.';
    if (strlen($url)   < 1) $greske[] = 'URL je obavezan.';

    if (empty($greske)) {
        if ($edit_id > 0) {
            $pdo->prepare('UPDATE meni_stavke SET naziv=?, url=?, pozicija=?, aktivan=? WHERE id=?')
                ->execute([$naziv, $url, $pozicija, $aktivan, $edit_id]);
            postaviti_flash('uspeh', 'Stavka je ažurirana.');
        } else {
            $pdo->prepare('INSERT INTO meni_stavke (naziv, url, pozicija, aktivan) VALUES (?,?,?,?)')
                ->execute([$naziv, $url, $pozicija, $aktivan]);
            postaviti_flash('uspeh', 'Stavka je dodana.');
        }
        header('Location: ' . BASE_URL . '/admin/meni.php'); exit();
    }
    $akcija = $edit_id > 0 ? 'izmeni' : 'nova';
    $id = $edit_id;
}

$stavka = null;
if ($akcija === 'izmeni' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM meni_stavke WHERE id = ?');
    $stmt->execute([$id]);
    $stavka = $stmt->fetch();
}

$stavke = $pdo->query('SELECT * FROM meni_stavke ORDER BY pozicija ASC, id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Meni – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1>☰ Upravljanje menijem</h1>
        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?><div class="flash-poruka flash-greska"><?= e($g) ?></div><?php endforeach; ?>

        <?php if (in_array($akcija,['nova','izmeni'])): ?>
            <a href="<?= BASE_URL ?>/admin/meni.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>
            <div class="p-box" style="max-width:450px;">
                <form method="POST">
                    <input type="hidden" name="edit_id" value="<?= $stavka['id'] ?? 0 ?>">
                    <div class="forma-group">
                        <label>Naziv stavke *</label>
                        <input type="text" name="naziv" value="<?= e($stavka['naziv'] ?? '') ?>" required>
                    </div>
                    <div class="forma-group">
                        <label>URL *</label>
                        <input type="text" name="url" value="<?= e($stavka['url'] ?? '') ?>" placeholder="/forma_fitness/stranica.php" required>
                    </div>
                    <div class="forma-group">
                        <label>Pozicija (redosled)</label>
                        <input type="number" name="pozicija" value="<?= e($stavka['pozicija'] ?? 0) ?>" min="0">
                    </div>
                    <div class="forma-group" style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="aktivan" id="aktivan" <?= ($stavka['aktivan'] ?? 1) ? 'checked' : '' ?>>
                        <label for="aktivan" style="margin:0;">Vidljiva u meniju</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $stavka ? 'Sačuvaj' : 'Dodaj stavku' ?></button>
                </form>
            </div>
        <?php else: ?>
            <div style="margin-bottom:16px;"><a href="?akcija=nova" class="btn btn-primary btn-sm">+ Nova stavka</a></div>
            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Pozicija</th><th>Naziv</th><th>URL</th><th>Status</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php foreach ($stavke as $s): ?>
                    <tr>
                        <td style="text-align:center;font-weight:700;color:var(--orange);"><?= $s['pozicija'] ?></td>
                        <td><strong><?= e($s['naziv']) ?></strong></td>
                        <td style="font-size:13px;color:var(--text2);"><?= e($s['url']) ?></td>
                        <td>
                            <a href="?akcija=toggle&id=<?= $s['id'] ?>" style="text-decoration:none;">
                                <?= $s['aktivan'] ? '<span class="badge badge-lako">Vidljiva</span>' : '<span class="badge" style="background:var(--bg3);color:var(--text3);">Skrivena</span>' ?>
                            </a>
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="?akcija=izmeni&id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Izmeni</a>
                            <a href="?akcija=obrisi&id=<?= $s['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Obrisati stavku?')">Obriši</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
