<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

$br_vezbi     = $pdo->query('SELECT COUNT(*) FROM vezbe')->fetchColumn();
$br_korisnika = $pdo->query('SELECT COUNT(*) FROM korisnici')->fetchColumn();
$br_poruka    = $pdo->query('SELECT COUNT(*) FROM kontakt_poruke WHERE procitano = 0')->fetchColumn();
$br_anketa    = $pdo->query('SELECT COUNT(*) FROM ankete WHERE aktivan = 1')->fetchColumn();
$br_trenera   = $pdo->query('SELECT COUNT(*) FROM treneri')->fetchColumn();
$br_casova    = $pdo->query('SELECT COUNT(*) FROM casovi WHERE aktivan = 1')->fetchColumn();

$poruke = $pdo->query('SELECT ime, email, naslov, datum, procitano FROM kontakt_poruke ORDER BY datum DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Forma Fitness</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
<div class="admin-layout">

    <?php include 'partials/sidebar.php'; ?>

    <main class="admin-main">
        <h1>📊 Dashboard</h1>

        <?= prikazati_flash() ?>

        <div class="grid-4" style="margin-bottom:30px;">
            <div class="stat-box">
                <span class="broj"><?= $br_vezbi ?></span>
                <p>Vežbi u bazi</p>
            </div>
            <div class="stat-box">
                <span class="broj"><?= $br_korisnika ?></span>
                <p>Korisnika</p>
            </div>
            <div class="stat-box">
                <span class="broj" style="<?= $br_poruka > 0 ? 'color:#e74c3c' : '' ?>"><?= $br_poruka ?></span>
                <p>Nepročitanih poruka</p>
            </div>
            <div class="stat-box">
                <span class="broj"><?= $br_anketa ?></span>
                <p>Aktivnih anketa</p>
            </div>
        </div>

        <div class="grid-2" style="margin-bottom:30px;">
            <div class="stat-box">
                <span class="broj"><?= $br_trenera ?></span>
                <p>Trenera</p>
            </div>
            <div class="stat-box">
                <span class="broj"><?= $br_casova ?></span>
                <p>Aktivnih treninga</p>
            </div>
        </div>

        <div class="p-box">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3>Poslednje kontakt poruke</h3>
                <a href="<?= BASE_URL ?>/admin/kontakt.php" class="btn btn-outline btn-sm">Sve poruke</a>
            </div>

            <?php if (empty($poruke)): ?>
                <p style="color:var(--text2);">Nema poruka.</p>
            <?php else: ?>
                <div class="tabela-wrap">
                    <table>
                        <thead>
                            <tr><th>Od</th><th>Naslov</th><th>Datum</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($poruke as $p): ?>
                            <tr>
                                <td><?= e($p['ime']) ?><br><small style="color:var(--text3);"><?= e($p['email']) ?></small></td>
                                <td><?= e(skratiti_tekst($p['naslov'], 50)) ?></td>
                                <td><?= formatirati_datum($p['datum'], 'd.m.Y. H:i') ?></td>
                                <td>
                                    <?php if (!$p['procitano']): ?>
                                        <span class="badge" style="background:rgba(231,76,60,0.2);color:#e74c3c;">Novo</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:var(--bg3);color:var(--text3);">Pročitano</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-box mt-3">
            <h3 style="margin-bottom:16px;">Brze akcije</h3>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>/admin/vezbe.php?akcija=nova" class="btn btn-primary btn-sm">+ Dodaj vežbu</a>
                <a href="<?= BASE_URL ?>/admin/treninzi.php?akcija=novi" class="btn btn-primary btn-sm">+ Dodaj trening</a>
                <a href="<?= BASE_URL ?>/admin/treneri.php?akcija=novi" class="btn btn-primary btn-sm">+ Dodaj trenera</a>
                <a href="<?= BASE_URL ?>/admin/ankete.php?akcija=nova" class="btn btn-primary btn-sm">+ Nova anketa</a>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline btn-sm" target="_blank">Pogledaj sajt ↗</a>
            </div>
        </div>

    </main>
</div>
</body>
</html>
