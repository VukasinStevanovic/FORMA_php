<?php
require_once 'konekcija.php';
require_once 'functions.php';

$stmt = $pdo->query('
    SELECT c.id, c.naziv, c.opis, c.dan_u_nedelji, c.vreme, c.kapacitet,
           CONCAT(t.ime, " ", t.prezime) AS trener_ime, t.id AS trener_id
    FROM casovi c
    LEFT JOIN treneri t ON c.trener_id = t.id
    WHERE c.aktivan = 1
    ORDER BY FIELD(c.dan_u_nedelji, "Ponedeljak","Utorak","Sreda","Četvrtak","Petak","Subota","Nedelja"), c.vreme ASC
');
$casovi = $stmt->fetchAll();

$po_danu = [];
foreach ($casovi as $cas) {
    $po_danu[$cas['dan_u_nedelji']][] = $cas;
}

$redosled_dana = ['Ponedeljak', 'Utorak', 'Sreda', 'Četvrtak', 'Petak', 'Subota', 'Nedelja'];

$naslov_stranice = 'Raspored treninga';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="page-naslov">
        <h1>Raspored <span>treninga</span></h1>
        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primary btn-sm">Prijavi se za trening</a>
    </div>

    <p style="color:var(--text2);margin-bottom:30px;">
        Kapacitet je ograničen — prijavite se na vreme!
    </p>

    <?php if (empty($casovi)): ?>
        <div style="text-align:center;padding:60px;color:var(--text2);">
            <div style="font-size:48px;margin-bottom:16px;">📅</div>
            <h3>Trenutno nema dostupnih treninga.</h3>
            <p>Proverite ponovo uskoro.</p>
        </div>
    <?php else: ?>

        <?php foreach ($redosled_dana as $dan): ?>
            <?php if (!isset($po_danu[$dan])) continue; ?>

            <div style="margin-bottom:32px;">
                <h2 style="font-size:20px;padding-bottom:8px;border-bottom:2px solid var(--orange);margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                    <span style="background:var(--orange);color:#fff;border-radius:50%;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;">
                        <?= substr($dan, 0, 1) ?>
                    </span>
                    <?= e($dan) ?>
                </h2>

                <?php foreach ($po_danu[$dan] as $cas): ?>
                <div class="cas-kartica">
                    <div style="flex:1;">
                        <h3><?= e($cas['naziv']) ?></h3>
                        <?php if ($cas['trener_ime']): ?>
                            <p style="margin-bottom:8px;">
                                👨‍💼 Trener:
                                <a href="<?= BASE_URL ?>/treneri.php" style="color:var(--orange);">
                                    <?= e($cas['trener_ime']) ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <p><?= e($cas['opis']) ?></p>
                        <p style="margin-top:8px;font-size:13px;color:var(--text3);">
                            👥 Kapacitet: <?= $cas['kapacitet'] ?> mesta
                        </p>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div class="cas-vreme"><?= substr($cas['vreme'], 0, 5) ?>h</div>
                        <div class="cas-dan"><?= e($cas['dan_u_nedelji']) ?></div>
                        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-outline btn-sm" style="margin-top:10px;display:inline-block;">
                            Prijavi se
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <div class="p-box mt-3" style="border-left:3px solid var(--orange);">
        <h4 style="margin-bottom:8px;">ℹ️ Napomena</h4>
        <p style="color:var(--text2);font-size:14px;">
            Raspored može biti promenjen praznikom ili tokom školskog raspusta.
            Za tačne informacije, kontaktirajte nas na <a href="mailto:vukasin.stevanovic.34.24@ict.edu.rs">vukasin.stevanovic.34.24@ict.edu.rs</a>
            ili pozovite <strong>+381 66 5 771171</strong>.
        </p>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
