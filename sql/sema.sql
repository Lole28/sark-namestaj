-- ============================================================
--  Šark nameštaj po meri — šema baze i početni podaci
--  Uvoz: phpMyAdmin -> Uvoz -> ovaj fajl
--  ili:  mysql --default-character-set=utf8mb4 -u root < sql/sema.sql
--  Tabele nose sufiks _ls.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sark_namestaj`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sark_namestaj`;

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

CREATE TABLE `korisnici_ls` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `korisnicko_ime` VARCHAR(30)  NOT NULL UNIQUE,
    `ime`            VARCHAR(120) NOT NULL,
    `email`          VARCHAR(160) NOT NULL UNIQUE,
    `lozinka_hash`   VARCHAR(255) NOT NULL,
    `uloga`          ENUM('admin','klijent') NOT NULL DEFAULT 'klijent',
    `kreiran`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kategorije_ls` (
    `id`    INT AUTO_INCREMENT PRIMARY KEY,
    `naziv` VARCHAR(120) NOT NULL,
    `slug`  VARCHAR(140) NOT NULL UNIQUE,
    `opis`  TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `radovi_ls` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `kategorija_id` INT NOT NULL,
    `naziv`         VARCHAR(160) NOT NULL,
    `slug`          VARCHAR(180) NOT NULL UNIQUE,
    `opis`          TEXT NOT NULL,
    `materijal`     VARCHAR(200) NULL,
    `cena_od`       DECIMAL(10,2) NULL,
    `istaknut`      TINYINT(1) NOT NULL DEFAULT 0,
    `kreiran`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_rad_kategorija` FOREIGN KEY (`kategorija_id`)
        REFERENCES `kategorije_ls`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_rad_kat` (`kategorija_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Lozinke: admin => "admin123", marko => "klijent123"
INSERT INTO `korisnici_ls` (`korisnicko_ime`, `ime`, `email`, `lozinka_hash`, `uloga`) VALUES
('admin', 'Administrator',  'admin@sark-namestaj.rs', '$2y$10$7Si4iQsgSQaqViCXdqNu/eIOPkqbld3.XlGHCnElnyDJkvvUKEaZy', 'admin'),
('marko', 'Marko Marković', 'marko@example.com',      '$2y$10$9FMM2tzyFtDEEZ8tNMuc7etM0RMKJhXdTvrCyochF6Yc6gmcsYbT2', 'klijent');

INSERT INTO `kategorije_ls` (`naziv`, `slug`, `opis`) VALUES
('Kuhinje',                 'kuhinje',                 'Kuhinje po meri od iverice i medijapana — mat i visoki sjaj, u svakom dekoru, sa kvalitetnim okovom i prigušenjem.'),
('Plakari i garderoberi',   'plakari-i-garderoberi',   'Ugradni plakari, garderoberi, komode i kreveti sa spremištem — iskoristimo svaki centimetar prostora.'),
('Dnevni boravak',          'dnevni-boravak',          'TV komode, klub stolovi, police, radni kutci i lamela zidovi — u usklađenom dezenu.'),
('Kupatila i predsoblja',   'kupatila-i-predsoblja',   'Vodootporni elementi za kupatilo i kompaktna rešenja za hodnik i predsoblje.'),
('Poslovni prostori',       'poslovni-prostori',       'Pultovi, police i kompletni enterijeri za lokale, kancelarije i ugostiteljske objekte.');

INSERT INTO `radovi_ls` (`kategorija_id`, `naziv`, `slug`, `opis`, `materijal`, `cena_od`, `istaknut`) VALUES
(1, 'L kuhinja u belom i dekoru hrasta', 'l-kuhinja-belo-hrast',
 'Ugaona kuhinja sa belim mat frontovima i gornjim elementima u dekoru hrasta. Radna ploča i zidna obloga u istom dezenu drveta, LED rasveta ispod visećih elemenata, fioke sa punim izvlačenjem i prigušenjem.',
 'Iverica 18 mm, ABS kant 2 mm, bela mat folija + dekor hrast, okov sa prigušenjem', 165000, 1),
(1, 'Bela kuhinja sa šankom i vinotekom', 'bela-kuhinja-sank-vinoteka',
 'U kuhinja sa produženim šankom, belim MDF frontovima i mermernom zidnom oblogom. Ugaona polica-vinoteka sa X pregradama i otvorene police, radna ploča u dekoru hrasta.',
 'MDF vrata bela, iverica u dekoru hrast, zidna obloga imitacija mermera', 210000, 1),
