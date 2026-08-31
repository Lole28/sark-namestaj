# Šark nameštaj po meri — MVC web aplikacija

Studentski projekat: prezentacioni sajt i sistem za upite stolarske firme
„Šark nameštaj po meri”. Izrađen u **PHP-u (8.x) i MySQL-u** uz sopstvenu
MVC arhitekturu, bez teških biblioteka. Bootstrap 5 je uključen lokalno
(u `assets/`), tako da aplikacija radi i bez interneta.

---

## Pokretanje kroz XAMPP (preporučeno)

1. Kopiraj ceo folder `sark-namestaj` u `C:\xampp\htdocs\`.
2. U XAMPP kontrolnoj tabli pokreni **Apache** i **MySQL**.
3. Uvezi bazu:
   - otvori `http://localhost/phpmyadmin`
   - kartica **Uvoz (Import)** → izaberi fajl `sql/sema.sql` → **Kreni (Go)**
   - (fajl sam kreira bazu `sark_namestaj` sa svim tabelama i početnim podacima)
4. Otvori aplikaciju: **`http://localhost/sark-namestaj/`**

> Podešavanja baze su u `config/config.php`. Podrazumevano je `root` bez lozinke
> (standardni XAMPP). Ako si menjao MySQL lozinku, upiši je tamo.

### Brzi test bez Apache-a

```
cd putanja\do\sark-namestaj
C:\xampp\php\php.exe -S localhost:8000 router.php
```
pa otvori `http://localhost:8000/`. (MySQL i dalje mora biti pokrenut.)

---

## Kredencijali za prijavu

| Uloga   | Korisničko ime | Lozinka      |
|---------|----------------|--------------|
| Admin   | `admin`        | `admin123`   |
| Klijent | `marko`        | `klijent123` |

Admin panel: `http://localhost/sark-namestaj/admin`

---

## Šta aplikacija radi

**Javni deo**
- Početna strana sa izdvojenim radovima i recenzijama
- Katalog radova sa **filtriranjem po kategoriji bez osvežavanja strane (Ajax)**
- Strana pojedinačnog rada sa galerijom slika i formom „Zatraži ponudu”
- Stranice *Usluge*, *O nama*, *Kontakt*
- Slanje upita i recenzije preko **Ajax-a** (rade i bez JavaScript-a)

**Nalog klijenta**
- Registracija, prijava, odjava (lozinke heširane `password_hash` / bcrypt)
- „Moji upiti” — pregled sopstvenih upita i njihovog statusa

**Administracija** (samo uloga `admin`)
- Kontrolna tabla sa brojem upita po statusu
- CRUD nad kategorijama i radovima
- **Upload slika uz rad kroz web servis** (`POST /api/slike`), sa pregledom pre slanja
- Lista upita + **promena statusa preko Ajax-a** (`PATCH /api/upiti/{id}`)
- Odobravanje / brisanje recenzija

**Web servisi (JSON API)**

| Metoda i putanja            | Namena                                            |
|-----------------------------|---------------------------------------------------|
| `GET /api/radovi`           | lista radova (filter `?kategorija=slug`)          |
| `GET /api/kurs`             | dohvata kurs EUR→RSD sa spoljnog servisa i kešira |
| `POST /api/upiti`           | kreiranje upita                                   |
| `POST /api/recenzije`       | kreiranje recenzije                               |
| `POST /api/slike`           | upload slike (admin)                              |
| `PATCH /api/upiti/{id}`     | promena statusa upita (admin)                     |

`GET /api/kurs` je primer **potrošnje tuđeg web servisa** — poziva
`open.er-api.com`, rezultat kešira u `storage/`, a ako nema interneta koristi
rezervnu vrednost iz `config.php`.

---

## MVC struktura

```
sark-namestaj/
├─ index.php            Front controller — jedina ulazna tačka
├─ .htaccess            Rewrite svih ruta na index.php + zaštita foldera
├─ routes.php           Definicije ruta
├─ router.php           (samo za `php -S`, ne koristi se na Apache-u)
├─ config/config.php    Podešavanja (baza, upload limiti, kurs API)
├─ sql/sema.sql         Šema baze + početni podaci
├─ app/
│  ├─ Core/             Database (PDO), Router, Controller, Model
│  ├─ Controllers/      Home, Stranice, Radovi, Upit, Auth, Nalog, Admin, Api
│  ├─ Models/           Korisnik, Kategorija, Rad, Slika, Upit, Recenzija
│  ├─ Services/         KursServis (spoljni web servis)
│  ├─ Views/            Šabloni (layout, home, radovi, auth, nalog, admin…)
│  └─ helpers.php       Pomoćne funkcije (e(), url(), csrf, validacija…)
├─ assets/
│  ├─ css/  js/         Bootstrap 5 (lokalno) + style.css + app.js
│  └─ uploads/          Otpremljene slike (skripte ovde blokirane .htaccess-om)
└─ storage/             Keš kursa
```

Tabele u bazi nose sufiks `_ls` (`korisnici_ls`, `kategorije_ls`, `radovi_ls`,
`slike_ls`, `upiti_ls`, `recenzije_ls`).

---

## Bezbednost

- **SQL injection** — isključivo PDO pripremljeni upiti sa vezanim parametrima
- **Lozinke** — `password_hash(PASSWORD_BCRYPT)` / `password_verify`, nikad čist tekst
- **CSRF** — token po sesiji, skriveno polje u svakoj formi, provera na svakom `POST`/`PATCH`
- **Neovlašćeni pristup** — `zahtevajPrijavu()` / `zahtevajAdmina()` na zaštićenim rutama; klijent vidi samo svoje upite
- **XSS** — `htmlspecialchars` (`e()`) na svakom izlazu
- **Upload** — provera MIME tipa (`finfo`), veličine i `getimagesize`, nasumično ime fajla, zabrana izvršavanja skripti u `assets/uploads/`
- **Sesija** — `httponly`, `samesite=Lax`, `session_regenerate_id` posle prijave
- `config/`, `app/`, `sql/`, `storage/` nedostupni preko browsera (`.htaccess`)

---

## Postavljanje na InfinityFree

1. Napravi bazu u cPanel-u i upiši njene podatke u `config/config.php`
   (`host`, `name`, `user`, `pass`).
2. Uvezi `sql/sema.sql` kroz phpMyAdmin (ili pokreni samo `CREATE TABLE` deo u postojećoj bazi).
3. FTP-om prebaci ceo folder u `htdocs/` (ili u podfolder — aplikacija sama
   prepoznaje osnovnu putanju).
4. U `config.php` postavi `'debug' => false`.
5. Proveri da folder `assets/uploads/` i `storage/` imaju dozvolu za pisanje.

---

## Tehnologije

PHP 8, MySQL / MariaDB, PDO, Bootstrap 5, čist JavaScript (fetch/Ajax), Apache (mod_rewrite).
