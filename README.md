# Šark nameštaj po meri

Web aplikacija za stolarsku firmu „Šark nameštaj po meri" — prezentacioni sajt sa
katalogom radova i sistemom za slanje upita. Firma izrađuje nameštaj po meri od
pločastog materijala (iverica i medijapan).

Rađeno u PHP-u i MySQL-u, po MVC obrascu, bez frameworka.

## Tehnologije

- PHP 8 (MVC, PDO)
- MySQL / MariaDB
- HTML, CSS, JavaScript (Ajax preko `fetch`)
- Bootstrap 5 (admin deo)
- Apache (mod_rewrite)

Fontovi i Bootstrap su uključeni lokalno, pa sajt radi i bez interneta.

## Pokretanje (XAMPP)

1. Prekopirati folder `sark-namestaj` u `C:\xampp\htdocs\`.
2. Pokrenuti **Apache** i **MySQL** iz XAMPP kontrolne table.
3. Otvoriti `http://localhost/phpmyadmin` → kartica **Uvoz** → izabrati `sql/sema.sql` → pokrenuti.
   Time se kreira baza `sark_namestaj` sa svim tabelama i početnim podacima.
4. Otvoriti `http://localhost/sark-namestaj/`.

Podaci za konekciju na bazu su u `config/config.php` (podrazumevano `root`, bez lozinke).

## Prijava

| Uloga         | Korisničko ime | Lozinka      |
|---------------|----------------|--------------|
| Administrator | `admin`        | `admin123`   |
| Klijent       | `marko`        | `klijent123` |

Admin panel: `http://localhost/sark-namestaj/admin`

## Funkcionalnosti

- Katalog radova sa filtriranjem po kategoriji **bez osvežavanja strane (Ajax)**
- Strana pojedinačnog rada sa galerijom i formom za upit
- Registracija i prijava (lozinke se **hešuju**); klijent prati status svojih upita
- Admin panel: unos i izmena kategorija i radova, **otpremanje slika preko web servisa**,
  pregled upita i **promena statusa preko Ajax-a**, odobravanje recenzija
- JSON API (`/api/radovi`, `/api/upiti`, `/api/recenzije`, `/api/slike`, `/api/upiti/{id}`)
- **Potrošnja spoljnog web servisa** — kurs EUR→RSD (sa rezervnom vrednošću ako servis nije dostupan)

## Bezbednost

- PDO pripremljeni upiti — zaštita od SQL injection-a
- `password_hash` / `password_verify` za lozinke
- CSRF token na svim formama, provera uloge na admin rutama
- `htmlspecialchars` na izlazu — zaštita od XSS-a
- Provera tipa i veličine slika pri otpremanju; folderi `config/`, `app/`, `sql/` zaštićeni preko `.htaccess`

## Struktura

```
sark-namestaj/
├─ index.php          ulazna tačka (front controller)
├─ routes.php         definicije ruta
├─ config/config.php  podešavanja (baza, upload limiti)
├─ sql/sema.sql       šema baze + početni podaci
├─ app/
│  ├─ Core/           Database (PDO), Router, Controller, Model
│  ├─ Controllers/    Home, Stranice, Radovi, Upit, Auth, Nalog, Admin, Api
│  ├─ Models/         Korisnik, Kategorija, Rad, Slika, Upit, Recenzija
│  ├─ Services/       KursServis (spoljni web servis)
│  └─ Views/          šabloni (layout, home, radovi, stranice, auth, nalog, admin)
└─ assets/            css, js, fontovi, slike
```

Tabele u bazi nose sufiks `_ls` (`korisnici_ls`, `kategorije_ls`, `radovi_ls`, `slike_ls`, `upiti_ls`, `recenzije_ls`).
