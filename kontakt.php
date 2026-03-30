<?php
require_once 'konekcija.php';
require_once 'functions.php';

$greske  = [];
$podaci  = ['ime' => '', 'email' => '', 'naslov' => '', 'poruka' => ''];

if (!empty($_GET['upit'])) {
    $podaci['naslov'] = 'Upit o članarini: ' . $_GET['upit'];
}

if (je_li_ulogovan()) {
    $stmt = $pdo->prepare('SELECT ime, prezime, email FROM korisnici WHERE id = ?');
    $stmt->execute([$_SESSION['korisnik_id']]);
    $kor = $stmt->fetch();
    if ($kor) {
        $podaci['ime']   = $kor['ime'] . ' ' . $kor['prezime'];
        $podaci['email'] = $kor['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime    = trim($_POST['ime']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $naslov = trim($_POST['naslov'] ?? '');
    $poruka = trim($_POST['poruka'] ?? '');

    $podaci = compact('ime', 'email', 'naslov', 'poruka');

    if (strlen($ime) < 2)   $greske['ime']    = 'Unesite vaše ime (min. 2 karaktera).';
    if (!validan_email($email)) $greske['email'] = 'Unesite ispravnu email adresu.';
    if (strlen($naslov) < 3) $greske['naslov'] = 'Naslov mora imati najmanje 3 karaktera.';
    if (strlen($poruka) < 10) $greske['poruka'] = 'Poruka mora imati najmanje 10 karaktera.';

    if (empty($greske)) {
        $stmt = $pdo->prepare('
            INSERT INTO kontakt_poruke (ime, email, naslov, poruka)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$ime, $email, $naslov, $poruka]);

        $email_telo = "
        <div style='font-family:sans-serif;background:#1a1a1a;color:#e0e0e0;padding:24px;border-radius:8px;'>
            <h3 style='color:#ff6600;'>Nova kontakt poruka sa Forma Fitness sajta</h3>
            <p><strong>Od:</strong> {$ime} ({$email})</p>
            <p><strong>Naslov:</strong> {$naslov}</p>
            <hr style='border:1px solid #333;'>
            <p><strong>Poruka:</strong></p>
            <p>" . nl2br(e($poruka)) . "</p>
        </div>";

        slati_email('vukasinstevanovic7@gmail.com', 'Nova poruka: ' . $naslov, $email_telo);

        postaviti_flash('uspeh', 'Poruka je uspešno poslata! Odgovorićemo vam u roku od 24 sata.');
        preusmeriti(BASE_URL . '/kontakt.php');
    }
}

$naslov_stranice = 'Kontakt';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="page-naslov">
        <h1>Kontaktirajte <span>nas</span></h1>
    </div>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:30px;align-items:start;">

        <div class="p-box">
            <h3 style="margin-bottom:20px;">Pošaljite poruku</h3>

            <form method="POST" action="" id="kontakt-forma" novalidate>
                <div class="forma-group">
                    <label for="ime">Ime i prezime *</label>
                    <input type="text" id="ime" name="ime"
                           value="<?= e($podaci['ime']) ?>"
                           placeholder="Vaše ime i prezime" required>
                    <?php if (!empty($greske['ime'])): ?>
                        <span class="greska-polje"><?= e($greske['ime']) ?></span>
                    <?php endif; ?>
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
                    <label for="naslov">Naslov *</label>
                    <input type="text" id="naslov" name="naslov"
                           value="<?= e($podaci['naslov']) ?>"
                           placeholder="O čemu se radi?" required>
                    <?php if (!empty($greske['naslov'])): ?>
                        <span class="greska-polje"><?= e($greske['naslov']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="forma-group">
                    <label for="poruka">Poruka *</label>
                    <textarea id="poruka" name="poruka"
                              placeholder="Vaša poruka..." required
                              rows="6"><?= e($podaci['poruka']) ?></textarea>
                    <?php if (!empty($greske['poruka'])): ?>
                        <span class="greska-polje"><?= e($greske['poruka']) ?></span>
                    <?php endif; ?>
                    <span id="broji-chars" style="font-size:12px;color:var(--text3);display:block;margin-top:4px;">0 karaktera</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;" id="submit-btn">
                    Pošalji poruku
                </button>
            </form>
        </div>

        <div>
            <div class="p-box" style="margin-bottom:16px;">
                <h4 style="margin-bottom:16px;"><i class="fa-solid fa-location-dot" style="color:var(--orange);"></i> Pronađite nas</h4>
                <p style="color:var(--text2);font-size:14px;line-height:1.8;">
                    Vladimira Žestića 29<br>
                    26000 Pančevo, Srbija
                </p>
            </div>

            <div class="p-box" style="margin-bottom:16px;">
                <h4 style="margin-bottom:16px;"><i class="fa-solid fa-phone" style="color:var(--orange);"></i> Kontakt</h4>
                <p style="color:var(--text2);font-size:14px;line-height:2;">
                    <i class="fa-solid fa-mobile-screen" style="color:var(--orange);"></i> <strong>+381 66 5 771171</strong><br>
                    <i class="fa-solid fa-envelope" style="color:var(--orange);"></i> <strong>vukasin.stevanovic.34.24@ict.edu.rs</strong>
                </p>
            </div>

            <div class="p-box" style="margin-bottom:16px;">
                <h4 style="margin-bottom:16px;"><i class="fa-solid fa-clock" style="color:var(--orange);"></i> Radno vreme</h4>
                <table style="width:100%;font-size:14px;">
                    <tr>
                        <td style="padding:4px 0;color:var(--text2);">Pon – Pet</td>
                        <td style="text-align:right;"><strong>09:00 – 23:00</strong></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:var(--text2);">Subota</td>
                        <td style="text-align:right;"><strong>09:00 – 22:00</strong></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 0;color:var(--text2);">Nedelja</td>
                        <td style="text-align:right;"><strong>zatvoreno</strong></td>
                    </tr>
                </table>
            </div>

            <div class="p-box">
                <h4 style="margin-bottom:8px;"><i class="fa-solid fa-hourglass-half" style="color:var(--orange);"></i> Vreme odgovora</h4>
                <p style="color:var(--text2);font-size:13px;">Odgovaramo na sve upite u roku od 24 sata radnim danima.</p>
            </div>
        </div>
    </div>

</div>

<script>
const forma = document.getElementById('kontakt-forma');
const poruka = document.getElementById('poruka');
const brojac = document.getElementById('broji-chars');
const btn = document.getElementById('submit-btn');

poruka.addEventListener('input', function() {
    const n = this.value.length;
    brojac.textContent = n + ' karaktera';
    brojac.style.color = n < 10 ? '#e74c3c' : '#27ae60';
});

forma.addEventListener('submit', function(e) {
    const ime    = document.getElementById('ime').value.trim();
    const email  = document.getElementById('email').value.trim();
    const naslov = document.getElementById('naslov').value.trim();
    const por    = poruka.value.trim();
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    let ok = true;

    if (ime.length < 2) {
        alert('Unesite vaše ime!');
        ok = false;
    } else if (!emailReg.test(email)) {
        alert('Unesite ispravnu email adresu!');
        ok = false;
    } else if (naslov.length < 3) {
        alert('Naslov mora imati najmanje 3 karaktera!');
        ok = false;
    } else if (por.length < 10) {
        alert('Poruka mora imati najmanje 10 karaktera!');
        ok = false;
    }

    if (!ok) {
        e.preventDefault();
        return;
    }

    btn.textContent = 'Slanje...';
    btn.disabled = true;
});
</script>

<style>
@media (max-width: 768px) {
    .main-content > div[style*="grid-template-columns"] {
        display: block !important;
    }
    .main-content > div[style*="grid-template-columns"] > div:last-child {
        margin-top: 20px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
