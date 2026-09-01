<?php

/**
 * Osnovni kontroler: renderovanje pogleda, JSON odgovori, preusmeravanja,
 * provera prijave / uloge i provera CSRF tokena.
 */
class Controller
{
    /** Renderuj pogled unutar zajedničkog layout-a. */
    public function prikazi(string $pogled, array $podaci = [], bool $saLayoutom = true): void
    {
        $podaci += ['naslov' => null, 'korisnik' => current_user()];
        extract($podaci, EXTR_SKIP);

        $putanja = BASE_PATH . '/app/Views/' . $pogled . '.php';
        if (!is_file($putanja)) {
            http_response_code(500);
            exit("Pogled nije pronađen: {$pogled}");
        }

        ob_start();
        require $putanja;
        $sadrzaj = ob_get_clean();

        if ($saLayoutom) {
            require BASE_PATH . '/app/Views/layout/header.php';
            echo $sadrzaj;
            require BASE_PATH . '/app/Views/layout/footer.php';
        } else {
            echo $sadrzaj;
        }
    }

    /** Vrati JSON i prekini izvršavanje. */
    protected function json(array $podaci, int $kod = 200): void
    {
        http_response_code($kod);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($podaci, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function preusmeri(string $putanja): void
    {
        header('Location: ' . url($putanja));
        exit;
    }

    /* --- Bezbednost ----------------------------------------------------- */

    protected function zahtevajPrijavu(): void
    {
        if (!current_user()) {
            flash('greska', 'Prijavite se da biste nastavili.');
            $this->preusmeri('prijava');
        }
    }

    protected function zahtevajAdmina(): void
    {
        $u = current_user();
        if (!$u) {
            flash('greska', 'Prijavite se da biste nastavili.');
            $this->preusmeri('prijava');
        }
        if (($u['uloga'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->prikazi('greske/404', ['naslov' => 'Zabranjen pristup', 'poruka' => 'Ova strana je dostupna samo administratoru.']);
            exit;
        }
    }

    protected function proveriCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            if (is_ajax()) {
                $this->json(['ok' => false, 'greska' => 'Sesija je istekla. Osvežite stranicu i pokušajte ponovo.'], 419);
            }
            http_response_code(419);
            exit('Neispravan CSRF token. Vratite se nazad i osvežite stranicu.');
        }
    }

    /** Očisti string sa forme. */
    protected function polje(string $kljuc, string $podrazumevano = ''): string
    {
        $v = $_POST[$kljuc] ?? $_GET[$kljuc] ?? $podrazumevano;
        return is_string($v) ? trim($v) : $podrazumevano;
    }
}
