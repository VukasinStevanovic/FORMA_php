<?php
require_once 'konekcija.php';
require_once 'functions.php';

$naslov_stranice = 'Početna';

$stmt = $pdo->query('SELECT id, naziv, grupa_misica, tezina, slika FROM vezbe ORDER BY datum_dodavanja DESC LIMIT 3');
$vezbe = $stmt->fetchAll();

$stmt = $pdo->query('SELECT id, ime, prezime, specijalnost, slika, pol FROM treneri LIMIT 3');
$treneri = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="hero">
    <h1>Budi i ti u <span>FORMI!</span></h1>
    <p>Moderna teretana u srcu Pančeva — čisto, bez gužve i sa besplatnim parkingom.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>/vezbe.php" class="btn btn-primary btn-lg">Istraži vežbe</a>
        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-outline btn-lg">Kontaktiraj nas</a>
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
                        <div class="placeholder-img" style="display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-dumbbell" style="font-size:32px;color:var(--orange);"></i></div>
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
                    <div class="card-img" style="display:flex;align-items:center;justify-content:center;background:var(--bg3);">
                        <i class="<?= ($t['pol'] ?? 'm') === 'z' ? 'fa-solid fa-person-dress' : 'fa-solid fa-person' ?>" style="font-size:52px;color:var(--orange);"></i>
                    </div>
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


    <section style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:40px;text-align:center;margin-bottom:20px;">
        <h2 style="margin-bottom:12px;">Spreman/na da počneš?</h2>
        <p style="color:var(--text2);margin-bottom:24px;">Javi nam se i saznaj sve što te zanima o našem klubu</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primary">Kontaktiraj nas</a>
            <a href="<?= BASE_URL ?>/treninzi.php" class="btn btn-outline">Raspored treninga</a>
        </div>
    </section>

</div>

<div style="text-align:center;padding:16px 20px;background:var(--bg2);border-top:1px solid var(--border);">
    <a href="<?= BASE_URL ?>/dokumentacija.html" target="_blank" style="color:var(--text3);font-size:13px;text-decoration:none;">
        <i class="fa-solid fa-file-lines"></i> Dokumentacija
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>
