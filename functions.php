<?php
function e($tekst) {
    return htmlspecialchars((string)($tekst ?? ''), ENT_QUOTES, 'UTF-8');
}

function preusmeriti($url) {
    header('Location: ' . $url);
    exit();
}

function je_li_ulogovan() {
    return isset($_SESSION['korisnik_id']) && !empty($_SESSION['korisnik_id']);
}

function je_li_admin() {
    return je_li_ulogovan() && isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin';
}

function generisati_token() {
    return bin2hex(random_bytes(32));
}

function postaviti_flash($tip, $poruka) {
    $_SESSION['flash_poruke'][] = ['tip' => $tip, 'poruka' => $poruka];
}

function dobiti_flash() {
    $poruke = $_SESSION['flash_poruke'] ?? [];
    unset($_SESSION['flash_poruke']);
    return $poruke;
}

function prikazati_flash() {
    $poruke = dobiti_flash();
    if (empty($poruke)) return '';

    $html = '';
    foreach ($poruke as $p) {
        $klasa = match($p['tip']) {
            'uspeh' => 'flash-uspeh',
            'greska' => 'flash-greska',
            default => 'flash-info'
        };
        $html .= '<div class="flash-poruka ' . $klasa . '">' . e($p['poruka']) . '</div>';
    }
    return $html;
}

function slati_email($do_adrese, $naslov, $html_telo) {
    $api_token = '165eea0c-3f80-43cb-8fe8-21e6ade1e515';

    $data = json_encode([
        'From'          => 'Forma Fitness <vukasinstevanovic7@gmail.com>',
        'To'            => $do_adrese,
        'Subject'       => $naslov,
        'HtmlBody'      => $html_telo,
        'TextBody'      => strip_tags($html_telo),
        'MessageStream' => 'outbound',
    ]);

    $ch = curl_init('https://api.postmarkapp.com/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Postmark-Server-Token: ' . $api_token,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        return true;
    }

    error_log('Forma Fitness email greška: HTTP ' . $http_code . ' | ' . $response);
    file_put_contents(ROOT_PATH . 'email_debug.txt', date('Y-m-d H:i:s') . " | HTTP $http_code | $response\n", FILE_APPEND);
    return false;
}

function email_aktivacija($ime, $link) {
    return "
    <div style='font-family:sans-serif;max-width:600px;margin:0 auto;background:#1a1a1a;color:#e0e0e0;padding:30px;border-radius:8px;'>
        <h2 style='color:#ff6600;'>Dobrodošli u Forma Fitness, {$ime}!</h2>
        <p>Hvala na registraciji. Kliknite na dugme ispod da aktivirate nalog:</p>
        <p style='text-align:center;margin:30px 0;'>
            <a href='{$link}' style='background:#ff6600;color:#fff;padding:12px 30px;border-radius:4px;text-decoration:none;font-weight:bold;'>Aktiviraj nalog</a>
        </p>
        <p style='color:#888;font-size:12px;'>Ako niste kreirali nalog na Forma Fitness sajtu, ignorišite ovaj email.<br>Link je validan 24 sata.</p>
        <hr style='border:1px solid #333;'>
        <p style='color:#555;font-size:11px;text-align:center;'>Forma Fitness Gym &bull; Beograd</p>
    </div>";
}

function upload_sliku($fajl, $folder = 'uploads') {
    $dozvoljeni_tipovi = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_velicina      = 5 * 1024 * 1024;

    if (!isset($fajl['error']) || $fajl['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mime_tip = finfo_file($finfo, $fajl['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_tip, $dozvoljeni_tipovi)) {
        return false;
    }

    if ($fajl['size'] > $max_velicina) {
        return false;
    }

    $ekstenzija = strtolower(pathinfo($fajl['name'], PATHINFO_EXTENSION));
    $novi_naziv = 'img_' . uniqid() . '.' . $ekstenzija;
    $put_do_foldera = ROOT_PATH . $folder;

    if (!is_dir($put_do_foldera)) {
        mkdir($put_do_foldera, 0755, true);
    }

    if (move_uploaded_file($fajl['tmp_name'], $put_do_foldera . DIRECTORY_SEPARATOR . $novi_naziv)) {
        return $novi_naziv;
    }

    return false;
}

function formatirati_datum($datum, $format = 'd.m.Y. H:i') {
    if (!$datum) return '-';
    return date($format, strtotime($datum));
}

function skratiti_tekst($tekst, $duzina = 150) {
    $tekst = strip_tags($tekst);
    if (mb_strlen($tekst) <= $duzina) return $tekst;
    return mb_substr($tekst, 0, $duzina) . '...';
}

function validan_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validna_lozinka($lozinka) {
    return strlen($lozinka) >= 8
        && preg_match('/[A-Z]/', $lozinka)
        && preg_match('/[0-9]/', $lozinka);
}

function dobiti_meni($pdo) {
    $stmt = $pdo->query('SELECT naziv, url FROM meni_stavke WHERE aktivan = 1 ORDER BY pozicija ASC');
    return $stmt->fetchAll();
}

function tezina_labela($tezina) {
    return match($tezina) {
        'lako'   => 'Lako',
        'srednje' => 'Srednje',
        'tesko'  => 'Teško',
        default  => $tezina
    };
}

function tezina_klasa($tezina) {
    return match($tezina) {
        'lako'   => 'badge-lako',
        'srednje' => 'badge-srednje',
        'tesko'  => 'badge-tesko',
        default  => ''
    };
}
