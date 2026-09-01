<?php

/**
 * Pomoćne funkcije dostupne svuda u aplikaciji.
 */

/** Bezbedno ispisivanje u HTML (zaštita od XSS-a). */
function e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Apsolutna putanja unutar aplikacije. */
function url(string $putanja = ''): string
{
    return BASE_URL . ltrim($putanja, '/');
}

/** Putanja do statičkog resursa u /assets. */
function asset(string $putanja): string
{
    return BASE_URL . 'assets/' . ltrim($putanja, '/');
}

/** URL slike (portfolio ili otpremljena kroz admin). */
function slika_url(?string $ime): string
{
    if (!$ime) {
        return asset('img/logo-mark.png');
    }
    if (str_starts_with($ime, 'img/')) {
        return asset($ime);
    }
    return BASE_URL . $GLOBALS['config']['app']['uploads_url'] . '/' . $ime;
}

/** URL umanjene verzije (za portfolio slike pNN.jpg -> pNN-t.jpg). */
function slika_thumb(?string $ime): string
{
    if ($ime && str_starts_with($ime, 'img/portfolio/') && str_ends_with($ime, '.jpg')) {
        return asset(substr($ime, 0, -4) . '-t.jpg');
    }
    return slika_url($ime);
}

/* --- CSRF ------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/* --- Sesija / korisnik --------------------------------------------------- */

function current_user(): ?array
{
    return $_SESSION['korisnik'] ?? null;
}

function is_admin(): bool
{
    return (current_user()['uloga'] ?? '') === 'admin';
}

function is_ajax(): bool
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}

/* --- Flash poruke ------------------------------------------------------- */

function flash(?string $tip = null, ?string $poruka = null): array
{
    if ($tip !== null && $poruka !== null) {
        $_SESSION['_flash'][] = ['tip' => $tip, 'poruka' => $poruka];
        return [];
    }
    $sve = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $sve;
}

/* --- Zapamćena polja forme (posle neuspešne validacije) --------------- */

function stare_upisi(array $polja): void
{
    $_SESSION['_stare'] = $polja;
}

function stare(?string $kljuc = null): mixed
{
    $s = $_SESSION['_stare'] ?? [];
    if ($kljuc === null) {
        return $s;
    }
    return $s[$kljuc] ?? '';
}

function stare_ocisti(): void
{
    unset($_SESSION['_stare'], $_SESSION['_greske']);
}

/** Greške validacije prenete kroz redirect. */
function greske_upisi(array $greske): void
{
    $_SESSION['_greske'] = $greske;
}

function greska(string $polje): string
{
    return $_SESSION['_greske'][$polje] ?? '';
}

function ima_gresku(string $polje): bool
{
    return isset($_SESSION['_greske'][$polje]);
}

/* --- Prikaz ----------------------------------------------------------- */

/** 12.345,00 -> "12.345 RSD" */
function dinar(float|int|string $iznos): string
{
    return number_format((float) $iznos, 0, ',', '.') . ' RSD';
}

/** Pretvori naslov u URL slug (podržava srpsku latinicu). */
function slugify(string $tekst): string
{
    $mapa = ['č' => 'c', 'ć' => 'c', 'đ' => 'dj', 'š' => 's', 'ž' => 'z',
             'Č' => 'c', 'Ć' => 'c', 'Đ' => 'dj', 'Š' => 's', 'Ž' => 'z'];
    $tekst = strtr($tekst, $mapa);
    $tekst = mb_strtolower($tekst, 'UTF-8');
    $tekst = preg_replace('/[^a-z0-9]+/u', '-', $tekst) ?? '';
    return trim($tekst, '-') ?: 'stavka';
}

/** class="active" za trenutnu navigaciju. */
function nav_active(string $putanja): string
{
    $trenutna = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/');
    $cilj = trim(BASE_URL . $putanja, '/');
    return $trenutna === $cilj ? 'active' : '';
}

/** Skrati tekst na n karaktera. */
function skrati(string $tekst, int $n = 120): string
{
    $tekst = trim(preg_replace('/\s+/', ' ', $tekst) ?? '');
    return mb_strlen($tekst) > $n ? mb_substr($tekst, 0, $n - 1) . '…' : $tekst;
}

/* --- Validacija ----------------------------------------------------- */

/**
 * @return array{0: array<string,string>, 1: array<string,mixed>}  [greske, ocisceno]
 */
function validiraj_upit(array $ulaz): array
{
    $g = [];
    $ime     = trim((string) ($ulaz['ime'] ?? ''));
    $email   = trim((string) ($ulaz['email'] ?? ''));
    $telefon = trim((string) ($ulaz['telefon'] ?? ''));
    $poruka  = trim((string) ($ulaz['poruka'] ?? ''));

    if (mb_strlen($ime) < 2)                              $g['ime'] = 'Unesite ime i prezime.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))       $g['email'] = 'Unesite ispravnu e-mail adresu.';
    if ($telefon !== '' && !preg_match('/^[0-9 +\/().-]{6,20}$/', $telefon)) $g['telefon'] = 'Broj telefona nije ispravan.';
    if (mb_strlen($poruka) < 10)                          $g['poruka'] = 'Poruka treba da ima bar 10 karaktera.';
    if (mb_strlen($poruka) > 2000)                        $g['poruka'] = 'Poruka je preduga (maksimum 2000 karaktera).';

    return [$g, [
        'ime'     => mb_substr($ime, 0, 120),
        'email'   => mb_substr($email, 0, 160),
        'telefon' => mb_substr($telefon, 0, 40),
        'poruka'  => $poruka,
        'rad_id'  => (int) ($ulaz['rad_id'] ?? 0) ?: null,
    ]];
}

/**
 * @return array{0: array<string,string>, 1: array<string,mixed>}
 */
function validiraj_recenziju(array $ulaz): array
{
    $g = [];
    $ime   = trim((string) ($ulaz['ime'] ?? ''));
    $ocena = (int) ($ulaz['ocena'] ?? 0);
    $tekst = trim((string) ($ulaz['tekst'] ?? ''));

    if (mb_strlen($ime) < 2)              $g['ime'] = 'Unesite ime.';
    if ($ocena < 1 || $ocena > 5)         $g['ocena'] = 'Ocena mora biti od 1 do 5.';
    if (mb_strlen($tekst) < 10)           $g['tekst'] = 'Napišite bar nekoliko reči (min. 10 karaktera).';
    if (mb_strlen($tekst) > 1000)         $g['tekst'] = 'Recenzija je preduga.';

    return [$g, [
        'ime'   => mb_substr($ime, 0, 80),
        'ocena' => max(1, min(5, $ocena)),
        'tekst' => $tekst,
    ]];
}
