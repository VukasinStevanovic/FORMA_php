<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

if (isset($_GET['procitano'])) {
    $pid = (int)$_GET['procitano'];
    $pdo->prepare('UPDATE kontakt_poruke SET procitano = 1 WHERE id = ?')->execute([$pid]);
    header('Location: ' . BASE_URL . '/admin/kontakt.php?id=' . $pid); exit();
}

if (isset($_GET['obrisi'])) {
    $did = (int)$_GET['obrisi'];
    $pdo->prepare('DELETE FROM kontakt_poruke WHERE id = ?')->execute([$did]);
    postaviti_flash('uspeh', 'Poruka je obrisana.');
    header('Location: ' . BASE_URL . '/admin/kontakt.php'); exit();
}

$detalj = null;
if (isset($_GET['id'])) {
    $sid = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM kontakt_poruke WHERE id = ?');
    $stmt->execute([$sid]);
    $detalj = $stmt->fetch();

    if ($detalj && !$detalj['procitano']) {
        $pdo->prepare('UPDATE kontakt_poruke SET procitano = 1 WHERE id = ?')->execute([$sid]);
        $detalj['procitano'] = 1;
    }
}

$poruke = $pdo->query('SELECT id, ime, email, naslov, datum, procitano FROM kontakt_poruke ORDER BY datum DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Kontakt poruke – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1>✉️ Kontakt poruke</h1>
        <?= prikazati_flash() ?>

        <?php if ($detalj): ?>
            <a href="<?= BASE_URL ?>/admin/kontakt.php" class="btn btn-outline btn-sm" style="margin-bottom:20px;display:inline-block;">← Nazad</a>

            <div class="p-box" style="max-width:700px;">
                <div style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border);">
                    <h3 style="margin-bottom:6px;"><?= e($detalj['naslov']) ?></h3>
                    <p style="color:var(--text2);font-size:14px;">
                        Od: <strong><?= e($detalj['ime']) ?></strong>
                        &lt;<a href="mailto:<?= e($detalj['email']) ?>"><?= e($detalj['email']) ?></a>&gt;
                        &bull; <?= formatirati_datum($detalj['datum']) ?>
                    </p>
                </div>
                <p style="color:var(--text);line-height:1.8;white-space:pre-line;"><?= e($detalj['poruka']) ?></p>

                <div style="margin-top:20px;display:flex;gap:10px;">
                    <a href="mailto:<?= e($detalj['email']) ?>?subject=Re: <?= e($detalj['naslov']) ?>" class="btn btn-primary btn-sm">Odgovori emailom</a>
                    <a href="?obrisi=<?= $detalj['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Obrisati poruku?')">Obriši</a>
                </div>
            </div>

        <?php else: ?>
            <?php
            $neprocitano = count(array_filter($poruke, fn($p) => !$p['procitano']));
            if ($neprocitano > 0): ?>
                <div class="flash-poruka flash-info">
                    Imate <strong><?= $neprocitano ?></strong> nepročitanu/ih poruku/a.
                </div>
            <?php endif; ?>

            <div class="tabela-wrap p-box" style="padding:0;">
                <table>
                    <thead><tr><th>Od</th><th>Naslov</th><th>Datum</th><th>Status</th><th>Akcije</th></tr></thead>
                    <tbody>
                    <?php if (empty($poruke)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text2);padding:30px;">Nema poruka</td></tr>
                    <?php else: ?>
                        <?php foreach ($poruke as $p): ?>
                        <tr <?= !$p['procitano'] ? 'style="background:rgba(255,102,0,0.04);"' : '' ?>>
                            <td>
                                <strong><?= e($p['ime']) ?></strong><br>
                                <small style="color:var(--text3);"><?= e($p['email']) ?></small>
                            </td>
                            <td>
                                <?php if (!$p['procitano']): ?>
                                    <strong style="color:var(--text);"><?= e($p['naslov']) ?></strong>
                                <?php else: ?>
                                    <?= e($p['naslov']) ?>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:13px;color:var(--text2);"><?= formatirati_datum($p['datum'], 'd.m.Y. H:i') ?></td>
                            <td>
                                <?= !$p['procitano']
                                    ? '<span class="badge" style="background:rgba(231,76,60,0.2);color:#e74c3c;">Novo</span>'
                                    : '<span class="badge" style="background:var(--bg3);color:var(--text3);">Pročitano</span>' ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Otvori</a>
                                <a href="?obrisi=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
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
