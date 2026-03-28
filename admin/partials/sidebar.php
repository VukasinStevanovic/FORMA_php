<?php
$trenutna = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <a href="<?= BASE_URL ?>/admin/" class="logo">⚡ Forma Fitness Admin</a>

    <a href="<?= BASE_URL ?>/admin/"
       class="<?= $trenutna === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : '' ?>">
        📊 Dashboard
    </a>

    <div class="nav-section">Sadržaj</div>
    <a href="<?= BASE_URL ?>/admin/vezbe.php"    class="<?= $trenutna === 'vezbe.php'    ? 'active' : '' ?>">🏋️ Vežbe</a>
    <a href="<?= BASE_URL ?>/admin/casovi.php"   class="<?= $trenutna === 'casovi.php'   ? 'active' : '' ?>">📅 Časovi</a>
    <a href="<?= BASE_URL ?>/admin/treneri.php"  class="<?= $trenutna === 'treneri.php'  ? 'active' : '' ?>">👨‍💼 Treneri</a>
    <a href="<?= BASE_URL ?>/admin/clanarine.php" class="<?= $trenutna === 'clanarine.php' ? 'active' : '' ?>">💳 Članarine</a>

    <div class="nav-section">Korisnici</div>
    <a href="<?= BASE_URL ?>/admin/korisnici.php" class="<?= $trenutna === 'korisnici.php' ? 'active' : '' ?>">👤 Korisnici</a>
    <a href="<?= BASE_URL ?>/admin/kontakt.php"  class="<?= $trenutna === 'kontakt.php'  ? 'active' : '' ?>">✉️ Poruke</a>

    <div class="nav-section">Ostalo</div>
    <a href="<?= BASE_URL ?>/admin/ankete.php"   class="<?= $trenutna === 'ankete.php'   ? 'active' : '' ?>">📊 Ankete</a>
    <a href="<?= BASE_URL ?>/admin/meni.php"     class="<?= $trenutna === 'meni.php'     ? 'active' : '' ?>">☰ Meni</a>

    <div style="border-top:1px solid var(--border);margin:16px 0;"></div>
    <a href="<?= BASE_URL ?>/index.php" target="_blank">🌐 Sajt ↗</a>
    <a href="<?= BASE_URL ?>/logout.php" style="color:#e74c3c;">🚪 Odjava</a>
</aside>
