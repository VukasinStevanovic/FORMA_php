<?php
require_once 'konekcija.php';
require_once 'functions.php';

$naslov_stranice = 'O autoru';
require_once 'includes/header.php';

// Proveriti koji format slike postoji
$slika_path = null;
foreach (['autor.jpg', 'autor.png', 'autor.jpeg', 'autor.webp'] as $ext) {
    if (file_exists(__DIR__ . '/uploads/' . $ext)) {
        $slika_path = BASE_URL . '/uploads/' . $ext;
        break;
    }
}
?>

<div class="main-content">

    <div class="page-naslov">
        <h1>O <span>autoru</span></h1>
    </div>

    <div style="max-width:700px;margin:0 auto;">

        <div class="p-box" style="display:flex;gap:30px;align-items:flex-start;flex-wrap:wrap;margin-bottom:30px;">
            <div style="flex-shrink:0;">
                <?php if ($slika_path): ?>
                    <img src="<?= $slika_path ?>" alt="Vukašin Stevanović"
                         style="width:160px;height:160px;border-radius:50%;object-fit:cover;border:3px solid var(--orange);display:block;">
                <?php else: ?>
                    <div style="width:160px;height:160px;background:var(--bg3);border:3px solid var(--orange);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:64px;">
                        👤
                    </div>
                <?php endif; ?>
            </div>

            <div style="flex:1;min-width:220px;">
                <h2 style="font-size:24px;margin-bottom:6px;">Vukašin Stevanović</h2>
                <p style="color:var(--orange);font-weight:600;margin-bottom:16px;">Student – Visoka ICT škola, Beograd</p>

                <p style="color:var(--text2);line-height:1.8;">
                    Student Visoke ICT škole Beogradu, IT smer, 2. godina. Imam veliku strast prema
                    teretani i fitnesu, a pored studija radim kao trener u teretani
                    <strong>Forma Fitness</strong> u Pančevu. Ovaj sajt je nastao kao projektni zadatak
                    iz predmeta web programiranje PHP 1 i spaja obe strasti — programiranje i teretanu.
                </p>
            </div>
        </div>

        <div class="p-box" style="margin-bottom:20px;">
            <h3 style="margin-bottom:12px;color:var(--orange);">O projektu</h3>
            <p style="color:var(--text2);line-height:1.8;">
                <strong>Forma Fitness</strong> je moj web projekat koji prikazuje sajt fites kluba
                sa bazom vežbi, rasporedom časova, sistemom anketa i kontakt formom.
                Sajt je radjen u proceduralnom PHP-u, bez framework-a, sa MySQL bazom podataka
                i kompletnim admin panelom za upravljanje svim sadržajem. Planiram da vremenom ovaj projekat
                nadogradim i sredim da se može koristiti kao oficijalni sajt fitness kluba <strong>FORMA</strong>;
            </p>
        </div>

    </div>

</div>

<style>
@media (max-width: 600px) {
    .p-box > div[style*="display:flex"] { flex-direction: column !important; align-items: center !important; text-align: center !important; }
}
</style>

<?php require_once 'includes/footer.php'; ?>
