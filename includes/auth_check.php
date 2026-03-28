<?php
if (!function_exists('je_li_ulogovan')) {
    require_once dirname(__DIR__) . '/functions.php';
}

function zahtevaj_login() {
    if (!je_li_ulogovan()) {
        postaviti_flash('greska', 'Morate biti prijavljeni da biste pristupili ovoj stranici.');
        preusmeriti(BASE_URL . '/login.php');
    }
}

function zahtevaj_admina() {
    if (!je_li_ulogovan()) {
        postaviti_flash('greska', 'Morate biti prijavljeni da biste pristupili admin panelu.');
        preusmeriti(BASE_URL . '/login.php');
    }

    if (!je_li_admin()) {
        postaviti_flash('greska', 'Nemate dozvolu za pristup admin panelu.');
        preusmeriti(BASE_URL . '/index.php');
    }
}
