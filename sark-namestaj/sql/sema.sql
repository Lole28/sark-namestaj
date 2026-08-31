-- ============================================================
--  Šark nameštaj po meri — šema baze i početni podaci
--  Uvoz: phpMyAdmin -> Uvoz -> izaberi ovaj fajl
--  ili:  mysql -u root < sql/sema.sql
--  Sve tabele nose sufiks _ls (inicijali) da se ne bi preklapale.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sark_namestaj`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sark_namestaj`;

-- Ovaj fajl je snimljen u UTF-8. Ako uvoziš iz komandne linije, koristi:
--   mysql --default-character-set=utf8mb4 -u root < sql/sema.sql
-- (phpMyAdmin prepoznaje UTF-8 automatski.)
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `slike_ls`;
DROP TABLE IF EXISTS `upiti_ls`;
DROP TABLE IF EXISTS `radovi_ls`;
DROP TABLE IF EXISTS `kategorije_ls`;
DROP TABLE IF EXISTS `korisnici_ls`;
DROP TABLE IF EXISTS `recenzije_ls`;
SET FOREIGN_KEY_CHECKS = 1;

-- --- Korisnici -------------------------------------------------
CREATE TABLE `korisnici_ls` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `korisnicko_ime` VARCHAR(30)  NOT NULL UNIQUE,
    `ime`            VARCHAR(120) NOT NULL,
    `email`          VARCHAR(160) NOT NULL UNIQUE,
    `lozinka_hash`   VARCHAR(255) NOT NULL,
    `uloga`          ENUM('admin','klijent') NOT NULL DEFAULT 'klijent',
    `kreiran`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Kategorije ---------------------------------------------
