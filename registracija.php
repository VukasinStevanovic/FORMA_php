<?php
require_once 'konekcija.php';
require_once 'functions.php';

if (je_li_ulogovan()) {
    preusmeriti(BASE_URL . '/index.php');
}

$greske  = [];
$podaci  = ['ime' => '', 'prezime' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime      = trim($_POST['ime']      ?? '');
    $prezime  = trim($_POST['prezime']  ?? '');
    $email    = trim($_POST['email']    ?? '');
    $lozinka  = $_POST['lozinka']       ?? '';
    $lozinka2 = $_POST['lozinka2']      ?? '';

    $podaci = compact('ime', 'prezime', 'email');

    if (empty($ime) || strlen($ime) < 2) {
        $greske['ime'] = 'Ime mora imati najmanje 2 karaktera.';
    }
    if (empty($prezime) || strlen($prezime) < 2) {
        $greske['prezime'] = 'Prezime mora imati najmanje 2 karaktera.';
    }
    if (!validan_email($email)) {
        $greske['email'] = 'Unesite ispravnu email adresu.';
    }
    if (!validna_lozinka($lozinka)) {
        $greske['lozinka'] = 'Lozinka mora imati najmanje 8 karaktera, jedno veliko slovo i jedan broj.';
    }
    if ($lozinka !== $lozinka2) {
        $greske['lozinka2'] = 'Lozinke se ne poklapaju!';
    }

    if (empty($greske['email'])) {
        $stmt = $pdo->prepare('SELECT id FROM korisnici WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $greske['email'] = 'Ova email adresa je već registrovana.';
        }
    }

    if (empty($greske)) {
        $token = generisati_token();
        $hash  = password_hash($lozinka, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('
            INSERT INTO korisnici (ime, prezime, email, lozinka, uloga, aktivan, aktivacioni_token)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$ime, $prezime, $email, $hash, 'korisnik', 0, $token]);

        $aktivacioni_link = 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/aktivacija.php?token=' . $token;
        $email_telo = email_aktivacija($ime, $aktivacioni_link);

        $email_poslat = slati_email($email, 'Aktivacija Forma Fitness naloga', $email_telo);

        if ($email_poslat) {
            postaviti_flash('uspeh', 'Registracija uspešna! Proverite email i kliknite na aktivacioni link.');
        } else {
            $pdo->prepare('UPDATE korisnici SET aktivan = 1, aktivacioni_token = NULL WHERE email = ?')
                ->execute([$email]);
            postaviti_flash('info', 'Registracija uspešna! Nalog je automatski aktiviran (email servis nije konfigurisan).');
        }

        preusmeriti(BASE_URL . '/login.php');
    }
}

$naslov_stranice = 'Registracija';
require_once 'includes/header.php';
?>

