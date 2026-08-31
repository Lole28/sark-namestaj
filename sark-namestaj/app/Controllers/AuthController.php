<?php

class AuthController extends Controller
{
    public function prijavaForma(): void
    {
        if (current_user()) {
            $this->preusmeri('/');
        }
        $this->prikazi('auth/prijava', ['naslov' => 'Prijava']);
        stare_ocisti();
    }

    public function prijava(): void
    {
        $this->proveriCsrf();

        $korisnicko = $this->polje('korisnicko_ime');
        $lozinka    = (string) ($_POST['lozinka'] ?? '');

        $model = new Korisnik();
        $nalog = $model->poKorisnickomImenu($korisnicko) ?? $model->poEmailu($korisnicko);

        if (!$nalog || !password_verify($lozinka, $nalog['lozinka_hash'])) {
            stare_upisi(['korisnicko_ime' => $korisnicko]);
            flash('greska', 'Pogrešno korisničko ime ili lozinka.');
            $this->preusmeri('prijava');
        }

        // zaštita od "session fixation"
        session_regenerate_id(true);

        $_SESSION['korisnik'] = [
            'id'             => (int) $nalog['id'],
            'ime'            => $nalog['ime'],
            'korisnicko_ime' => $nalog['korisnicko_ime'],
            'uloga'          => $nalog['uloga'],
        ];

        stare_ocisti();
        flash('uspeh', 'Dobrodošli, ' . $nalog['ime'] . '!');
        $this->preusmeri($nalog['uloga'] === 'admin' ? 'admin' : 'nalog/upiti');
    }

    public function registracijaForma(): void
    {
        if (current_user()) {
            $this->preusmeri('/');
        }
        $this->prikazi('auth/registracija', ['naslov' => 'Registracija']);
        stare_ocisti();
    }

    public function registracija(): void
    {
        $this->proveriCsrf();

        $ime        = $this->polje('ime');
        $korisnicko = $this->polje('korisnicko_ime');
        $email      = $this->polje('email');
        $lozinka    = (string) ($_POST['lozinka'] ?? '');
        $lozinka2   = (string) ($_POST['lozinka2'] ?? '');

        $model  = new Korisnik();
        $greske = [];

        if (mb_strlen($ime) < 2) {
            $greske['ime'] = 'Unesite ime i prezime.';
        }
        if (!preg_match('/^[a-zA-Z0-9_.]{3,30}$/', $korisnicko)) {
            $greske['korisnicko_ime'] = '3–30 karaktera: slova, brojevi, tačka ili donja crta.';
        } elseif ($model->poKorisnickomImenu($korisnicko)) {
            $greske['korisnicko_ime'] = 'Korisničko ime je zauzeto.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $greske['email'] = 'Unesite ispravnu e-mail adresu.';
        } elseif ($model->poEmailu($email)) {
            $greske['email'] = 'Nalog sa ovom e-mail adresom već postoji.';
        }
        if (mb_strlen($lozinka) < 6) {
            $greske['lozinka'] = 'Lozinka mora imati bar 6 karaktera.';
        } elseif ($lozinka !== $lozinka2) {
            $greske['lozinka2'] = 'Lozinke se ne poklapaju.';
        }

        if ($greske) {
            stare_upisi(['ime' => $ime, 'korisnicko_ime' => $korisnicko, 'email' => $email]);
            greske_upisi($greske);
            flash('greska', 'Proverite unos.');
            $this->preusmeri('registracija');
        }

        $id = $model->registruj($ime, $korisnicko, $email, $lozinka);

        session_regenerate_id(true);
        $_SESSION['korisnik'] = [
            'id'             => $id,
            'ime'            => $ime,
            'korisnicko_ime' => $korisnicko,
            'uloga'          => 'klijent',
        ];

        stare_ocisti();
        flash('uspeh', 'Nalog je kreiran. Dobrodošli!');
        $this->preusmeri('nalog/upiti');
    }

    public function odjava(): void
    {
        $this->proveriCsrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        flash('uspeh', 'Odjavljeni ste.');
        $this->preusmeri('/');
    }
}
