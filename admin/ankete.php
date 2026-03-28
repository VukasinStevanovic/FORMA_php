<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$akcija = $_GET['akcija'] ?? 'lista';
$id     = (int)($_GET['id'] ?? 0);
$greske = [];

if ($akcija === 'toggle' && $id > 0) {
    $pdo->prepare('UPDATE ankete SET aktivan = NOT aktivan WHERE id = ?')->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/ankete.php'); exit();
}

if ($akcija === 'obrisi' && $id > 0) {
    $pdo->prepare('DELETE FROM ankete WHERE id = ?')->execute([$id]);
    postaviti_flash('uspeh', 'Anketa je obrisana.');
    header('Location: ' . BASE_URL . '/admin/ankete.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_anketa'])) {
    $pitanje  = trim($_POST['pitanje'] ?? '');
    $odgovori = array_filter(array_map('trim', $_POST['odgovori'] ?? []));
    $aktivan  = isset($_POST['aktivan']) ? 1 : 0;

    if (strlen($pitanje) < 5) $greske[] = 'Pitanje je premalo (min. 5 karaktera).';
    if (count($odgovori) < 2) $greske[] = 'Dodajte najmanje 2 odgovora.';

    if (empty($greske)) {
        $pdo->prepare('INSERT INTO ankete (pitanje, aktivan) VALUES (?, ?)')->execute([$pitanje, $aktivan]);
        $anketa_id = $pdo->lastInsertId();

        foreach ($odgovori as $odg) {
            $pdo->prepare('INSERT INTO anketa_odgovori (anketa_id, tekst_odgovora) VALUES (?, ?)')->execute([$anketa_id, $odg]);
        }

        postaviti_flash('uspeh', 'Anketa je kreirana.');
        header('Location: ' . BASE_URL . '/admin/ankete.php'); exit();
    }
    $akcija = 'nova';
}

