<?php
require_once 'konekcija.php';
require_once 'functions.php';

$pretraga      = trim($_GET['pretraga']   ?? '');
$filter_grupa  = trim($_GET['grupa']      ?? '');
$filter_tezina = trim($_GET['tezina']     ?? '');

$uslovi = [];
$params = [];

if (!empty($pretraga)) {
    $uslovi[] = '(naziv LIKE ? OR opis LIKE ?)';
    $params[] = '%' . $pretraga . '%';
    $params[] = '%' . $pretraga . '%';
}

if (!empty($filter_grupa)) {
    $uslovi[] = 'grupa_misica = ?';
    $params[] = $filter_grupa;
}

if (!empty($filter_tezina)) {
    $uslovi[] = 'tezina = ?';
    $params[] = $filter_tezina;
}

$where = !empty($uslovi) ? 'WHERE ' . implode(' AND ', $uslovi) : '';

$sql  = "SELECT id, naziv, opis, slika, grupa_misica, tip_vezbe, tezina FROM vezbe {$where} ORDER BY naziv ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vezbe = $stmt->fetchAll();

$stmt_grupe = $pdo->query('SELECT DISTINCT grupa_misica FROM vezbe WHERE grupa_misica IS NOT NULL ORDER BY grupa_misica ASC');
$grupe = $stmt_grupe->fetchAll(PDO::FETCH_COLUMN);

$naslov_stranice = 'Baza vežbi';
require_once 'includes/header.php';
?>

<div class="main-content">

    <div class="page-naslov">
        <h1>Baza <span>vežbi</span></h1>
        <span style="color:var(--text2);font-size:14px;">Pronađeno: <?= count($vezbe) ?> vežbi</span>
    </div>

    <div class="filter-bar">
        <div class="forma-group">
            <label for="pretraga">Pretraga po nazivu</label>
            <input type="text" id="pretraga" placeholder="Npr. bench press..."
                   value="<?= e($pretraga) ?>">
        </div>
        <div class="forma-group">
            <label for="filter-grupa">Grupa mišića</label>
            <select id="filter-grupa">
                <option value="">-- Sve grupe --</option>
                <?php foreach ($grupe as $g): ?>
                    <option value="<?= e($g) ?>" <?= $filter_grupa === $g ? 'selected' : '' ?>>
                        <?= e($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="forma-group">
            <label for="filter-tezina">Težina</label>
            <select id="filter-tezina">
                <option value="">-- Sve težine --</option>
                <option value="lako"    <?= $filter_tezina === 'lako'    ? 'selected' : '' ?>>Lako</option>
                <option value="srednje" <?= $filter_tezina === 'srednje' ? 'selected' : '' ?>>Srednje</option>
                <option value="tesko"   <?= $filter_tezina === 'tesko'   ? 'selected' : '' ?>>Teško</option>
            </select>
        </div>
        <div style="align-self:flex-end;">
            <a href="<?= BASE_URL ?>/vezbe.php" class="btn btn-outline btn-sm" style="display:inline-block;">
                Resetuj filtere
            </a>
        </div>
    </div>

    <?php if (empty($vezbe)): ?>
        <div style="text-align:center;padding:60px 20px;color:var(--text2);">
            <div style="font-size:48px;margin-bottom:16px;">🔍</div>
            <h3>Nema rezultata</h3>
            <p>Pokušajte sa drugom pretragom ili resetujte filtere.</p>
            <a href="<?= BASE_URL ?>/vezbe.php" class="btn btn-outline mt-2">Prikaži sve vežbe</a>
        </div>
    <?php else: ?>
        <div class="grid-3" id="vezbe-grid">
            <?php foreach ($vezbe as $v): ?>
            <a href="<?= BASE_URL ?>/vezba_detalj.php?id=<?= $v['id'] ?>" style="text-decoration:none;"
               data-naziv="<?= strtolower(e($v['naziv'])) ?>"
               data-opis="<?= strtolower(e(substr($v['opis'] ?? '', 0, 100))) ?>"
               class="vezba-item">
                <div class="card" style="height:100%;">
                    <?php if ($v['slika']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($v['slika']) ?>"
                             alt="<?= e($v['naziv']) ?>"
                             style="width:100%;height:180px;object-fit:cover;">
                    <?php else: ?>
                        <div class="placeholder-img">🏋️</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 style="margin-bottom:8px;"><?= e($v['naziv']) ?></h3>
                        <p style="margin-bottom:10px;"><?= e(skratiti_tekst($v['opis'] ?? '', 90)) ?></p>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                            <span class="badge badge-orange"><?= e($v['grupa_misica']) ?></span>
                            <span class="badge <?= tezina_klasa($v['tezina']) ?>"><?= tezina_labela($v['tezina']) ?></span>
                            <?php if ($v['tip_vezbe']): ?>
                                <span style="font-size:12px;color:var(--text3);"><?= e($v['tip_vezbe']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
const pretragaInput   = document.getElementById('pretraga');
const grupaSelect     = document.getElementById('filter-grupa');
const tezinaSelect    = document.getElementById('filter-tezina');

function primeniFiltre() {
    const params = new URLSearchParams();
    if (pretragaInput.value.trim())   params.set('pretraga', pretragaInput.value.trim());
    if (grupaSelect.value)            params.set('grupa',    grupaSelect.value);
    if (tezinaSelect.value)           params.set('tezina',   tezinaSelect.value);

    window.location.href = '<?= BASE_URL ?>/vezbe.php' + (params.toString() ? '?' + params.toString() : '');
}

grupaSelect.addEventListener('change', primeniFiltre);
tezinaSelect.addEventListener('change', primeniFiltre);

let timer;
pretragaInput.addEventListener('input', function() {
    clearTimeout(timer);
    timer = setTimeout(primeniFiltre, 500);
});
pretragaInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { clearTimeout(timer); primeniFiltre(); }
});
</script>

<?php require_once 'includes/footer.php'; ?>