CREATE TABLE `kategorije_ls` (
    `id`    INT AUTO_INCREMENT PRIMARY KEY,
    `naziv` VARCHAR(120) NOT NULL,
    `slug`  VARCHAR(140) NOT NULL UNIQUE,
    `opis`  TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Radovi ------------------------------------------------
CREATE TABLE `radovi_ls` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `kategorija_id` INT NOT NULL,
    `naziv`         VARCHAR(160) NOT NULL,
    `slug`          VARCHAR(180) NOT NULL UNIQUE,
    `opis`          TEXT NOT NULL,
    `materijal`     VARCHAR(160) NULL,
    `cena_od`       DECIMAL(10,2) NULL,
    `istaknut`      TINYINT(1) NOT NULL DEFAULT 0,
    `kreiran`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_rad_kategorija` FOREIGN KEY (`kategorija_id`)
        REFERENCES `kategorije_ls`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_rad_kategorija` (`kategorija_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Slike ------------------------------------------------
CREATE TABLE `slike_ls` (
    `id`        INT AUTO_INCREMENT PRIMARY KEY,
    `rad_id`    INT NOT NULL,
    `putanja`   VARCHAR(255) NOT NULL,
    `alt_tekst` VARCHAR(200) NULL,
    `redosled`  INT NOT NULL DEFAULT 1,
    CONSTRAINT `fk_slika_rad` FOREIGN KEY (`rad_id`)
        REFERENCES `radovi_ls`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_slika_rad` (`rad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Upiti ------------------------------------------------
CREATE TABLE `upiti_ls` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `rad_id`      INT NULL,
    `korisnik_id` INT NULL,
    `ime`         VARCHAR(120) NOT NULL,
    `email`       VARCHAR(160) NOT NULL,
    `telefon`     VARCHAR(40) NULL,
    `poruka`      TEXT NOT NULL,
    `status`      ENUM('nov','u_obradi','zavrsen') NOT NULL DEFAULT 'nov',
    `kreiran`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_upit_rad` FOREIGN KEY (`rad_id`)
        REFERENCES `radovi_ls`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_upit_korisnik` FOREIGN KEY (`korisnik_id`)
        REFERENCES `korisnici_ls`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_upit_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Recenzije ------------------------------------------------
CREATE TABLE `recenzije_ls` (
    `id`       INT AUTO_INCREMENT PRIMARY KEY,
    `ime`      VARCHAR(80) NOT NULL,
    `ocena`    TINYINT NOT NULL,
    `tekst`    TEXT NOT NULL,
    `odobrena` TINYINT(1) NOT NULL DEFAULT 0,
    `kreiran`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  POČETNI PODACI
-- ============================================================

-- Lozinke: admin => "admin123", klijent => "klijent123"
INSERT INTO `korisnici_ls` (`korisnicko_ime`, `ime`, `email`, `lozinka_hash`, `uloga`) VALUES
('admin',  'Administrator',   'admin@sark-namestaj.rs',  '$2y$10$7Si4iQsgSQaqViCXdqNu/eIOPkqbld3.XlGHCnElnyDJkvvUKEaZy', 'admin'),
('marko',  'Marko Marković',  'marko@example.com',       '$2y$10$9FMM2tzyFtDEEZ8tNMuc7etM0RMKJhXdTvrCyochF6Yc6gmcsYbT2', 'klijent');

INSERT INTO `kategorije_ls` (`naziv`, `slug`, `opis`) VALUES
('Kuhinje',           'kuhinje',           'Kuhinje po meri sa kvalitetnim okovom, radnim pločama i mehanizmima za tiho zatvaranje.'),
('Plakari i garderoberi', 'plakari-i-garderoberi', 'Ugradni plakari, garderoberi i predsoblja koji maksimalno iskoriste svaki centimetar.'),
('Kupatilski nameštaj', 'kupatilski-namestaj', 'Vodootporni elementi za kupatilo — komode ispod lavaboa, viseći ormarići, ogledala.'),
('Kancelarija',       'kancelarija',       'Recepcije, radni stolovi, police i ormari za poslovni prostor.'),
('Nameštaj po meri',  'namestaj-po-meri',  'TV komode, vitrine, police, klupe i sve ostalo što se ne uklapa u standardne mere.');

INSERT INTO `radovi_ls` (`kategorija_id`, `naziv`, `slug`, `opis`, `materijal`, `cena_od`, `istaknut`) VALUES
(1, 'Kuhinja u hrastu sa antracit frontovima', 'kuhinja-hrast-antracit',
 'L kuhinja 3,6 + 2,1 m sa ostrvom. Donji elementi u dekoru hrasta, gornji u mat antracit foliji. Fioke sa punim izvlačenjem i amortizerima, sudopera od granita, LED rasveta ispod visećih elemenata.',
 'Egger ploča 18 mm, ABS kant 2 mm, Blum okov', 185000, 1),
(1, 'Bela mat kuhinja sa ostrvom', 'bela-mat-kuhinja-ostrvo',
 'Moderna kuhinja bez rukohvata (grip-less), sa integrisanim ostrvom 1,8 m i barskim delom. Radna ploča imitacija betona, ugradni aparati.',
 'MDF lakiran mat, radna ploča Kronospan 38 mm', 240000, 1),
(1, 'Kuhinja u jasenu, ravne linije', 'kuhinja-jasen-ravne-linije',
 'Jednoredna kuhinja 4,2 m u prirodnom dekoru jasena. Visoki elementi do plafona, kolona za ugradnu rernu i mikrotalasnu.',
 'Iverica u dekoru jasena, front 18 mm', 156000, 0),
(2, 'Ugradni plakar sa kliznim vratima', 'ugradni-plakar-klizna-vrata',
 'Plakar 2,6 x 2,5 m sa tri klizna krila (ogledalo u sredini). Unutrašnjost: police, dve šipke, fioke i prostor za ranac/kofere.',
 'Iverica 18 mm, sistem kliznih vrata SevrollSystem', 78000, 1),
(2, 'Garderober u orahu sa LED rasvetom', 'garderober-orah-led',
 'Otvoreni garderober u spavaćoj sobi, 3 m širine, sa senzorskom LED rasvetom i staklenim fiokama sa strane.',
 'Dekor orah natur, staklo lakobel', 132000, 0),
(2, 'Predsoblje sa klupom i ogledalom', 'predsoblje-klupa-ogledalo',
 'Kompaktno predsoblje 1,9 m: garderobni deo sa vešalicama, klupa za obuvanje sa fiokama i veliko ogledalo.',
 'Iverica u dekoru hrast sonoma, tapacirano sedište', 46000, 0),
(3, 'Komoda ispod lavaboa sa vodootpornim frontovima', 'komoda-ispod-lavaboa',
 'Viseća komoda 80 cm sa dve fioke oko sifona i integrisanim rukohvatom. Otporna na vlagu.',
 'Vodootporna ploča, front MDF lak', 28000, 0),
(3, 'Ogledalo sa ormarićem i utičnicom', 'ogledalo-sa-ormaricem',
 'Ormarić sa ogledalskim vratima 70 x 65 cm, unutrašnja utičnica i LED traka po obodu.',
 'Vodootporna ploča, ogledalo 4 mm', 22000, 0),
(4, 'Recepcija sa osvetljenim logo panelom', 'recepcija-logo-panel',
 'Pultna recepcija 2,4 m u dva nivoa, sa panelom za logo firme i LED pozadinskim osvetljenjem. Prolaz za kablove i mesto za CPU.',
 'Iverica 25 mm, HPL obloga, LED profil', 168000, 1),
(4, 'Radni sto po meri sa kablovskim kanalom', 'radni-sto-kablovski-kanal',
 'Radni sto 160 x 80 cm sa metalnim nogama, kablovskim kanalom ispod ploče i pokretnom kasetom sa tri fioke.',
 'Ploča 28 mm hrast, metalne noge crne', 39000, 0),
(4, 'Zidna polica — masiv hrast', 'zidna-polica-masiv-hrast',
 'Set od tri police po 1,8 m od masiva hrasta, ulje-vosak zaštita, skriveni nosači.',
 'Hrast masiv 40 mm, ulje Osmo', 24500, 0),
(5, 'TV komoda sa fiokama i nišom', 'tv-komoda-fioke-nisa',
 'Lebdeća TV komoda 2,2 m sa tri fioke push-to-open i otvorenom nišom za risiver.',
 'Iverica dekor hrast, front mat crni', 34000, 1),
(5, 'Vitrina sa staklom i rasvetom', 'vitrina-staklo-rasveta',
 'Visoka vitrina 60 x 200 cm sa staklenim vratima, staklenim policama i LED rasvetom, za trpezariju.',
 'Iverica orah, staklo prohrom okov', 41000, 0),
(5, 'Klupa za trpezariju od jasenovog masiva', 'klupa-trpezarija-jasen',
 'Klupa 1,6 m uz trpezarijski sto, od jasenovog masiva, sa blagom obradom ivica.',
 'Jasen masiv 40 mm, ulje-vosak', 18500, 0);

INSERT INTO `slike_ls` (`rad_id`, `putanja`, `alt_tekst`, `redosled`) VALUES
(1, 's1.svg', 'Kuhinja hrast i antracit, pogled iz ugla', 1),
(1, 's2.svg', 'Detalj ostrva', 2),
(2, 's2.svg', 'Bela mat kuhinja sa ostrvom', 1),
(2, 's3.svg', 'Kolona sa ugradnim aparatima', 2),
(3, 's3.svg', 'Kuhinja u jasenu', 1),
(4, 's4.svg', 'Ugradni plakar sa kliznim vratima', 1),
(4, 's6.svg', 'Unutrašnjost plakara', 2),
(5, 's5.svg', 'Garderober u orahu', 1),
(6, 's6.svg', 'Predsoblje sa klupom', 1),
(7, 's7.svg', 'Komoda ispod lavaboa', 1),
(8, 's8.svg', 'Ogledalo sa ormarićem', 1),
(9, 's9.svg', 'Recepcija sa logo panelom', 1),
(10, 's10.svg', 'Radni sto sa kablovskim kanalom', 1),
(11, 's11.svg', 'Zidne police od hrasta', 1),
(12, 's12.svg', 'TV komoda sa fiokama', 1),
(13, 's13.svg', 'Vitrina sa staklom', 1),
(14, 's14.svg', 'Klupa za trpezariju', 1);

INSERT INTO `recenzije_ls` (`ime`, `ocena`, `tekst`, `odobrena`) VALUES
('Jelena S.',    5, 'Kuhinja je stigla tačno na dogovoreni dan, montaža čista i brza. Sve fioke rade savršeno.', 1),
('Nenad i Ana',  5, 'Napravili su nam plakar po meri za kosi plafon — niko drugi nije hteo da se prihvati. Preporuka!', 1),
('Milan P.',     4, 'Kvalitet izrade odličan, malo su kasnili sa jednim elementom ali su korektno izašli u susret.', 1),
('Tijana',       5, 'Divna komunikacija i 3D prikaz pre izrade mi je mnogo pomogao da se odlučim.', 0);

INSERT INTO `upiti_ls` (`rad_id`, `korisnik_id`, `ime`, `email`, `telefon`, `poruka`, `status`) VALUES
(1, 2, 'Marko Marković', 'marko@example.com', '064 111 2233', 'Zanima me slična kuhinja za prostor 3,2 x 1,8 m. Da li je moguća poseta sledeće nedelje?', 'u_obradi'),
(NULL, NULL, 'Sofija Ilić', 'sofija@example.com', '063 444 5566', 'Potreban mi je ugradni plakar u hodniku, dužina oko 2 m. Molim okvirnu cenu.', 'nov');
