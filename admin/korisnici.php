<?php
require_once '../konekcija.php';
require_once '../functions.php';
require_once '../includes/auth_check.php';

zahtevaj_admina();

if (isset($_GET['toggle_aktivan'])) {
    $tid = (int)$_GET['toggle_aktivan'];
    if ($tid !== (int)$_SESSION['korisnik_id']) {
        $pdo->prepare('UPDATE korisnici SET aktivan = NOT aktivan WHERE id = ?')->execute([$tid]);
    }
    header('Location: ' . BASE_URL . '/admin/korisnici.php'); exit();
}

$korisnici = $pdo->query('SELECT id, ime, prezime, email, uloga, aktivan, datum_registracije FROM korisnici ORDER BY datum_registracije DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Korisnici – Admin | Forma Fitness</title><link rel="stylesheet" href="<?= BASE_URL ?>/style.css"></head>
<body>
<div class="admin-layout">
    <?php include 'partials/sidebar.php'; ?>
    <main class="admin-main">
        <h1><i class="fa-solid fa-users si"></i> Korisnici</h1>
        <?= prikazati_flash() ?>

        <p style="color:var(--text2);margin-bottom:20px;">Ukupno: <strong><?= count($korisnici) ?></strong> korisnika</p>

        <div class="tabela-wrap p-box" style="padding:0;">
            <table>
                <thead><tr><th>#</th><th>Ime i prezime</th><th>Email</th><th>Uloga</th><th>Aktivan</th><th>Registrovan</th><th>Akcija</th></tr></thead>
                <tbody>
                <?php if (empty($korisnici)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text2);padding:30px;">Nema korisnika</td></tr>
                <?php else: ?>
                    <?php foreach ($korisnici as $k): ?>
                    <tr>
                        <td style="color:var(--text3);"><?= $k['id'] ?></td>
                        <td><strong><?= e($k['ime'] . ' ' . $k['prezime']) ?></strong></td>
                        <td style="font-size:13px;"><?= e($k['email']) ?></td>
                        <td>
                            <?php if ($k['uloga'] === 'admin'): ?>
                                <span class="badge" style="background:rgba(255,102,0,0.2);color:var(--orange);">Admin</span>
                            <?php else: ?>
                                <span class="badge" style="background:var(--bg3);color:var(--text2);">Korisnik</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($k['id'] !== (int)$_SESSION['korisnik_id']): ?>
                                <a href="?toggle_aktivan=<?= $k['id'] ?>" style="text-decoration:none;">
                                    <?= $k['aktivan'] ? '<span class="badge badge-lako">Da</span>' : '<span class="badge badge-tesko">Ne</span>' ?>
                                </a>
                            <?php else: ?>
                                <span class="badge badge-lako">Da (ti)</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:var(--text2);"><?= formatirati_datum($k['datum_registracije'], 'd.m.Y.') ?></td>
                        <td>
                            <?php if ($k['id'] !== (int)$_SESSION['korisnik_id']): ?>
                                <a href="?toggle_aktivan=<?= $k['id'] ?>" class="btn btn-outline btn-sm">
                                    <?= $k['aktivan'] ? 'Deaktiviraj' : 'Aktiviraj' ?>
                                </a>
                            <?php else: ?>
                                <span style="color:var(--text3);font-size:13px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
