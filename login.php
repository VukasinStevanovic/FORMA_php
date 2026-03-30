<?php
require_once 'konekcija.php';
require_once 'functions.php';

if (je_li_ulogovan()) {
    preusmeriti(BASE_URL . '/index.php');
}

$greske = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email']   ?? '');
    $lozinka = trim($_POST['lozinka'] ?? '');

    if (empty($email) || empty($lozinka)) {
        $greske[] = 'Sva polja su obavezna.';
    } else {
        $stmt = $pdo->prepare('SELECT id, ime, prezime, email, lozinka, uloga, aktivan FROM korisnici WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $korisnik = $stmt->fetch();

        if (!$korisnik || !password_verify($lozinka, $korisnik['lozinka'])) {
            $greske[] = 'Pogrešan email ili lozinka!';
        } elseif (!$korisnik['aktivan']) {
            $greske[] = 'Nalog nije aktiviran. Proverite email i kliknite na aktivacioni link.';
        } else {
            $_SESSION['korisnik_id']  = $korisnik['id'];
            $_SESSION['korisnik_ime'] = $korisnik['ime'];
            $_SESSION['uloga']        = $korisnik['uloga'];

            postaviti_flash('uspeh', 'Uspešno ste se prijavili! Dobrodošli, ' . $korisnik['ime'] . '.');

            if ($korisnik['uloga'] === 'admin') {
                preusmeriti(BASE_URL . '/admin/');
            } else {
                preusmeriti(BASE_URL . '/index.php');
            }
        }
    }
}

$naslov_stranice = 'Prijava';
require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="forma-wrap" style="margin-top:20px;">
        <h2 style="text-align:center;margin-bottom:24px;">🔐 Prijava</h2>

        <?php if (!empty($greske)): ?>
            <?php foreach ($greske as $g): ?>
                <div class="flash-poruka flash-greska"><?= e($g) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="forma-group">
                <label for="email">Email adresa</label>
                <input type="email" id="email" name="email"
                       value="<?= e($_POST['email'] ?? '') ?>"
                       placeholder="vas@email.com" required autofocus>
            </div>

            <div class="forma-group">
                <label for="lozinka">Lozinka</label>
                <div style="position:relative;">
                    <input type="password" id="lozinka" name="lozinka"
                           placeholder="••••••••" required style="padding-right:44px;">
                    <button type="button" onclick="toggleLozinka('lozinka', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text2);font-size:16px;padding:4px;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">
                    Prijavi se
                </button>
            </div>
        </form>

        <p style="text-align:center;margin-top:20px;color:var(--text2);font-size:14px;">
            Nemate nalog?
            <a href="<?= BASE_URL ?>/registracija.php">Registrujte se</a>
        </p>
    </div>
</div>

<script>
function toggleLozinka(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-regular fa-eye';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
