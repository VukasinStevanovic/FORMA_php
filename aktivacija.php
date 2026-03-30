<?php
require_once 'konekcija.php';
require_once 'functions.php';

$naslov_stranice = 'Aktivacija naloga';
$status = '';

$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    $status = 'nevalidan';
} else {
    $stmt = $pdo->prepare('
        SELECT id, ime, datum_registracije
        FROM korisnici
        WHERE aktivacioni_token = ? AND aktivan = 0
        LIMIT 1
    ');
    $stmt->execute([$token]);
    $korisnik = $stmt->fetch();

    if (!$korisnik) {
        $stmt2 = $pdo->prepare('SELECT id FROM korisnici WHERE aktivacioni_token = ? AND aktivan = 1 LIMIT 1');
        $stmt2->execute([$token]);
        $status = $stmt2->fetch() ? 'vec_aktiviran' : 'nevalidan';
    } else {
        $datum = strtotime($korisnik['datum_registracije']);
        if (time() - $datum > 86400) {
            $status = 'istekao';
        } else {
            $update = $pdo->prepare('UPDATE korisnici SET aktivan = 1, aktivacioni_token = NULL WHERE id = ?');
            $update->execute([$korisnik['id']]);
            $status = 'uspeh';
            $ime_korisnika = $korisnik['ime'];
        }
    }
}

require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="forma-wrap" style="text-align:center;padding:40px;">

        <?php if ($status === 'uspeh'): ?>
            <div style="font-size:64px;margin-bottom:16px;"><i class="fa-solid fa-circle-check" style="color:#27ae60;"></i></div>
            <h2 style="color:var(--orange);margin-bottom:12px;">Nalog aktiviran!</h2>
            <p style="color:var(--text2);margin-bottom:24px;">
                Dobrodošao/la, <strong><?= e($ime_korisnika) ?></strong>!<br>
                Vaš nalog je uspešno aktiviran. Možete se sada prijaviti.
            </p>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary">Prijavi se</a>

        <?php elseif ($status === 'vec_aktiviran'): ?>
            <div style="font-size:64px;margin-bottom:16px;"><i class="fa-solid fa-circle-info" style="color:var(--orange);"></i></div>
            <h2 style="margin-bottom:12px;">Nalog je već aktivan</h2>
            <p style="color:var(--text2);margin-bottom:24px;">Vaš nalog je već aktiviran. Možete se prijaviti.</p>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-primary">Prijavi se</a>

        <?php elseif ($status === 'istekao'): ?>
            <div style="font-size:64px;margin-bottom:16px;"><i class="fa-solid fa-clock" style="color:#e74c3c;"></i></div>
            <h2 style="color:#e74c3c;margin-bottom:12px;">Link je istekao</h2>
            <p style="color:var(--text2);margin-bottom:24px;">
                Aktivacioni link je validan samo 24 sata.<br>
                Registrujte se ponovo ili kontaktirajte podršku.
            </p>
            <a href="<?= BASE_URL ?>/registracija.php" class="btn btn-outline">Registruj se ponovo</a>

        <?php else: ?>
            <div style="font-size:64px;margin-bottom:16px;"><i class="fa-solid fa-circle-xmark" style="color:#e74c3c;"></i></div>
            <h2 style="color:#e74c3c;margin-bottom:12px;">Nevalidan link</h2>
            <p style="color:var(--text2);margin-bottom:24px;">
                Aktivacioni link nije validan.<br>
                Proverite email ili se ponovo registrujte.
            </p>
            <a href="<?= BASE_URL ?>/registracija.php" class="btn btn-outline">Registracija</a>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