(1, 'Linijska kuhinja, belo i antracit', 'linijska-kuhinja-belo-antracit',
 'Jednoredna kuhinja sa visokim elementima do plafona, kombinacija bele i antracit folije. Kolona za ugradnu rernu i frižider, bez vidljivih ručki.',
 'Iverica 18 mm, bela + antracit folija, ABS kant', 132000, 0),
(1, 'Bež mat kuhinja sa kamenom oblogom', 'bez-mat-kuhinja-kamen',
 'Ugaona kuhinja u toplom bež mat dekoru, sa radnom pločom i zidnom oblogom u imitaciji svetlog kamena. Visoka kolona sa nišom i otvorena polica.',
 'Iverica u bež mat dekoru, radna ploča i obloga imitacija kamena', 158000, 0),
(1, 'Kuhinja u niši, antracit i hrast', 'kuhinja-nisa-antracit-hrast',
 'Kuhinja ugrađena u zidnu nišu, sa donjim elementima u antracit visokom sjaju i gornjim u dekoru hrasta. Staklena vitrina, ugradni aparati i LED rasveta.',
 'Iverica 18 mm, antracit visoki sjaj + dekor hrast, staklo, LED', 176000, 1),
(1, 'Klasična bela kuhinja sa tamnom pločom', 'klasicna-bela-kuhinja-tamna-ploca',
 'Kuhinja klasičnog stila sa profilisanim belim vratima i tamnom radnom pločom i oblogom u dezenu mermera. Ugaona postavka sa visećim vitrinama sa staklom.',
 'MDF profilisana vrata, bela folija, radna ploča dezen tamnog mermera', 150000, 0),
(1, 'L kuhinja u krem visokom sjaju', 'l-kuhinja-krem-sjaj',
 'Ugaona kuhinja sa krem frontovima u visokom sjaju i radnom pločom u dekoru hrasta. Kompaktno rešenje za manji prostor, sa ugradnom rernom u koloni.',
 'Iverica 18 mm, krem visoki sjaj, radna ploča dekor hrast', 140000, 0),
(1, 'Mala kuhinja u niši, siva i hrast', 'mala-kuhinja-nisa-siva-hrast',
 'Kompaktna kuhinja u niši, sa sivim visokim sjajem i dekorom hrasta, staklenim gornjim vitrinama i ugradnim Bosch aparatima. Idealna za garsonjere i stanove za izdavanje.',
 'Iverica 18 mm, siva visoki sjaj + dekor hrast, staklo', 128000, 1),
(1, 'Maslinasto zelena kuhinja', 'maslinasto-zelena-kuhinja',
 'Jednoredna kuhinja u mat maslinasto zelenom dekoru, sa radnom pločom u imitaciji kamena i visokom crnom kolonom sa X-vinotekom.',
 'Iverica u mat maslinastom dekoru, radna ploča imitacija kamena', 172000, 0),
(1, 'Bež kuhinja sa staklenom vitrinom', 'bez-kuhinja-staklena-vitrina',
 'Kuhinja sa bež frontovima sa suptilnim graviranim dezenom, sivom radnom pločom i ugaonom vitrinom sa staklenim policama. Šank deo sa dodatnim odlaganjem.',
 'Iverica sa graviranim dezenom, bež sjaj, staklene police', 168000, 1),
(1, 'Kuhinja sa ostrvom, ljubičasta i bela', 'kuhinja-ostrvo-ljubicasta-bela',
 'Kuhinja sa kuhinjskim ostrvom, kombinacija ljubičastog i belog visokog sjaja. Ostrvo i zidna obloga u dezenu mermera, visoke staklene vitrine sa osvetljenjem sa strane.',
 'Iverica 18 mm, ljubičasti + beli visoki sjaj, ploča dezen mermera', 205000, 0),
(1, 'Bela kuhinja, mermerni zid', 'bela-kuhinja-mermerni-zid',
 'Jednoredna bela kuhinja sa graviranim dezenom na vratima i radnom pločom u imitaciji belog mermera, uklopljena uz mermernu zidnu oblogu.',
 'Iverica sa graviranim dezenom, bela, ploča imitacija mermera', 135000, 0),
(2, 'Trokrilni ormar u dekoru oraha', 'trokrilni-ormar-orah',
 'Klasičan trokrilni ormar u toplom dekoru oraha, sa metalnim ručkama. Unutrašnjost sa policama, šipkom za vešalice i prostorom za posteljinu.',
 'Iverica 18 mm, dekor orah, ABS kant, metalne ručke', 42000, 1),
(2, 'Pregradni plakar sa vitrinom i komodom', 'pregradni-plakar-vitrina-komoda',
 'Plakar koji deli prostor kuhinje i hodnika: donji deo zatvoren, gornji sa staklenim vratima kao vitrina, uz uklopljenu komodu sa fiokama. Sve u dekoru hrasta.',
 'Iverica 18 mm, dekor hrast, staklena vrata, prigušene fioke', 78000, 0),
