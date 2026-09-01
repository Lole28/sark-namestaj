# Šark nameštaj po meri — MVC web aplikacija

Prezentacioni sajt i sistem za upite porodične stolarske firme „Šark nameštaj po meri”,
koja izrađuje nameštaj po meri od **pločastog materijala (iverica i medijapan)**.

Izrađeno u **PHP-u (8.x) i MySQL-u** uz sopstvenu MVC arhitekturu. Dizajn i animacije
(krem editorijalni stil, scroll-reveal, marquee traka radova, lightbox) rađeni po
uzoru na `youngandnorgate.com`, prilagođeno bojama logoa (antracit + taupe).
Fontovi (Fraunces, Hanken Grotesk) i Bootstrap su uključeni lokalno — sajt radi i bez interneta.

---

## Pokretanje kroz XAMPP

1. Kopiraj folder `sark-namestaj` u `C:\xampp\htdocs\`.
2. U XAMPP kontrolnoj tabli pokreni **Apache** i **MySQL**.
3. Uvezi bazu: `http://localhost/phpmyadmin` → **Uvoz** → `sql/sema.sql` → **Kreni**.
   (fajl sam kreira bazu `sark_namestaj` sa svim tabelama i početnim podacima —
   5 kategorija, 23 rada, 32 fotografije, recenzije).
4. Otvori: **`http://localhost/sark-namestaj/`**

Podešavanja baze su u `config/config.php` (podrazumevano `root` bez lozinke — standardni XAMPP).

### Brzi test bez Apache-a
```
C:\xampp\php\php.exe -S localhost:8000 router.php
```

---

## Kredencijali

| Uloga   | Korisničko ime | Lozinka      |
|---------|----------------|--------------|
| Admin   | `admin`        | `admin123`   |
| Klijent | `marko`        | `klijent123` |

Admin panel: `http://localhost/sark-namestaj/admin`

---

## Logo

Logo se učitava iz **`assets/img/logo.png`** (i `assets/img/logo-mark.png` za favicon).
Trenutno je ubačena obrađena verzija sa providnom pozadinom.
**Da postaviš svoju verziju logoa:** samo prebaci svoj PNG preko te dve datoteke
(najbolje sa providnom pozadinom, uspravan grb). Nigde u kodu ne treba ništa menjati.

---

## Stranice i funkcije

**Javni deo**
- **Početna** — hero, izjava, „O nama”, rotirajuća traka svih radova, recenzije, poziv na akciju.
- **Radovi** — mreža svih radova sa filtrom po kategoriji **bez osvežavanja strane (Ajax)**.
- **Detalj rada** — velika fotografija, specifikacija, galerija (lightbox) i forma za upit.
- **O nama** — priča o porodičnoj firmi (lojalni, istrajni, sve po dogovoru) i o pločastom materijalu.
- **Kontakt** — forma za upit + forma za recenziju (obe preko **Ajax-a**, rade i bez JavaScript-a).
- **Rotirajuća traka radova** — klik na sliku otvara **lightbox** sa uvećanom slikom, kratkim
  opisom i dugmetom **„Kontaktirajte nas”** koje vodi na kontakt stranu (sa unapred popunjenom temom).

**Nalog klijenta** — registracija, prijava, „Moji upiti” sa statusom.

**Administracija** (uloga `admin`)
- Kontrolna tabla (broj upita po statusu).
- CRUD nad kategorijama i radovima.
- **Upload slika uz rad kroz web servis** (`POST /api/slike`), sa pregledom pre slanja.
- Lista upita + **promena statusa preko Ajax-a** (`PATCH /api/upiti/{id}`).
- Odobravanje / brisanje recenzija.

**Web servisi (JSON API)**

| Metoda i putanja        | Namena                                       |
|-------------------------|----------------------------------------------|
| `GET /api/radovi`       | lista radova (filter `?kategorija=slug`)      |
| `GET /api/kurs`         | kurs EUR→RSD sa spoljnog servisa + keš        |
| `POST /api/upiti`       | kreiranje upita                              |
| `POST /api/recenzije`   | kreiranje recenzije                          |
| `POST /api/slike`       | upload slike uz rad (admin)                   |
| `PATCH /api/upiti/{id}` | promena statusa upita (admin)                 |

`GET /api/kurs` je primer **potrošnje tuđeg web servisa** (`open.er-api.com`);
poziva se sa fronta preko `fetch`, rezultat se kešira u `/storage`, a ako nema
interneta koristi se rezervna vrednost iz `config.php`.

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
│  ├─ Views/            Šabloni (layout, home, radovi, stranice, auth, nalog, admin)
│  └─ helpers.php       Pomoćne funkcije (e(), url(), csrf, validacija…)
├─ assets/
│  ├─ css/  js/         Bootstrap 5 (lokalno) + style.css + app.js
│  ├─ fonts/            Fraunces + Hanken Grotesk (woff2, lokalno)
│  ├─ img/portfolio/    32 fotografije radova (pXX.jpg + pXX-t.jpg umanjene)
│  └─ uploads/          Slike otpremljene kroz admin (skripte ovde blokirane)
└─ storage/             Keš kursa
```

Tabele nose sufiks `_ls`: `korisnici_ls`, `kategorije_ls`, `radovi_ls`, `slike_ls`, `upiti_ls`, `recenzije_ls`.

---

## Bezbednost

- **SQL injection** — isključivo PDO pripremljeni upiti sa vezanim parametrima.
- **Lozinke** — `password_hash(PASSWORD_BCRYPT)` / `password_verify`, nikad čist tekst.
- **CSRF** — token po sesiji, provera na svakom `POST`/`PATCH`.
- **Neovlašćeni pristup** — `zahtevajPrijavu()` / `zahtevajAdmina()`; klijent vidi samo svoje upite.
- **XSS** — `htmlspecialchars` (`e()`) na svakom izlazu.
- **Upload** — provera MIME (`finfo`), veličine i `getimagesize`; nasumično ime fajla; zabrana skripti u `assets/uploads/`.
- **Sesija** — `httponly`, `samesite=Lax`, `session_regenerate_id` posle prijave.
- `config/`, `app/`, `sql/`, `storage/` nedostupni preko browsera (`.htaccess`).

---

## Postavljanje na InfinityFree

1. Napravi bazu u cPanel-u i upiši podatke u `config/config.php`.
2. Uvezi `sql/sema.sql` kroz phpMyAdmin.
3. FTP-om prebaci ceo folder u `htdocs/` (aplikacija sama prepoznaje osnovnu putanju).
4. U `config.php` postavi `'debug' => false`.
5. Proveri da `assets/uploads/` i `storage/` imaju dozvolu za pisanje.

---

## Tehnologije

PHP 8, MySQL / MariaDB, PDO, Bootstrap 5 (admin), čist JavaScript (fetch/Ajax,
IntersectionObserver za animacije), Apache (mod_rewrite). Fontovi Fraunces i Hanken Grotesk.
