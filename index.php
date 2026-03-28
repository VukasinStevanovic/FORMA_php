<?php
require_once 'konekcija.php';
require_once 'functions.php';

$naslov_stranice = 'Početna';

$stmt = $pdo->query('SELECT id, naziv, grupa_misica, tezina, slika FROM vezbe ORDER BY datum_dodavanja DESC LIMIT 3');
$vezbe = $stmt->fetchAll();

$stmt = $pdo->query('SELECT id, ime, prezime, specijalnost, slika FROM treneri LIMIT 3');
$treneri = $stmt->fetchAll();

$stmt = $pdo->query('SELECT naziv, cena, trajanje_dana, opis FROM clanarine WHERE aktivan = 1 ORDER BY cena ASC');
$clanarine = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="hero">
    <h1>Dobrodošli u <span>Forma Fitness</span></h1>
    <p>Moderan gym u srcu Beograda. Iskusni treneri, vrhunska oprema i baza od 100+ vežbi na jednom mestu.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>/vezbe.php" class="btn btn-primary btn-lg">Istraži vežbe</a>
        <a href="<?= BASE_URL ?>/clanarine.php" class="btn btn-outline btn-lg">Pogledaj cene</a>
    </div>
</section>

<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:30px 20px;">
    <div style="max-width:1100px;margin:0 auto;">
        <div class="grid-4">
            <div style="text-align:center;padding:10px;">
                <div style="font-size:36px;font-weight:800;color:var(--orange);">500+</div>
                <p style="color:var(--text2);font-size:14px;">Aktivnih članova</p>
            </div>
            <div style="text-align:center;padding:10px;">
                <div style="font-size:36px;font-weight:800;color:var(--orange);">100+</div>
                <p style="color:var(--text2);font-size:14px;">Vežbi u bazi</p>
            </div>
            <div style="text-align:center;padding:10px;">
                <div style="font-size:36px;font-weight:800;color:var(--orange);">3</div>
                <p style="color:var(--text2);font-size:14px;">Iskusna trenera</p>
            </div>
            <div style="text-align:center;padding:10px;">
                <div style="font-size:36px;font-weight:800;color:var(--orange);">5g</div>
                <p style="color:var(--text2);font-size:14px;">U poslu</p>
            </div>
        </div>
    </div>
</section>

<div class="main-content">

    <section style="margin-bottom:50px;">
        <div class="sekcija-naslov">
            <h2>Popularne <span>vežbe</span></h2>
            <p>Pogledaj deo naše baze vežbi sa uputstvima i video materijalima</p>
        </div>
        <div class="grid-3">
            <?php foreach ($vezbe as $v): ?>
            <a href="<?= BASE_URL ?>/vezba_detalj.php?id=<?= $v['id'] ?>" style="text-decoration:none;">
                <div class="card">
                    <?php if ($v['slika']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($v['slika']) ?>" alt="<?= e($v['naziv']) ?>" class="card-img" style="height:180px;object-fit:cover;">
                    <?php else: ?>
                        <div class="placeholder-img">🏋️</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?= e($v['naziv']) ?></h3>
                        <p><span class="badge badge-orange"><?= e($v['grupa_misica']) ?></span></p>
                        <span class="badge <?= tezina_klasa($v['tezina']) ?>"><?= tezina_labela($v['tezina']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/vezbe.php" class="btn btn-outline">Sve vežbe →</a>
        </div>
    </section>

    <section style="margin-bottom:50px;">
        <div class="sekcija-naslov">
            <h2>Naš <span>tim</span></h2>
            <p>Upoznajte naše iskusne trenere</p>
        </div>
        <div class="grid-3">
            <?php foreach ($treneri as $t): ?>
            <div class="card trener-card">
                <?php if ($t['slika']): ?>
                    <img src="<?= BASE_URL ?>/uploads/<?= e($t['slika']) ?>" alt="<?= e($t['ime']) ?>" class="card-img" style="height:200px;object-fit:cover;">
                <?php else: ?>
                    <div class="card-img">👨‍💼</div>
                <?php endif; ?>
                <div class="card-body">
                    <h3><?= e($t['ime'] . ' ' . $t['prezime']) ?></h3>
                    <p class="specijalnost"><?= e($t['specijalnost']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/treneri.php" class="btn btn-outline">Svi treneri →</a>
        </div>
    </section>

    <section style="margin-bottom:50px;">
        <div class="sekcija-naslov">
            <h2>Planovi <span>članarine</span></h2>
            <p>Izaberite plan koji odgovara vašim ciljevima</p>
        </div>
        <div class="grid-3">
            <?php foreach ($clanarine as $i => $c): ?>
            <div class="card clanarina-card <?= $i === 1 ? 'istaknuta' : '' ?>">
                <div class="card-body">
                    <h3><?= e($c['naziv']) ?></h3>
                    <div class="clanarina-cena"><?= number_format($c['cena'], 0, ',', '.') ?><span> RSD</span></div>
                    <div class="clanarina-trajanje"><?= $c['trajanje_dana'] ?> dana</div>
                    <p class="clanarina-opis"><?= e(skratiti_tekst($c['opis'], 100)) ?></p>
                    <a href="<?= BASE_URL ?>/clanarine.php" class="btn btn-primary" style="width:100%;display:block;">Saznaj više</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:40px;text-align:center;margin-bottom:20px;">
        <h2 style="margin-bottom:12px;">Spreman/na da počneš?</h2>
        <p style="color:var(--text2);margin-bottom:24px;">Javi nam se i saznaj sve što te zanima o našem klubu</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primary">Kontaktiraj nas</a>
            <a href="<?= BASE_URL ?>/casovi.php" class="btn btn-outline">Raspored časova</a>
        </div>
    </section>

</div>

<?php require_once 'includes/footer.php'; ?>