$ankete = $pdo->query('
    SELECT a.*, (SELECT COUNT(*) FROM anketa_glasovi WHERE anketa_id = a.id) AS ukupno_glasova
    FROM ankete a
    ORDER BY a.datum_kreiranja DESC
')->fetchAll();

$statistika = [];
if ($akcija === 'statistika' && $id > 0) {
    $stmt = $pdo->prepare('SELECT pitanje FROM ankete WHERE id = ?');
    $stmt->execute([$id]);
    $ank = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT tekst_odgovora, broj_glasova FROM anketa_odgovori WHERE anketa_id = ? ORDER BY broj_glasova DESC');
    $stmt->execute([$id]);
    $statistika = $stmt->fetchAll();
    $ukupno_ank = array_sum(array_column($statistika, 'broj_glasova'));
}
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Ankete – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1>📊 Ankete</h1>
        <?= prikazati_flash() ?>
        <?php foreach ($greske as $g): ?><div class="flash-poruka flash-greska"><?= e($g) ?></div><?php endforeach; ?>

        <?php if ($akcija === 'statistika' && $id > 0 && isset($ank)): ?>
            <a href="<?= BASE_URL ?>/admin/ankete.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>

            <div class="p-box" style="max-width:600px;">
                <h3 style="margin-bottom:6px;"><?= e($ank['pitanje']) ?></h3>
                <p style="color:var(--text2);font-size:14px;margin-bottom:20px;">Ukupno glasova: <strong><?= $ukupno_ank ?></strong></p>

                <?php if (empty($statistika)): ?>
                    <p style="color:var(--text2);">Nema glasova.</p>
                <?php else: ?>
                    <?php foreach ($statistika as $o): ?>
                        <?php $proc = $ukupno_ank > 0 ? round(($o['broj_glasova'] / $ukupno_ank) * 100) : 0; ?>
                        <div class="progress-wrap">
                            <div class="progress-label">
                                <strong><?= e($o['tekst_odgovora']) ?></strong>
                                <span><?= $o['broj_glasova'] ?> (<?= $proc ?>%)</span>
                            </div>
                            <div class="progress-bar-outer">
                                <div class="progress-bar-inner" style="width:<?= $proc ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php elseif ($akcija === 'nova'): ?>
            <a href="<?= BASE_URL ?>/admin/ankete.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>

            <div class="p-box" style="max-width:550px;">
                <form method="POST">
                    <input type="hidden" name="nova_anketa" value="1">
                    <div class="forma-group">
                        <label>Pitanje ankete *</label>
                        <input type="text" name="pitanje" value="<?= e($_POST['pitanje'] ?? '') ?>"
                               placeholder="Npr. Koji trening preferirate?" required>
                    </div>

                    <div class="forma-group">
                        <label>Odgovori (min. 2) *</label>
                        <div id="odgovori-wrap">
                            <?php
                            $preth_odg = $_POST['odgovori'] ?? ['', '', ''];
                            foreach ($preth_odg as $i => $o):
                            ?>
                            <div class="odg-red" style="display:flex;gap:8px;margin-bottom:8px;">
                                <input type="text" name="odgovori[]" value="<?= e($o) ?>"
                                       placeholder="Odgovor <?= $i + 1 ?>"
                                       style="flex:1;padding:8px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);">
                                <?php if ($i >= 2): ?>
                                    <button type="button" onclick="this.parentElement.remove();"
                                            style="background:rgba(231,76,60,0.2);border:none;color:#e74c3c;border-radius:var(--radius);padding:0 12px;cursor:pointer;">✕</button>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="dodaj-odg" class="btn btn-outline btn-sm" style="margin-top:6px;">+ Dodaj odgovor</button>
                    </div>

                    <div class="forma-group" style="display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="aktivan" id="aktivan" checked>
                        <label for="aktivan" style="margin:0;">Odmah aktivna</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Kreiraj anketu</button>
                </form>
            </div>

            <script>
            let brojac = <?= count($preth_odg) ?>;
            document.getElementById('dodaj-odg').addEventListener('click', function() {
                brojac++;
                const wrap = document.getElementById('odgovori-wrap');
                const div = document.createElement('div');
                div.className = 'odg-red';
                div.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;';
                div.innerHTML = `<input type="text" name="odgovori[]" placeholder="Odgovor ${brojac}"
                    style="flex:1;padding:8px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);">
                    <button type="button" onclick="this.parentElement.remove();"
                        style="background:rgba(231,76,60,0.2);border:none;color:#e74c3c;border-radius:var(--radius);padding:0 12px;cursor:pointer;">✕</button>`;
                wrap.appendChild(div);
            });
            </script>

        <?php else: ?>
            <div style="margin-bottom:16px;">
                <a href="?akcija=nova" class="btn btn-primary btn-sm">+ Nova anketa</a>
            </div>

            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Pitanje</th><th>Glasova</th><th>Status</th><th>Kreirano</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php if (empty($ankete)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text2);padding:30px;">Nema anketa</td></tr>
                    <?php else: ?>
                        <?php foreach ($ankete as $a): ?>
                        <tr>
                            <td><?= e(skratiti_tekst($a['pitanje'], 70)) ?></td>
                            <td style="font-weight:700;color:var(--orange);"><?= $a['ukupno_glasova'] ?></td>
                            <td>
                                <a href="?akcija=toggle&id=<?= $a['id'] ?>" style="text-decoration:none;">
                                    <?= $a['aktivan'] ? '<span class="badge badge-lako">Aktivna</span>' : '<span class="badge" style="background:var(--bg3);color:var(--text3);">Neaktivna</span>' ?>
                                </a>
                            </td>
                            <td style="font-size:13px;color:var(--text2);"><?= formatirati_datum($a['datum_kreiranja'], 'd.m.Y.') ?></td>
                            <td style="white-space:nowrap;">
                                <a href="?akcija=statistika&id=<?= $a['id'] ?>" class="btn btn-outline btn-sm">Statistika</a>
                                <a href="?akcija=obrisi&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Obrisati anketu i sve glasove?')">Obriši</a>
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
