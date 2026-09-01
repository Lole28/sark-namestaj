<?php

/**
 * Obrada formi bez JavaScript-a (rezervni put).
 * Kada je JS uključen, iste podatke prima ApiController preko Ajax-a.
 */
class UpitController extends Controller
{
    public function posalji(): void
    {
        $this->proveriCsrf();

        [$greske, $ocisceno] = validiraj_upit($_POST);
        $povratak = $this->polje('povratak', url('kontakt'));

        if ($greske) {
            stare_upisi($_POST);
            greske_upisi($greske);
            flash('greska', 'Proverite polja u formi.');
            header('Location: ' . $povratak);
            exit;
        }

        $ocisceno['korisnik_id'] = current_user()['id'] ?? null;
        (new Upit())->kreiraj($ocisceno);

        stare_ocisti();
        flash('uspeh', 'Hvala! Vaš upit je poslat. Javićemo se u najkraćem roku.');
        header('Location: ' . $povratak);
        exit;
    }

    public function recenzija(): void
    {
        $this->proveriCsrf();

        [$greske, $ocisceno] = validiraj_recenziju($_POST);

        if ($greske) {
            stare_upisi($_POST);
            greske_upisi($greske);
            flash('greska', 'Proverite polja u formi recenzije.');
        } else {
            (new Recenzija())->kreiraj($ocisceno['ime'], $ocisceno['ocena'], $ocisceno['tekst']);
            stare_ocisti();
            flash('uspeh', 'Hvala na recenziji! Biće objavljena nakon provere.');
        }

        header('Location: ' . ($this->polje('povratak', url('/')) ?: url('/')));
        exit;
    }
}
