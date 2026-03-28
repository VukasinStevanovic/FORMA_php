CREATE DATABASE IF NOT EXISTS forma_fitness_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE forma_fitness_db;

CREATE TABLE IF NOT EXISTS korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(100) NOT NULL,
    prezime VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    lozinka VARCHAR(255) NOT NULL,
    uloga ENUM('admin','korisnik') DEFAULT 'korisnik',
    aktivan TINYINT DEFAULT 0,
    aktivacioni_token VARCHAR(64),
    datum_registracije DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS meni_stavke (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    pozicija INT DEFAULT 0,
    aktivan TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS treneri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(100) NOT NULL,
    prezime VARCHAR(100) NOT NULL,
    slika VARCHAR(255),
    opis TEXT,
    specijalnost VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vezbe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(255) NOT NULL,
    opis TEXT,
    slika VARCHAR(255),
    grupa_misica VARCHAR(100),
    tip_vezbe VARCHAR(100),
    tezina ENUM('lako','srednje','tesko') DEFAULT 'srednje',
    datum_dodavanja DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS casovi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(255) NOT NULL,
    trener_id INT,
    opis TEXT,
    dan_u_nedelji VARCHAR(20),
    vreme TIME,
    kapacitet INT DEFAULT 20,
    aktivan TINYINT DEFAULT 1,
    FOREIGN KEY (trener_id) REFERENCES treneri(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clanarine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(255) NOT NULL,
    cena DECIMAL(10,2) NOT NULL,
    trajanje_dana INT NOT NULL,
    opis TEXT,
    aktivan TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kontakt_poruke (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    naslov VARCHAR(255) NOT NULL,
    poruka TEXT NOT NULL,
    datum DATETIME DEFAULT CURRENT_TIMESTAMP,
    procitano TINYINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ankete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pitanje TEXT NOT NULL,
    aktivan TINYINT DEFAULT 1,
    datum_kreiranja DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS anketa_odgovori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anketa_id INT NOT NULL,
    tekst_odgovora VARCHAR(255) NOT NULL,
    broj_glasova INT DEFAULT 0,
    FOREIGN KEY (anketa_id) REFERENCES ankete(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS anketa_glasovi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anketa_id INT NOT NULL,
    korisnik_id INT,
    ip_adresa VARCHAR(50),
    datum DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (anketa_id) REFERENCES ankete(id) ON DELETE CASCADE,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO korisnici (ime, prezime, email, lozinka, uloga, aktivan, aktivacioni_token) VALUES
('Admin', 'Forma Fitness', 'admin@forma_fitness.rs', 'PLACEHOLDER_POKRENUTI_SETUP', 'admin', 1, NULL);

INSERT INTO meni_stavke (naziv, url, pozicija, aktivan) VALUES
('Početna', '/forma_fitness/index.php', 1, 1),
('Vežbe', '/forma_fitness/vezbe.php', 2, 1),
('Časovi', '/forma_fitness/casovi.php', 3, 1),
('Treneri', '/forma_fitness/treneri.php', 4, 1),
('Članarine', '/forma_fitness/clanarine.php', 5, 1),
('Kontakt', '/forma_fitness/kontakt.php', 6, 1);

INSERT INTO treneri (ime, prezime, slika, opis, specijalnost) VALUES
('Marko', 'Jovanović', NULL,
 'Marko je licencirani personal trener sa 8 godina iskustva u powerliftingu i snažnom treningu. Radio je sa sportistima različitih nivoa, od početnika do takmičara. Njegovi treninzi su intenzivni ali sistematični — uvek sa fokusom na pravilnu tehniku i dugoročan napredak.',
 'Powerlifting, Snažni trening, Dijeta'),
('Ana', 'Petrović', NULL,
 'Ana je sertifikovani instruktor yoge (RYT 500) i pilatesa sa 5 godina iskustva. Veruje da je fizičko i mentalno zdravlje neraskidivo povezano. Njeni časovi su mirni, fokusirani i dostupni svim uzrastima.',
 'Yoga, Pilates, Fleksibilnost, Meditacija'),
('Ivan', 'Nikolić', NULL,
 'Ivan je CrossFit trener Level 2 sertifikacije sa pozadinom u vojnoj fizičkoj pripremi. Poznat po kreativnim i varijabilnim treninzima koji nikad ne postanu monotoni. Specijalizovan za funkcionalni trening i HIIT programe.',
 'CrossFit, HIIT, Funkcionalni trening');

INSERT INTO vezbe (naziv, opis, slika, grupa_misica, tip_vezbe, tezina) VALUES
('Bench Press',
 'Ležeće potiskivanje je osnovna compound vežba za razvoj grudi, ramena i tricepsa. Lezite na ravnu klupu, šipku hvatajte malo šire od širine ramena. Spuštajte šipku kontrolisano do grudi, zatim snažno potiskujte gore. Leđa su ravna, stopala čvrsto na podu. Izbegavajte odbijanje od grudi.',
 NULL, 'Grudi, Tricepsi, Ramena', 'Slobodni tegovi', 'srednje'),
('Deadlift',
 'Mrtvo dizanje je kralj svih vežbi i angažuje skoro sve mišićne grupe. Stanite ispred šipke sa stopalima u širini kukova. Savijte se u kukovima i kolenima, leđa ravna, hvatajte šipku malo šire od ramena. Dižete šipku pritiskom nogu u pod (ne vučenjem leđima). Vežba zahteva odličnu tehniku — obavezno konsultovati trenera pre početka.',
 NULL, 'Leđa, Noge, Gluteus, Core', 'Slobodni tegovi', 'tesko'),
('Squat',
 'Čučanj je osnovna vežba za razvoj nogu i zadnjice. Šipka leži na gornjim leđima (trapezi), noge su u širini ramena ili malo šire. Savijate kolena i kukove istovremeno, spuštate se dok bedra nisu paralelna sa podom ili ispod. Kolena prate pravac prstiju, ne smeju padati unutra.',
 NULL, 'Noge, Gluteus, Core', 'Slobodni tegovi', 'tesko'),
('Pull-up',
 'Zgibovi na šipci su jedna od najefikasnijih vežbi za razvoj leđa i bicepsa. Hvatajte šipku pronacijom (dlanovi od vas), ruke malo šire od ramena. Vučite telo gore kontrakcijem leđnih mišića dok brada ne prođe iznad šipke, zatim se spuštajte polako i kontrolisano. Nemojte da se ljuljate.',
 NULL, 'Leđa, Bicepsi, Core', 'Vlastita težina', 'srednje'),
('Overhead Press',
 'Stojeće guranje šipke iznad glave razvija ramena i tricepse. Šipka kreće sa visine ključnih kostiju (rack grip), laktovi su blago ispred šipke. Pritiskujte direktno gore dok ruke nisu potpuno ispružene, glava malo nazad da propustite šipku. Na vrhu šipka je iznad glave, ne ispred.',
 NULL, 'Ramena, Tricepsi, Core', 'Slobodni tegovi', 'srednje'),
('Bicep Curl',
 'Klasično savijanje za bicepse sa bučicama ili šipkom. Stanite uspravno, laktovi uz telo. Savijajte laktove i dižite teret prema ramenima supinacijom podlaktice (dlan gore). Laktovi ne smeju da se kreću napred — to prebacuje rad na ramena. Spuštajte polako za bolji efekat.',
 NULL, 'Bicepsi, Podlaktice', 'Slobodni tegovi', 'lako'),
('Plank',
 'Plank je statička vežba koja izuzetno jača core mišiće. Oslonite se na podlaktice i prste nogu, telo je ravno kao daska od glave do pete. Zadnjica nije ni podignuta ni spuštena, stomak uvučen. Dišite normalno i zadržite poziciju što duže možete. Počnite sa 20-30 sekundi, napredujte postepeno.',
 NULL, 'Core, Trbuh, Leđa', 'Vlastita težina', 'lako');

INSERT INTO casovi (naziv, trener_id, opis, dan_u_nedelji, vreme, kapacitet, aktivan) VALUES
('Jutarnja Yoga', 2,
 'Opuštajuća jutarnja yoga sesija za sve nivoe. Fokus na disanje, rastezanje i mentalni mir. Odlično za buđenje tela i postavljanje pozitivnog tona dana. Nema prethodnog iskustva potrebno.',
 'Ponedeljak', '08:00:00', 15, 1),
('CrossFit HIIT', 3,
 'Intenzivan funkcionalni trening koji kombinuje cardio i snagu. Svaki čas je drugačiji — nema dosade! Program se menja nedeljno. Preporučuje se za napredne vežbače sa solidnom tehničkom bazom.',
 'Utorak', '18:00:00', 12, 1),
('Powerlifting Osnove', 1,
 'Naučite pravilnu tehniku squat, bench press i deadlift vežbi pod nadzorom iskusnog trenera. Idealno za početnike i one koji žele popraviti tehniku. Svaki polaznik dobija individualni feedback.',
 'Sreda', '17:00:00', 10, 1),
('Večernji Pilates', 2,
 'Pilates čas fokusiran na core snagu, ravnotežu i fleksibilnost. Odlično za oporavak od napornih treninga i prevenciju povreda. Doneti sopstvenu prostirku. Sve starosti dobrodošle.',
 'Četvrtak', '19:30:00', 15, 1);

INSERT INTO clanarine (naziv, cena, trajanje_dana, opis, aktivan) VALUES
('Osnovna', 2500.00, 30,
 'Mesečna članarina sa pristupom teretani svakog radnog dana (Pon-Pet) od 06:00 do 22:00. Upotreba svih sprava, slobodnih tegova i kardio opreme.',
 1),
('Standardna', 6500.00, 90,
 'Tromesečna članarina sa neograničenim pristupom 7 dana u nedelji od 06:00 do 23:00. Uključuje 2 grupna časa mesečno po izboru. Idealno za redovne vežbače koji žele uštedu.',
 1),
('Premium', 18000.00, 365,
 'Godišnja VIP članarina sa neograničenim pristupom, svim grupnim časovima bez ograničenja, jednim personalnim treningom mesečno i početnom nutritivnom konsultacijom. Naša najpopularnija opcija za ozbiljne vežbače.',
 1);

INSERT INTO ankete (pitanje, aktivan) VALUES
('Koji dan u nedelji preferujete za intenzivni trening?', 1),
('Koja oprema vam je najvažnija u teretani?', 1);

INSERT INTO anketa_odgovori (anketa_id, tekst_odgovora, broj_glasova) VALUES
(1, 'Ponedeljak', 14),
(1, 'Utorak', 9),
(1, 'Sreda', 11),
(1, 'Petak', 18),
(1, 'Subota', 22),
(2, 'Slobodni tegovi (šipke, bučice)', 28),
(2, 'Sprave za snagu', 12),
(2, 'Kardio oprema (trakovi, bicikli)', 9),
(2, 'Funkcionalna zona (TRX, vreće)', 8);