(2, 'Krevet sa spremištem i noćni ormarići', 'krevet-spremiste-nocni-ormarici',
 'Bračni krevet 160×200 sa velikim spremištem u podnožju i dva noćna ormarića sa fiokama. Kombinacija dekora hrasta i belih fioka.',
 'Iverica 18 mm, dekor hrast + bela folija, fioke sa vođicama', 54000, 1),
(2, 'Most-plakar iznad kreveta', 'most-plakar-iznad-kreveta',
 'Nadgradnja iznad kreveta („most”) sa plakarom sa obe strane, u sivom visokom sjaju i dekoru hrasta. Tapacirano uzglavlje, LED rasveta i lebdeći noćni ormarići.',
 'Iverica 18 mm, siva visoki sjaj + dekor hrast, tapacirano uzglavlje, LED', 96000, 0),
(3, 'TV zid sa lamelama i LED rasvetom', 'tv-zid-lamele-led',
 'Lebdeća bela TV komoda uz dekorativni zid sa crnim lamelama i osvetljenom niši sa pozadinskim LED-om. Čiste linije, bez vidljivih ručki.',
 'Iverica 18 mm, bela folija, MDF lamele, LED traka', 62000, 1),
(3, 'TV komoda i klub sto, siva i hrast', 'tv-komoda-klub-sto-siva-hrast',
 'Set za dnevni boravak: lebdeća TV komoda i klub sto sa fiokom i otvorenom nišom, u usklađenom dezenu hrasta i sive.',
 'Iverica 18 mm, dekor hrast + siva, prigušene fioke', 48000, 0),
(3, 'Trpezarijski sto sa lamela zidom', 'trpezarijski-sto-lamela-zid',
 'Trpezarijski sto sa pločom u dekoru hrasta i crnim metalnim nogama, sa uklopljenim lamela zidom iza koji vizuelno deli prostor.',
 'Ploča iverica 36 mm dekor hrast, metalne noge, zidne lamele', 33000, 0),
(3, 'Radni kutak sa policama i ormarom', 'radni-kutak-police-ormar',
 'Kućna kancelarija: lebdeći radni sto, skrivene lebdeće police i ormar sa staklenim vratima, u kombinaciji dekora hrasta i oraha.',
 'Iverica 18 mm, dekor hrast/orah, skriveni nosači polica, staklo', 44000, 1),
(4, 'Predsoblje sa klupom i lamelama', 'predsoblje-klupa-lamele',
 'Kompaktno predsoblje: beli visoki elementi za odlaganje, siva klupa-cipelarnik i zidne lamele. Rešenje za uzak hodnik.',
 'Iverica 18 mm, bela + siva folija, MDF lamele', 46000, 1),
(4, 'Kupatilski nameštaj sa otvorenim policama', 'kupatilski-namestaj-otvorene-police',
 'Set za kupatilo: visoki beli ormarić, otvorene police u dekoru drveta i komoda ispod nadgradnog lavaboa. Otporno na vlagu.',
 'Vodootporna ploča, bela folija + dekor drvo, ABS kant', 28000, 0),
(5, 'Enterijer pekare i kafeterije', 'enterijer-pekare-kafeterije',
 'Kompletan enterijer ugostiteljskog objekta: pultovi i police obložene dekorom oraha, uklopljene staklene rashladne vitrine, zidne obloge i barski deo sa sedenjem.',
 'Iverica 25 mm, HPL i dekor orah, ugradnja rashladnih vitrina', 320000, 1);

