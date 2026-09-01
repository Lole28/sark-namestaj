<?php

/**
 * JSON web servisi koje koristi front-end (Ajax) i koji demonstriraju
 * REST pristup: GET liste, POST kreiranje, PATCH izmena, upload fajla.
 */
class ApiController extends Controller
{
    /* --- GET /api/radovi?kategorija=slug --------------------------- */

    public function radovi(): void
    {
        $slug   = $this->polje('kategorija') ?: null;
        $radovi = (new Rad())->lista($slug);

        $out = array_map(static function (array $r): array {
            return [
                'id'          => (int) $r['id'],
                'naziv'       => $r['naziv'],
                'slug'        => $r['slug'],
                'kategorija'  => $r['kategorija_naziv'],
                'materijal'   => $r['materijal'],
                'cena_od'     => $r['cena_od'] !== null ? (float) $r['cena_od'] : null,
                'opis_kratak' => skrati((string) $r['opis'], 110),
                'slika'       => slika_url($r['naslovna']),
                'url'         => url('radovi/' . $r['slug']),
            ];
        }, $radovi);

        $this->json(['ok' => true, 'broj' => count($out), 'radovi' => $out]);
    }

    /* --- GET /api/kurs -------------------------------------------- */

    public function kurs(): void
    {
        $this->json(['ok' => true] + KursServis::eurRsd());
    }

    /* --- POST /api/upiti ---------------------------------------- */

    public function napraviUpit(): void
    {
        $this->proveriCsrf();
        [$greske, $ocisceno] = validiraj_upit($this->telo());

        if ($greske) {
            $this->json(['ok' => false, 'greske' => $greske], 422);
        }

        $ocisceno['korisnik_id'] = current_user()['id'] ?? null;
        $id = (new Upit())->kreiraj($ocisceno);

        $this->json([
            'ok'     => true,
            'id'     => $id,
            'poruka' => 'Hvala! Vaš upit je poslat — javićemo se u najkraćem roku.',
        ], 201);
    }

    /* --- POST /api/recenzije ----------------------------------- */

    public function napraviRecenziju(): void
    {
        $this->proveriCsrf();
        [$greske, $ocisceno] = validiraj_recenziju($this->telo());

        if ($greske) {
            $this->json(['ok' => false, 'greske' => $greske], 422);
        }

        (new Recenzija())->kreiraj($ocisceno['ime'], $ocisceno['ocena'], $ocisceno['tekst']);
        $this->json(['ok' => true, 'poruka' => 'Hvala na recenziji! Objavljujemo je nakon kratke provere.'], 201);
    }

    /* --- POST /api/slike  (upload slike uz rad, samo admin) ------- */

    public function otpremiSliku(): void
    {
        $this->zahtevajAdmina();
        $this->proveriCsrf();

        $radId = (int) ($_POST['rad_id'] ?? 0);
        if (!$radId || !(new Rad())->nadji($radId)) {
            $this->json(['ok' => false, 'greska' => 'Nepoznat rad.'], 422);
        }

        if (empty($_FILES['slika']) || $_FILES['slika']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['ok' => false, 'greska' => 'Fajl nije primljen. Proverite veličinu (max 3 MB).'], 422);
        }

        $fajl = $_FILES['slika'];
        $cfg  = $GLOBALS['config']['app'];

        if ($fajl['size'] > $cfg['max_upload_bytes']) {
            $this->json(['ok' => false, 'greska' => 'Slika je veća od 3 MB.'], 422);
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fajl['tmp_name']);
        if (!in_array($mime, $cfg['dozvoljeni_tipovi'], true)) {
            $this->json(['ok' => false, 'greska' => 'Dozvoljene su samo JPG, PNG i WEBP slike.'], 422);
        }
        if (getimagesize($fajl['tmp_name']) === false) {
            $this->json(['ok' => false, 'greska' => 'Fajl nije ispravna slika.'], 422);
        }

        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $ime = bin2hex(random_bytes(8)) . '.' . $ext;
        $cilj = $cfg['uploads_path'] . '/' . $ime;

        if (!is_dir($cfg['uploads_path'])) {
            @mkdir($cfg['uploads_path'], 0775, true);
        }
        if (!move_uploaded_file($fajl['tmp_name'], $cilj)) {
            $this->json(['ok' => false, 'greska' => 'Snimanje fajla nije uspelo (dozvole foldera).'], 500);
        }

        $alt = trim((string) ($_POST['alt_tekst'] ?? '')) ?: 'Fotografija rada';
        $slikaId = (new Slika())->dodaj($radId, $ime, mb_substr($alt, 0, 160));

        $this->json([
            'ok'    => true,
            'slika' => ['id' => $slikaId, 'url' => slika_url($ime), 'alt' => $alt],
        ], 201);
    }

    /* --- POST /api/slike/{id}/brisanje -------------------------- */

    public function obrisiSliku(string $id): void
    {
        $this->zahtevajAdmina();
        $this->proveriCsrf();

        $slika = (new Slika())->nadji((int) $id);
        if ($slika) {
            @unlink($GLOBALS['config']['app']['uploads_path'] . '/' . $slika['putanja']);
            (new Slika())->obrisi((int) $id);
        }
        $this->json(['ok' => true]);
    }

    /* --- PATCH /api/upiti/{id}  (promena statusa, samo admin) ---- */

    public function promeniStatusUpita(string $id): void
    {
        $this->zahtevajAdmina();
        $this->proveriCsrf();

        $status = (string) ($this->telo()['status'] ?? '');
        if (!(new Upit())->promeniStatus((int) $id, $status)) {
            $this->json(['ok' => false, 'greska' => 'Nepoznat status.'], 422);
        }

        $this->json([
            'ok'           => true,
            'status'       => $status,
            'status_naziv' => Upit::STATUS_NAZIV[$status],
        ]);
    }

    /* --- Interno ------------------------------------------------- */

    /** Telo zahteva: podržava i form-data i JSON (za PATCH iz fetch-a). */
    private function telo(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }
        $sirovo = file_get_contents('php://input') ?: '';
        $json = json_decode($sirovo, true);
        return is_array($json) ? $json : [];
    }

    protected function proveriCsrf(): void
    {
        $token = $_POST['_csrf']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? ($this->telo()['_csrf'] ?? '');

        if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            $this->json(['ok' => false, 'greska' => 'Sesija je istekla. Osvežite stranicu.'], 419);
        }
    }
}
