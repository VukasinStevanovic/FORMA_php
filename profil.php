<?php
require_once 'konekcija.php';
require_once 'functions.php';
require_once 'includes/auth_check.php';

zahtevaj_login();

$korisnik_id = $_SESSION['korisnik_id'];
$greske = [];
$uspeh = false;

$stmt = $pdo->prepare('SELECT id, ime, prezime, email, uloga, datum_registracije FROM korisnici WHERE id = ?');
$stmt->execute([$korisnik_id]);
$korisnik = $stmt->fetch();

if (!$korisnik) {
    preusmeriti(BASE_URL . '/logout.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['azuriraj'])) {
    $novo_ime     = trim($_POST['ime']     ?? '');
    $novo_prezime = trim($_POST['prezime'] ?? '');
    $nova_lozinka = $_POST['nova_lozinka'] ?? '';
    $stara_lozinka = $_POST['stara_lozinka'] ?? '';

    if (strlen($novo_ime) < 2) {
        $greske[] = 'Ime mora imati najmanje 2 karaktera.';
    }
    if (strlen($novo_prezime) < 2) {
        $greske[] = 'Prezime mora imati najmanje 2 karaktera.';
    }

    if (!empty($nova_lozinka)) {
        if (!password_verify($stara_lozinka, $korisnik['lozinka'] ?? '')) {
            $stmt2 = $pdo->prepare('SELECT lozinka FROM korisnici WHERE id = ?');
            $stmt2->execute([$korisnik_id]);
            $loz_red = $stmt2->fetch();
            if (!password_verify($stara_lozinka, $loz_red['lozinka'])) {
                $greske[] = 'Stara lozinka nije ispravna.';
            }
        }
        if (!validna_lozinka($nova_lozinka)) {
            $greske[] = 'Nova lozinka mora imati min. 8 karaktera, jedno veliko slovo i jedan broj.';
        }
    }

    if (empty($greske)) {
        if (!empty($nova_lozinka)) {
            $novi_hash = password_hash($nova_lozinka, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE korisnici SET ime = ?, prezime = ?, lozinka = ? WHERE id = ?');
            $stmt->execute([$novo_ime, $novo_prezime, $novi_hash, $korisnik_id]);
        } else {
            $stmt = $pdo->prepare('UPDATE korisnici SET ime = ?, prezime = ? WHERE id = ?');
            $stmt->execute([$novo_ime, $novo_prezime, $korisnik_id]);
        }

        $_SESSION['korisnik_ime'] = $novo_ime;
        $korisnik['ime'] = $novo_ime;
        $korisnik['prezime'] = $novo_prezime;

        $uspeh = true;
        postaviti_flash('uspeh', 'Profil je uspešno ažuriran!');
        preusmeriti(BASE_URL . '/profil.php');
    }
}

$naslov_stranice = 'Moj profil';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="profil-header">
        <div class="profil-avatar"><i class="fa-solid fa-circle-user" style="font-size:64px;color:var(--orange);"></i></div>
        <div>
            <h1><?= e($korisnik['ime'] . ' ' . $korisnik['prezime']) ?></h1>
            <p style="color:var(--text2);"><?= e($korisnik['email']) ?></p>
            <p style="margin-top:6px;">
                <span class="badge badge-orange"><?= $korisnik['uloga'] === 'admin' ? 'Administrator' : 'Korisnik' ?></span>
                &nbsp; Član od: <span style="color:var(--text2);"><?= formatirati_datum($korisnik['datum_registracije'], 'd.m.Y.') ?></span>
            </p>
        </div>
    </div>

    <?php if (!empty($greske)): ?>
        <?php foreach ($greske as $g): ?>
            <div class="flash-poruka flash-greska"><?= e($g) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="p-box" style="max-width:500px;">
        <h3 style="margin-bottom:20px;">Izmeni podatke</h3>

        <form method="POST" action="" novalidate>
            <div class="grid-2" style="gap:12px;">
                <div class="forma-group">
                    <label for="ime">Ime</label>
                    <input type="text" id="ime" name="ime" value="<?= e($korisnik['ime']) ?>" required>
                </div>
                <div class="forma-group">
                    <label for="prezime">Prezime</label>
                    <input type="text" id="prezime" name="prezime" value="<?= e($korisnik['prezime']) ?>" required>
                </div>
            </div>

            <div class="forma-group">
                <label>Email adresa</label>
                <input type="email" value="<?= e($korisnik['email']) ?>" disabled
                       style="opacity:0.5;cursor:not-allowed;">
                <small style="color:var(--text3);">Email adresu nije moguće menjati.</small>
            </div>

            <hr style="border:1px solid var(--border);margin:20px 0;">
            <p style="color:var(--text2);font-size:14px;margin-bottom:16px;">Promena lozinke (opcionalno)</p>

            <div class="forma-group">
                <label for="stara_lozinka">Stara lozinka</label>
                <input type="password" id="stara_lozinka" name="stara_lozinka" placeholder="Unesite trenutnu lozinku">
            </div>

            <div class="forma-group">
                <label for="nova_lozinka">Nova lozinka</label>
                <input type="password" id="nova_lozinka" name="nova_lozinka"
                       placeholder="Min. 8 karaktera, 1 veliko, 1 broj">
            </div>

            <button type="submit" name="azuriraj" class="btn btn-primary" style="width:100%;padding:12px;margin-top:8px;">
                Sačuvaj izmene
            </button>
        </form>
    </div>

    <?php if (je_li_admin()): ?>
    <div class="p-box mt-3" style="max-width:500px;">
        <h3 style="margin-bottom:12px;">Admin panel</h3>
        <p style="color:var(--text2);font-size:14px;margin-bottom:16px;">Kao administrator imate pristup upravljačkom panelu.</p>
        <a href="<?= BASE_URL ?>/admin/" class="btn btn-primary">Otvori Admin Panel</a>
    </div>
    <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>
