<?php
require_once 'konekcija.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anketa_id'], $_POST['odgovor_id'])) {
    $anketa_id  = (int)$_POST['anketa_id'];
    $odgovor_id = (int)$_POST['odgovor_id'];
    $ip         = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $korisnik_id = je_li_ulogovan() ? $_SESSION['korisnik_id'] : null;

    $stmt = $pdo->prepare('SELECT id FROM ankete WHERE id = ? AND aktivan = 1 LIMIT 1');
    $stmt->execute([$anketa_id]);
    $anketa_ok = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT id FROM anketa_odgovori WHERE id = ? AND anketa_id = ? LIMIT 1');
    $stmt->execute([$odgovor_id, $anketa_id]);
    $odg_ok = $stmt->fetch();

    if ($anketa_ok && $odg_ok) {
        $vec_glasao = false;

        if ($korisnik_id) {
            $stmt = $pdo->prepare('SELECT id FROM anketa_glasovi WHERE anketa_id = ? AND korisnik_id = ? LIMIT 1');
            $stmt->execute([$anketa_id, $korisnik_id]);
            $vec_glasao = (bool)$stmt->fetch();
        } else {
            $stmt = $pdo->prepare('SELECT id FROM anketa_glasovi WHERE anketa_id = ? AND ip_adresa = ? AND korisnik_id IS NULL LIMIT 1');
            $stmt->execute([$anketa_id, $ip]);
            $vec_glasao = (bool)$stmt->fetch();
        }

        if (!$vec_glasao) {
            $pdo->prepare('UPDATE anketa_odgovori SET broj_glasova = broj_glasova + 1 WHERE id = ?')
                ->execute([$odgovor_id]);

            $pdo->prepare('INSERT INTO anketa_glasovi (anketa_id, korisnik_id, ip_adresa) VALUES (?, ?, ?)')
                ->execute([$anketa_id, $korisnik_id, $ip]);

            postaviti_flash('uspeh', 'Glas je zabeležen! Hvala na učešću.');
        } else {
            postaviti_flash('greska', 'Već ste glasali u ovoj anketi.');
        }
    } else {
        postaviti_flash('greska', 'Nešto je pošlo naopako. Pokušajte ponovo.');
    }

    preusmeriti(BASE_URL . '/anketa.php#anketa-' . $anketa_id);
}

$stmt = $pdo->query('SELECT * FROM ankete WHERE aktivan = 1 ORDER BY datum_kreiranja DESC');
$ankete = $stmt->fetchAll();

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$korisnik_id = je_li_ulogovan() ? $_SESSION['korisnik_id'] : null;

foreach ($ankete as &$anketa) {
    $stmt = $pdo->prepare('SELECT * FROM anketa_odgovori WHERE anketa_id = ? ORDER BY id ASC');
    $stmt->execute([$anketa['id']]);
    $anketa['odgovori'] = $stmt->fetchAll();

    $anketa['ukupno'] = array_sum(array_column($anketa['odgovori'], 'broj_glasova'));

    if ($korisnik_id) {
        $stmt = $pdo->prepare('SELECT id FROM anketa_glasovi WHERE anketa_id = ? AND korisnik_id = ? LIMIT 1');
        $stmt->execute([$anketa['id'], $korisnik_id]);
    } else {
        $stmt = $pdo->prepare('SELECT id FROM anketa_glasovi WHERE anketa_id = ? AND ip_adresa = ? AND korisnik_id IS NULL LIMIT 1');
        $stmt->execute([$anketa['id'], $ip]);
    }
    $anketa['vec_glasao'] = (bool)$stmt->fetch();
}
unset($anketa);

$naslov_stranice = 'Ankete';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="page-naslov">
        <h1><i class="fa-solid fa-chart-pie" style="color:var(--orange);"></i> <span>Ankete</span></h1>
    </div>

    <p style="color:var(--text2);margin-bottom:30px;">
        Vaše mišljenje nam je važno! Učestvujte u anketama i pomozite nam da poboljšamo naše usluge.
    </p>

    <?php if (empty($ankete)): ?>
        <div style="text-align:center;padding:60px;color:var(--text2);">
            <div style="font-size:48px;margin-bottom:16px;"><i class="fa-solid fa-clipboard-list" style="color:var(--orange);"></i></div>
            <h3>Trenutno nema aktivnih anketa.</h3>
            <p>Proverite ponovo uskoro!</p>
        </div>
    <?php else: ?>

        <?php foreach ($ankete as $a): ?>
        <div class="anketa-blok" id="anketa-<?= $a['id'] ?>">
            <h3><?= e($a['pitanje']) ?></h3>

            <?php if ($a['vec_glasao']): ?>
                <p style="color:var(--text2);font-size:13px;margin-bottom:16px;">
                    ✓ Već ste glasali • Ukupno glasova: <strong><?= $a['ukupno'] ?></strong>
                </p>

                <?php foreach ($a['odgovori'] as $o): ?>
                    <?php $procenat = $a['ukupno'] > 0 ? round(($o['broj_glasova'] / $a['ukupno']) * 100) : 0; ?>
                    <div class="progress-wrap">
                        <div class="progress-label">
                            <strong><?= e($o['tekst_odgovora']) ?></strong>
                            <span><?= $o['broj_glasova'] ?> glasova (<?= $procenat ?>%)</span>
                        </div>
                        <div class="progress-bar-outer">
                            <div class="progress-bar-inner" style="width:<?= $procenat ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <form method="POST" action="<?= BASE_URL ?>/anketa.php">
                    <input type="hidden" name="anketa_id" value="<?= $a['id'] ?>">

                    <?php foreach ($a['odgovori'] as $o): ?>
                    <label class="anketa-opcija">
                        <input type="radio" name="odgovor_id" value="<?= $o['id'] ?>" required>
                        <?= e($o['tekst_odgovora']) ?>
                    </label>
                    <?php endforeach; ?>

                    <div style="margin-top:16px;">
                        <button type="submit" class="btn btn-primary btn-sm">Glasaj</button>
                        <?php if (!je_li_ulogovan()): ?>
                            <span style="font-size:12px;color:var(--text3);margin-left:10px;">
                                Glasanje je dostupno i bez prijave (provjera po IP adresi)
                            </span>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>
