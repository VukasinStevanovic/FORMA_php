<?php
if (!function_exists('dobiti_meni')) {
    require_once dirname(__DIR__) . '/functions.php';
}

$meni = dobiti_meni($pdo);

$trenutni_url = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($naslov_stranice) ? e($naslov_stranice) . ' | Forma Fitness' : 'Forma Fitness - Gym & Baza vežbi' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
    <meta name="description" content="Forma Fitness gym klub - baza vežbi, raspored časova, treneri i članarine u Beogradu.">
</head>
<body>

<nav class="navbar">
    <div class="nav-inner">
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo">FIT<span>ZONE</span></a>

        <button class="hamburger" id="hamburger" aria-label="Otvori meni">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-links" id="nav-links">
            <?php foreach ($meni as $stavka): ?>
                <a href="<?= e($stavka['url']) ?>"
                   class="<?= str_contains($trenutni_url, basename($stavka['url'])) ? 'active' : '' ?>">
                    <?= e($stavka['naziv']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="nav-right">
            <?php if (je_li_ulogovan()): ?>
                <a href="<?= BASE_URL ?>/profil.php">
                    👤 <?= e($_SESSION['korisnik_ime'] ?? 'Profil') ?>
                </a>
                <?php if (je_li_admin()): ?>
                    <a href="<?= BASE_URL ?>/admin/">Admin</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/logout.php">Odjava</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Prijava</a>
                <a href="<?= BASE_URL ?>/registracija.php" class="btn-nav">Registracija</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php
$flash_html = prikazati_flash();
if ($flash_html): ?>
    <div style="max-width:1100px;margin:16px auto;padding:0 20px;">
        <?= $flash_html ?>
    </div>
<?php endif; ?>

<script>
document.getElementById('hamburger').addEventListener('click', function() {
    document.getElementById('nav-links').classList.toggle('open');
});
</script>
