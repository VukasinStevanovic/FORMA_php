<?php
require_once 'konekcija.php';
require_once 'functions.php';

$naslov_stranice = 'O autoru';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="page-naslov">
        <h1>O <span>autoru</span></h1>
    </div>

    <div style="max-width:800px;margin:0 auto;">

        <div class="p-box" style="display:flex;gap:30px;align-items:flex-start;flex-wrap:wrap;margin-bottom:30px;">
            <div style="flex-shrink:0;">
                <div style="width:160px;height:160px;background:var(--bg3);border:3px solid var(--orange);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:64px;">
                    👨‍🎓
                </div>
            </div>

            <div style="flex:1;min-width:250px;">
                <h2 style="font-size:24px;margin-bottom:6px;">Petar Studentović</h2>
                <p style="color:var(--orange);font-weight:600;margin-bottom:12px;">Student – Elektrotehnički fakultet, Beograd</p>

                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                    <span class="badge badge-orange">PHP</span>
                    <span class="badge badge-orange">MySQL</span>
                    <span class="badge badge-orange">HTML/CSS</span>
                    <span class="badge badge-orange">JavaScript</span>
                </div>

                <p style="color:var(--text2);line-height:1.8;">
                    Student treće godine softverskog inženjerstva. Ovaj sajt je napravljen kao
                    projekat za kurs <strong>Web programiranje</strong>. Tema gym kluba mi se dopala
                    jer sam i sam aktivan u teretani, pa mi je bilo lakše osmisliti sadržaj.
                </p>
            </div>
        </div>

        <div class="p-box" style="margin-bottom:20px;">
            <h3 style="margin-bottom:16px;color:var(--orange);">O projektu</h3>
            <p style="color:var(--text2);line-height:1.8;margin-bottom:12px;">
                <strong>Forma Fitness</strong> je studentski web projekat koji kombinuje informativni sajt gym kluba
                sa bazom vežbi. Cilj projekta je bio primeniti znanja iz kolegijuma Web programiranja u
                realnom scenariju — od projektovanja baze podataka do implementacije autentifikacije i
                admin panela.
            </p>
            <p style="color:var(--text2);line-height:1.8;">
                Sve funkcionalnosti su implementirane od nule, bez upotrebe framework-a, što mi je
                pomoglo da dublje razumem kako web aplikacije zaista funkcionišu "ispod haube".
            </p>
        </div>

        <div class="p-box" style="margin-bottom:20px;">
            <h3 style="margin-bottom:16px;color:var(--orange);">Tehničke specifikacije</h3>
            <table style="width:100%;font-size:14px;">
                <tr>
                    <td style="padding:10px 0;color:var(--text2);border-bottom:1px solid var(--border);width:40%;">Backend</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);"><strong>PHP 8.x (proceduralni)</strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:var(--text2);border-bottom:1px solid var(--border);">Baza podataka</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);"><strong>MySQL (PDO + Prepared Statements)</strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:var(--text2);border-bottom:1px solid var(--border);">Frontend</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);"><strong>HTML5, CSS3 (vanilla), JavaScript (vanilla)</strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:var(--text2);border-bottom:1px solid var(--border);">Email</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);"><strong>PHPMailer (SMTP)</strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:var(--text2);border-bottom:1px solid var(--border);">Razvojno okruženje</td>
                    <td style="padding:10px 0;border-bottom:1px solid var(--border);"><strong>XAMPP (Apache + MySQL)</strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:var(--text2);">Dizajn</td>
                    <td style="padding:10px 0;"><strong>Tamna tema (crna + narandžasta)</strong></td>
                </tr>
            </table>
        </div>

        <div class="p-box" style="margin-bottom:20px;">
            <h3 style="margin-bottom:16px;color:var(--orange);">Implementirane funkcionalnosti</h3>
            <ul style="color:var(--text2);font-size:14px;line-height:2.2;list-style:disc;padding-left:20px;">
                <li>Registracija sa aktivacijom naloga putem emaila</li>
                <li>Prijava i odjava sa session-based autentifikacijom</li>
                <li>Zaštita stranica od neautorizovanog pristupa</li>
                <li>Dinamički meni iz baze podataka</li>
                <li>Baza vežbi sa pretragom i filterima</li>
                <li>Raspored grupnih časova</li>
                <li>Prikaz trenera i planova članarina</li>
                <li>Kontakt forma sa čuvanjem u bazu i slanjem emaila</li>
                <li>Sistem anketa sa zaštitom od višestrukog glasanja</li>
                <li>Admin panel: CRUD za sve entitete</li>
                <li>Upload slika (vežbe i treneri)</li>
                <li>Responzivni dizajn (mobilni uređaji)</li>
            </ul>
        </div>

        <div class="p-box" style="border-left:3px solid var(--orange);">
            <h4 style="margin-bottom:8px;">📌 Napomena</h4>
            <p style="color:var(--text2);font-size:14px;line-height:1.8;">
                Ovo je akademski projekat namenjen za demonstraciju stečenih znanja iz web programiranja.
                Sadržaj (nazivi, podaci, slike) su izmišljeni i korišćeni isključivo za edukativne svrhe.
                Kod je napisan proceduralnim PHP-om bez framework-a, namerno — kao vežba razumevanja osnova.
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
