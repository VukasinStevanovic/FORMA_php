<?php
require_once 'konekcija.php';
require_once 'functions.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    preusmeriti(BASE_URL . '/vezbe.php');
}

$stmt = $pdo->prepare('SELECT * FROM vezbe WHERE id = ?');
$stmt->execute([$id]);
$vezba = $stmt->fetch();

if (!$vezba) {
    postaviti_flash('greska', 'Vežba nije pronađena.');
    preusmeriti(BASE_URL . '/vezbe.php');
}

$stmt2 = $pdo->prepare('
    SELECT id, naziv, tezina, slika
    FROM vezbe
    WHERE grupa_misica = ? AND id != ?
    ORDER BY RAND()
    LIMIT 3
');
$stmt2->execute([$vezba['grupa_misica'], $id]);
$slicne = $stmt2->fetchAll();

$naslov_stranice = $vezba['naziv'];
require_once 'includes/header.php';
?>

<div class="main-content">

    <p style="margin-bottom:20px;">
        <a href="<?= BASE_URL ?>/vezbe.php" style="color:var(--text2);font-size:14px;">← Nazad na sve vežbe</a>
    </p>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:30px;align-items:start;">

        <div>
            <?php if ($vezba['slika']): ?>
                <img src="<?= BASE_URL ?>/uploads/<?= e($vezba['slika']) ?>"
                     alt="<?= e($vezba['naziv']) ?>"
                     style="width:100%;max-height:400px;object-fit:cover;border-radius:var(--radius);margin-bottom:24px;">
            <?php else: ?>
                <div style="width:100%;height:280px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;font-size:72px;margin-bottom:24px;">
                    🏋️
                </div>
            <?php endif; ?>

            <h1 style="font-size:28px;margin-bottom:16px;"><?= e($vezba['naziv']) ?></h1>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;">
                <span class="badge badge-orange"><?= e($vezba['grupa_misica']) ?></span>
                <span class="badge <?= tezina_klasa($vezba['tezina']) ?>"><?= tezina_labela($vezba['tezina']) ?></span>
                <?php if ($vezba['tip_vezbe']): ?>
                    <span class="badge" style="background:var(--bg3);color:var(--text2);"><?= e($vezba['tip_vezbe']) ?></span>
                <?php endif; ?>
            </div>

            <div class="p-box">
                <h3 style="margin-bottom:12px;">Opis i tehnika</h3>
                <p style="color:var(--text2);line-height:1.8;white-space:pre-line;"><?= e($vezba['opis']) ?></p>
            </div>
        </div>

        <div>
            <div class="p-box" style="margin-bottom:20px;">
                <h3 style="margin-bottom:16px;">Informacije</h3>
                <table style="width:100%;font-size:14px;">
                    <tr>
                        <td style="padding:8px 0;color:var(--text2);border-bottom:1px solid var(--border);">Grupa mišića</td>
                        <td style="padding:8px 0;border-bottom:1px solid var(--border);text-align:right;">
                            <strong><?= e($vezba['grupa_misica']) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:var(--text2);border-bottom:1px solid var(--border);">Tip vežbe</td>
                        <td style="padding:8px 0;border-bottom:1px solid var(--border);text-align:right;">
                            <strong><?= e($vezba['tip_vezbe'] ?: 'N/A') ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:var(--text2);border-bottom:1px solid var(--border);">Težina</td>
                        <td style="padding:8px 0;border-bottom:1px solid var(--border);text-align:right;">
                            <span class="badge <?= tezina_klasa($vezba['tezina']) ?>"><?= tezina_labela($vezba['tezina']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:var(--text2);">Dodato</td>
                        <td style="padding:8px 0;text-align:right;">
                            <strong><?= formatirati_datum($vezba['datum_dodavanja'], 'd.m.Y.') ?></strong>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if ($vezba['tezina'] === 'tesko'): ?>
            <div class="p-box" style="border-left:3px solid #e74c3c;margin-bottom:20px;">
                <h4 style="color:#e74c3c;margin-bottom:8px;">⚠️ Napredna vežba</h4>
                <p style="color:var(--text2);font-size:13px;">Ova vežba zahteva dobru tehničku pripremu. Preporučujemo da je izvodite pod nadzorom iskusnog trenera, posebno pri prvim pokušajima.</p>
            </div>
            <?php endif; ?>

            <div class="p-box">
                <h4 style="margin-bottom:12px;">Zainteresovani za trening?</h4>
                <p style="color:var(--text2);font-size:13px;margin-bottom:14px;">Pogledajte naš raspored grupnih časova ili kontaktirajte trenere.</p>
                <a href="<?= BASE_URL ?>/casovi.php" class="btn btn-primary btn-sm" style="display:block;text-align:center;margin-bottom:8px;">Raspored časova</a>
                <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-outline btn-sm" style="display:block;text-align:center;">Kontaktiraj nas</a>
            </div>
        </div>

    </div>

    <?php if (!empty($slicne)): ?>
    <section style="margin-top:40px;">
        <h3 style="margin-bottom:20px;">Slične vežbe <span style="color:var(--text2);font-size:15px;">(<?= e($vezba['grupa_misica']) ?>)</span></h3>
        <div class="grid-3">
            <?php foreach ($slicne as $s): ?>
            <a href="<?= BASE_URL ?>/vezba_detalj.php?id=<?= $s['id'] ?>" style="text-decoration:none;">
                <div class="card">
                    <?php if ($s['slika']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($s['slika']) ?>" alt="<?= e($s['naziv']) ?>" style="width:100%;height:140px;object-fit:cover;">
                    <?php else: ?>
                        <div class="placeholder-img" style="height:140px;font-size:32px;">🏋️</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 style="font-size:15px;"><?= e($s['naziv']) ?></h3>
                        <span class="badge <?= tezina_klasa($s['tezina']) ?>"><?= tezina_labela($s['tezina']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div>

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