INSERT INTO `slike_ls` (`rad_id`, `putanja`, `alt_tekst`, `redosled`) VALUES
(1, 'img/portfolio/p01.jpg', 'L kuhinja belo i dekor hrast, pogled iz ugla', 1),
(1, 'img/portfolio/p02.jpg', 'L kuhinja, detalj radne ploče i LED rasvete', 2),
(2, 'img/portfolio/p03.jpg', 'Bela kuhinja sa šankom i X-vinotekom', 1),
(2, 'img/portfolio/p07.jpg', 'Bela kuhinja, mermerna zidna obloga i otvorene police', 2),
(3, 'img/portfolio/p05.jpg', 'Linijska kuhinja belo i antracit', 1),
(4, 'img/portfolio/p08.jpg', 'Bež mat kuhinja sa kamenom oblogom', 1),
(5, 'img/portfolio/p10.jpg', 'Kuhinja u niši, antracit i hrast, sa trpezarijom', 1),
(6, 'img/portfolio/p12.jpg', 'Klasična bela kuhinja, tamna radna ploča', 1),
(6, 'img/portfolio/p13.jpg', 'Klasična bela kuhinja, ugaona postavka', 2),
(7, 'img/portfolio/p15.jpg', 'L kuhinja krem visoki sjaj', 1),
(8, 'img/portfolio/p18.jpg', 'Mala kuhinja u niši, siva i hrast', 1),
(8, 'img/portfolio/p19.jpg', 'Mala kuhinja u niši, pogled ka hodniku', 2),
(9, 'img/portfolio/p21.jpg', 'Maslinasto zelena kuhinja, gornji elementi', 1),
(9, 'img/portfolio/p22.jpg', 'Maslinasto zelena kuhinja sa X-vinotekom', 2),
(10, 'img/portfolio/p23.jpg', 'Bež kuhinja sa graviranim dezenom', 1),
(10, 'img/portfolio/p24.jpg', 'Bež kuhinja, ugaona postavka', 2),
(10, 'img/portfolio/p25.jpg', 'Bež kuhinja sa staklenom vitrinom i šankom', 3),
(11, 'img/portfolio/p29.jpg', 'Kuhinja sa ostrvom, ljubičasta i bela', 1),
(12, 'img/portfolio/p32.jpg', 'Bela kuhinja uz mermerni zid', 1),
(13, 'img/portfolio/p30.jpg', 'Trokrilni ormar u dekoru oraha', 1),
(14, 'img/portfolio/p11.jpg', 'Pregradni plakar sa vitrinom i komodom', 1),
(15, 'img/portfolio/p04.jpg', 'Krevet sa spremištem i noćni ormarići', 1),
(16, 'img/portfolio/p20.jpg', 'Most-plakar iznad kreveta sa LED rasvetom', 1),
(17, 'img/portfolio/p14.jpg', 'TV zid sa lamelama i LED pozadinskim osvetljenjem', 1),
(18, 'img/portfolio/p16.jpg', 'TV komoda i klub sto, siva i hrast', 1),
(18, 'img/portfolio/p17.jpg', 'Dnevni boravak, TV komoda i klub sto', 2),
(19, 'img/portfolio/p09.jpg', 'Trpezarijski sto sa lamela zidom', 1),
(20, 'img/portfolio/p31.jpg', 'Radni kutak sa lebdećim policama i ormarom', 1),
(21, 'img/portfolio/p06.jpg', 'Predsoblje sa klupom i zidnim lamelama', 1),
(22, 'img/portfolio/p26.jpg', 'Kupatilski nameštaj sa otvorenim policama', 1),
(23, 'img/portfolio/p27.jpg', 'Enterijer pekare, pultovi obloženi dekorom oraha', 1),
(23, 'img/portfolio/p28.jpg', 'Rashladne vitrine i barski deo pekare', 2);

INSERT INTO `recenzije_ls` (`ime`, `ocena`, `tekst`, `odobrena`) VALUES
('Jelena S.',      5, 'Kuhinju su nam napravili tačno po meri za nezgodan ugao. Dekor hrasta je prelep, fioke se same dovlače. Sve dogovoreno na početku i ispoštovano.', 1),
('Nenad i Ana',    5, 'Porodična firma u pravom smislu — dolazili i vikendom da bi montaža bila gotova na vreme. Plakar za kosi plafon niko drugi nije hteo da radi.', 1),
('Milan P.',       4, 'Kvalitet izrade i kantovanja je odličan. Malo su probili rok za jedan element, ali su korektno izašli u susret.', 1),
('Tijana M.',      5, 'Predsoblje i kupatilski nameštaj od medijapana — sve besprekorno uklopljeno u prostor. Preporuka za svakoga ko traži nameštaj po meri.', 1),
('Pekara „Klas”',  5, 'Opremili su nam kompletan enterijer pekare. Rokovi, komunikacija i završna obrada na nivou.', 1),
('Aleksandar V.',  5, '3D prikaz pre izrade mi je mnogo pomogao da se odlučim. Kada je nameštaj stigao, izgledao je identično kao na slici.', 0);

INSERT INTO `upiti_ls` (`rad_id`, `korisnik_id`, `ime`, `email`, `telefon`, `poruka`, `status`) VALUES
(1, 2, 'Marko Marković', 'marko@example.com', '064 111 2233', 'Zanima me slična L kuhinja za prostor 3,2 × 1,8 m, dekor hrasta. Da li je moguća poseta radi mere sledeće nedelje?', 'u_obradi'),
(NULL, NULL, 'Sofija Ilić', 'sofija@example.com', '063 444 5566', 'Potreban mi je ugradni plakar u hodniku, dužina oko 2 m, medijapan bele boje. Molim okvirnu cenu.', 'nov');