<div class="main-content">
    <div class="forma-wrap" style="max-width:560px;margin-top:20px;">
        <h2 style="text-align:center;margin-bottom:24px;"><i class="fa-solid fa-user-plus" style="color:var(--orange);"></i> Registracija</h2>

        <form method="POST" action="" id="reg-forma" novalidate>
            <div class="grid-2" style="gap:12px;">
                <div class="forma-group">
                    <label for="ime">Ime *</label>
                    <input type="text" id="ime" name="ime"
                           value="<?= e($podaci['ime']) ?>"
                           placeholder="Npr. Marko" required>
                    <?php if (!empty($greske['ime'])): ?>
                        <span class="greska-polje"><?= e($greske['ime']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="forma-group">
                    <label for="prezime">Prezime *</label>
                    <input type="text" id="prezime" name="prezime"
                           value="<?= e($podaci['prezime']) ?>"
                           placeholder="Npr. Petrović" required>
                    <?php if (!empty($greske['prezime'])): ?>
                        <span class="greska-polje"><?= e($greske['prezime']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="forma-group">
                <label for="email">Email adresa *</label>
                <input type="email" id="email" name="email"
                       value="<?= e($podaci['email']) ?>"
                       placeholder="vas@email.com" required>
                <?php if (!empty($greske['email'])): ?>
                    <span class="greska-polje"><?= e($greske['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="forma-group">
                <label for="lozinka">Lozinka *</label>
                <div style="position:relative;">
                    <input type="password" id="lozinka" name="lozinka"
                           placeholder="Min. 8 karaktera, 1 veliko slovo, 1 broj" required style="padding-right:44px;">
                    <button type="button" onclick="toggleLozinka('lozinka', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text2);font-size:16px;padding:4px;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($greske['lozinka'])): ?>
                    <span class="greska-polje"><?= e($greske['lozinka']) ?></span>
                <?php endif; ?>
                <div id="lozinka-jac" style="height:4px;border-radius:2px;margin-top:6px;background:var(--border);transition:background 0.3s;"></div>
            </div>

            <div class="forma-group">
                <label for="lozinka2">Potvrda lozinke *</label>
                <div style="position:relative;">
                    <input type="password" id="lozinka2" name="lozinka2"
                           placeholder="Unesite lozinku ponovo" required style="padding-right:44px;">
                    <button type="button" onclick="toggleLozinka('lozinka2', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text2);font-size:18px;padding:4px;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($greske['lozinka2'])): ?>
                    <span class="greska-polje"><?= e($greske['lozinka2']) ?></span>
                <?php endif; ?>
                <span id="poklapanje" style="font-size:12px;margin-top:4px;display:block;"></span>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">
                    Kreiraj nalog
                </button>
            </div>
        </form>

        <p style="text-align:center;margin-top:20px;color:var(--text2);font-size:14px;">
            Već imate nalog?
            <a href="<?= BASE_URL ?>/login.php">Prijavite se</a>
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

const lozinkaInput = document.getElementById('lozinka');
const lozinka2Input = document.getElementById('lozinka2');
const jacBar = document.getElementById('lozinka-jac');
const poklapanjeEl = document.getElementById('poklapanje');

lozinkaInput.addEventListener('input', function() {
    const val = this.value;
    let jac = 0;
    if (val.length >= 8) jac++;
    if (/[A-Z]/.test(val)) jac++;
    if (/[0-9]/.test(val)) jac++;
    if (/[^A-Za-z0-9]/.test(val)) jac++;

    const boje = ['#e74c3c', '#f39c12', '#f1c40f', '#27ae60'];
    jacBar.style.background = jac > 0 ? boje[jac - 1] : 'var(--border)';
    jacBar.style.width = (jac * 25) + '%';
});

lozinka2Input.addEventListener('input', function() {
    if (this.value === '') {
        poklapanjeEl.textContent = '';
        return;
    }
    if (this.value === lozinkaInput.value) {
        poklapanjeEl.textContent = '✓ Lozinke se poklapaju';
        poklapanjeEl.style.color = '#27ae60';
    } else {
        poklapanjeEl.textContent = '✗ Lozinke se ne poklapaju';
        poklapanjeEl.style.color = '#e74c3c';
    }
});

document.getElementById('reg-forma').addEventListener('submit', function(e) {
    const ime = document.getElementById('ime').value.trim();
    const prezime = document.getElementById('prezime').value.trim();
    const email = document.getElementById('email').value.trim();
    const loz = lozinkaInput.value;
    const loz2 = lozinka2Input.value;

    if (!ime || !prezime || !email || !loz || !loz2) {
        alert('Sva polja su obavezna!');
        e.preventDefault();
        return;
    }

    if (loz !== loz2) {
        alert('Lozinke se ne poklapaju!');
        e.preventDefault();
        return;
    }

    if (loz.length < 8 || !/[A-Z]/.test(loz) || !/[0-9]/.test(loz)) {
        alert('Lozinka mora imati min. 8 karaktera, jedno veliko slovo i jedan broj.');
        e.preventDefault();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
