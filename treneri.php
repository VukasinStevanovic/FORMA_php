<?php
require_once 'konekcija.php';
require_once 'functions.php';

$stmt = $pdo->query('SELECT * FROM treneri ORDER BY id ASC');
$treneri = $stmt->fetchAll();

$naslov_stranice = 'Naš tim';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="sekcija-naslov">
        <h2>Naš <span>tim</span></h2>
        <p>Upoznajte stručnjake koji će vas voditi na putu ka ciljevima</p>
    </div>

    <?php if (empty($treneri)): ?>
        <p style="text-align:center;color:var(--text2);">Informacije o trenerima su uskoro dostupne.</p>
    <?php else: ?>

        <?php foreach ($treneri as $t): ?>
        <div class="p-box" style="margin-bottom:24px;">
            <div style="display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;">
                <div style="flex-shrink:0;">
                    <?php if ($t['slika']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($t['slika']) ?>"
                             alt="<?= e($t['ime'] . ' ' . $t['prezime']) ?>"
                             style="width:140px;height:140px;object-fit:cover;border-radius:50%;border:3px solid var(--orange);">
                    <?php else: ?>
                        <div style="width:140px;height:140px;background:var(--bg3);border-radius:50%;border:3px solid var(--orange);display:flex;align-items:center;justify-content:center;font-size:48px;">
                            👨‍💼
                        </div>
                    <?php endif; ?>
                </div>

                <div style="flex:1;min-width:250px;">
                    <h2 style="font-size:22px;margin-bottom:6px;">
                        <?= e($t['ime'] . ' ' . $t['prezime']) ?>
                    </h2>
                    <p style="color:var(--orange);font-size:14px;font-weight:600;margin-bottom:14px;">
                        <?= e($t['specijalnost']) ?>
                    </p>
                    <p style="color:var(--text2);line-height:1.8;"><?= e($t['opis']) ?></p>

                    <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;">
                        <?php
                        $spec = explode(',', $t['specijalnost']);
                        foreach ($spec as $s):
                        ?>
                            <span class="badge badge-orange"><?= e(trim($s)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <div style="text-align:center;margin-top:30px;padding:30px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);">
        <h3 style="margin-bottom:12px;">Zainteresovani za personalni trening?</h3>
        <p style="color:var(--text2);margin-bottom:20px;">
            Kontaktirajte nas da zakažete inicijalni razgovor sa jednim od naših trenera.
            Procenićemo vaš nivo i ciljeve i predložiti optimalan program.
        </p>
        <a href="<?= BASE_URL ?>/kontakt.php" class="btn btn-primary">Zakaži konsultaciju</a>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
