<?php
$trenutna = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <a href="<?= BASE_URL ?>/admin/" class="logo">
        <i class="fa-solid fa-bolt" style="color:var(--orange);"></i> Forma Fitness Admin
    </a>

    <a href="<?= BASE_URL ?>/admin/"
       class="<?= $trenutna === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high si"></i> Dashboard
    </a>

    <div class="nav-section">Sadržaj</div>
    <a href="<?= BASE_URL ?>/admin/vezbe.php"    class="<?= $trenutna === 'vezbe.php'    ? 'active' : '' ?>"><i class="fa-solid fa-dumbbell si"></i> Vežbe</a>
    <a href="<?= BASE_URL ?>/admin/treninzi.php"  class="<?= $trenutna === 'treninzi.php'  ? 'active' : '' ?>"><i class="fa-solid fa-calendar-days si"></i> Treninzi</a>
    <a href="<?= BASE_URL ?>/admin/treneri.php"  class="<?= $trenutna === 'treneri.php'  ? 'active' : '' ?>"><i class="fa-solid fa-user-tie si"></i> Treneri</a>

    <div class="nav-section">Korisnici</div>
    <a href="<?= BASE_URL ?>/admin/korisnici.php" class="<?= $trenutna === 'korisnici.php' ? 'active' : '' ?>"><i class="fa-solid fa-users si"></i> Korisnici</a>
    <a href="<?= BASE_URL ?>/admin/kontakt.php"  class="<?= $trenutna === 'kontakt.php'  ? 'active' : '' ?>"><i class="fa-solid fa-envelope si"></i> Poruke</a>

    <div class="nav-section">Ostalo</div>
    <a href="<?= BASE_URL ?>/admin/ankete.php"   class="<?= $trenutna === 'ankete.php'   ? 'active' : '' ?>"><i class="fa-solid fa-chart-pie si"></i> Ankete</a>
    <a href="<?= BASE_URL ?>/admin/meni.php"     class="<?= $trenutna === 'meni.php'     ? 'active' : '' ?>"><i class="fa-solid fa-bars si"></i> Meni</a>

    <div style="border-top:1px solid var(--border);margin:16px 0;"></div>
    <a href="<?= BASE_URL ?>/index.php" target="_blank"><i class="fa-solid fa-globe si"></i> Sajt ↗</a>
    <a href="<?= BASE_URL ?>/logout.php" style="color:#e74c3c;"><i class="fa-solid fa-right-from-bracket" style="width:18px;text-align:center;color:#e74c3c;"></i> Odjava</a>
</aside>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.si { color: var(--orange); width: 18px; text-align: center; display: inline-block; }
</style>
