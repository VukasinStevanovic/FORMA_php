<?php
require_once 'konekcija.php';
require_once 'functions.php';

$stmt = $pdo->query('SELECT * FROM clanarine WHERE aktivan = 1 ORDER BY cena ASC');
$clanarine = $stmt->fetchAll();

$naslov_stranice = 'Članarine';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="sekcija-naslov">
        <h2>Planovi <span>članarine</span></h2>
        <p>Jednostavne i transparentne cene bez skrivenih troškova</p>
    </div>

    <?php if (empty($clanarine)): ?>
        <p style="text-align:center;color:var(--text2);">Trenutno nema dostupnih planova.</p>
    <?php else: ?>
        <div class="grid-3" style="max-width:900px;margin:0 auto 40px;">
            <?php foreach ($clanarine as $i => $c): ?>
            <div class="card clanarina-card <?= $i === 1 ? 'istaknuta' : '' ?>">
                <div class="card-body">
                    <h3 style="font-size:20px;margin-bottom:4px;"><?= e($c['naziv']) ?></h3>
                    <div class="clanarina-cena">
                        <?= number_format($c['cena'], 0, ',', '.') ?>
                        <span> RSD</span>
                    </div>
                    <div class="clanarina-trajanje">
                        <?php
                        if ($c['trajanje_dana'] >= 365) {
                            echo '1 godina';
                        } elseif ($c['trajanje_dana'] >= 90) {
                            echo '3 meseca';
                        } elseif ($c['trajanje_dana'] >= 30) {
                            echo '1 mesec';
                        } else {
                            echo $c['trajanje_dana'] . ' dana';
                        }
                        ?>
                    </div>
                    <p class="clanarina-opis"><?= e($c['opis']) ?></p>
                    <a href="<?= BASE_URL ?>/kontakt.php?upit=<?= urlencode($c['naziv']) ?>"
                       class="btn <?= $i === 1 ? 'btn-primary' : 'btn-outline' ?>"
                       style="width:100%;display:block;margin-top:auto;">
                        Odaberi plan
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="max-width:700px;margin:0 auto;">
        <h3 style="margin-bottom:24px;text-align:center;">Često postavljana pitanja</h3>

        <div class="p-box" style="margin-bottom:14px;">
            <h4 style="color:var(--orange);margin-bottom:8px;">Kako da se učlаnim?</h4>
            <p style="color:var(--text2);font-size:14px;">Jednostavno nas kontaktirajte putem kontakt forme, emaila ili dođite lično. Potrebna je samo lična karta i plaćanje izabrane članarine.</p>
        </div>

        <div class="p-box" style="margin-bottom:14px;">
            <h4 style="color:var(--orange);margin-bottom:8px;">Da li mogu da zamrznem članarinu?</h4>
            <p style="color:var(--text2);font-size:14px;">Da, Standardna i Premium članarina mogu biti zamrznute do 30 dana godišnje (uz pisani zahtev 5 dana unapred).</p>
        </div>

        <div class="p-box" style="margin-bottom:14px;">
            <h4 style="color:var(--orange);margin-bottom:8px;">Postoji li probni period?</h4>
            <p style="color:var(--text2);font-size:14px;">Nudimo jednodnevni besplatni probni trening za sve nove posetioce. Kontaktirajte nas da zakažete.</p>
        </div>

        <div class="p-box">
            <h4 style="color:var(--orange);margin-bottom:8px;">Da li su grupni časovi uključeni?</h4>
            <p style="color:var(--text2);font-size:14px;">Osnovna članarina ne uključuje grupne časove. Standardna uključuje 2 mesečno, a Premium sve časove bez ograničenja.</p>
        </div>
    </div>

    <div style="text-align:center;margin-top:40px;padding:30px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);">
        <h3 style="margin-bottom:12px;">Niste sigurni koji plan je pravi za vas?</h3>
        <p style="color:var(--text2);margin-bottom:20px;">Kontaktirajte nas i pomoći ćemo vam da odaberete plan koji odgovara vašim ciljevima i budžetu.</p>
        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primary">Kontaktiraj nas</a>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
