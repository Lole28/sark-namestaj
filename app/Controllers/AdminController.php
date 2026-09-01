<?php

class AdminController extends Controller
{
    public function __construct()
    {
        $this->zahtevajAdmina();
    }

    /* --- Kontrolna tabla --------------------------------------------- */

    public function pocetna(): void
    {
        $this->prikazi('admin/dashboard', [
            'naslov'    => 'Administracija',
            'brojUpita' => (new Upit())->brojPoStatusu(),
            'brojRadova'     => (int) Database::veza()->query("SELECT COUNT(*) FROM radovi_ls")->fetchColumn(),
            'brojKategorija' => (int) Database::veza()->query("SELECT COUNT(*) FROM kategorije_ls")->fetchColumn(),
            'brojRecenzija'  => (int) Database::veza()->query("SELECT COUNT(*) FROM recenzije_ls WHERE odobrena = 0")->fetchColumn(),
            'poslednji'      => (new Upit())->sviDetaljno(),
        ]);
    }

    /* --- Kategorije -------------------------------------------------- */

    public function kategorije(): void
    {
        $this->prikazi('admin/kategorije', [
            'naslov'     => 'Kategorije',
            'kategorije' => (new Kategorija())->sveSaBrojem(),
        ]);
    }

    public function kategorijaForma(?string $id = null): void
    {
        $kategorija = $id ? (new Kategorija())->nadji((int) $id) : null;
        if ($id && !$kategorija) {
            $this->preusmeri('admin/kategorije');
        }
        $this->prikazi('admin/kategorija_forma', [
            'naslov'     => $kategorija ? 'Izmena kategorije' : 'Nova kategorija',
            'kategorija' => $kategorija,
        ]);
        stare_ocisti();
    }

    public function sacuvajKategoriju(): void
    {
        $this->proveriCsrf();
        $naziv = $this->polje('naziv');
        $opis  = $this->polje('opis');

        if (mb_strlen($naziv) < 2) {
            greske_upisi(['naziv' => 'Naziv je obavezan.']);
            stare_upisi($_POST);
            $this->preusmeri('admin/kategorije/nova');
        }

        (new Kategorija())->kreiraj($naziv, $opis);
        flash('uspeh', 'Kategorija je dodata.');
        $this->preusmeri('admin/kategorije');
    }

    public function azurirajKategoriju(string $id): void
    {
        $this->proveriCsrf();
        $model = new Kategorija();
        if (!$model->nadji((int) $id)) {
            $this->preusmeri('admin/kategorije');
        }
        $naziv = $this->polje('naziv');
        if (mb_strlen($naziv) < 2) {
            greske_upisi(['naziv' => 'Naziv je obavezan.']);
            stare_upisi($_POST);
            $this->preusmeri("admin/kategorije/{$id}/izmena");
        }
        $model->azuriraj((int) $id, $naziv, $this->polje('opis'));
        flash('uspeh', 'Kategorija je sačuvana.');
        $this->preusmeri('admin/kategorije');
    }

    public function obrisiKategoriju(string $id): void
    {
        $this->proveriCsrf();
        $brojRadova = (int) $this->uz("SELECT COUNT(*) FROM radovi_ls WHERE kategorija_id = ?", [(int) $id]);
        if ($brojRadova > 0) {
            flash('greska', 'Kategorija se ne može obrisati dok sadrži radove.');
        } else {
            (new Kategorija())->obrisi((int) $id);
            flash('uspeh', 'Kategorija je obrisana.');
        }
        $this->preusmeri('admin/kategorije');
    }

    /* --- Radovi --------------------------------------------------- */

    public function radovi(): void
    {
        $this->prikazi('admin/radovi', [
            'naslov' => 'Radovi',
            'radovi' => (new Rad())->lista(),
        ]);
    }

    public function radForma(?string $id = null): void
    {
        $rad   = $id ? (new Rad())->nadji((int) $id) : null;
        if ($id && !$rad) {
            $this->preusmeri('admin/radovi');
        }
        $this->prikazi('admin/rad_forma', [
            'naslov'     => $rad ? 'Izmena rada' : 'Novi rad',
            'rad'        => $rad,
            'slike'      => $rad ? (new Slika())->zaRad((int) $rad['id']) : [],
            'kategorije' => (new Kategorija())->sveAbecedno(),
        ]);
        stare_ocisti();
    }

    public function sacuvajRad(): void
    {
        $this->proveriCsrf();
        [$greske, $p] = $this->validirajRad($_POST);
        if ($greske) {
            greske_upisi($greske);
            stare_upisi($_POST);
            $this->preusmeri('admin/radovi/novi');
        }
        $id = (new Rad())->kreiraj($p);
        flash('uspeh', 'Rad je kreiran. Sada dodajte slike.');
        $this->preusmeri("admin/radovi/{$id}/izmena");
    }

    public function azurirajRad(string $id): void
    {
        $this->proveriCsrf();
        $model = new Rad();
        if (!$model->nadji((int) $id)) {
            $this->preusmeri('admin/radovi');
        }
        [$greske, $p] = $this->validirajRad($_POST);
        if ($greske) {
            greske_upisi($greske);
            stare_upisi($_POST);
            $this->preusmeri("admin/radovi/{$id}/izmena");
        }
        $model->azuriraj((int) $id, $p);
        flash('uspeh', 'Rad je sačuvan.');
        $this->preusmeri("admin/radovi/{$id}/izmena");
    }

    public function obrisiRad(string $id): void
    {
        $this->proveriCsrf();
        foreach ((new Slika())->zaRad((int) $id) as $s) {
            @unlink($GLOBALS['config']['app']['uploads_path'] . '/' . $s['putanja']);
        }
        (new Rad())->obrisi((int) $id);
        flash('uspeh', 'Rad je obrisan.');
        $this->preusmeri('admin/radovi');
    }

    /* --- Upiti ---------------------------------------------------- */

    public function upiti(): void
    {
        $this->prikazi('admin/upiti', [
            'naslov' => 'Upiti klijenata',
            'upiti'  => (new Upit())->sviDetaljno(),
            'brojevi' => (new Upit())->brojPoStatusu(),
        ]);
    }

    /* --- Recenzije --------------------------------------------------- */

    public function recenzije(): void
    {
        $this->prikazi('admin/recenzije', [
            'naslov'    => 'Recenzije',
            'recenzije' => (new Recenzija())->sve(),
        ]);
    }

    public function odobriRecenziju(string $id): void
    {
        $this->proveriCsrf();
        (new Recenzija())->odobri((int) $id);
        flash('uspeh', 'Recenzija je objavljena.');
        $this->preusmeri('admin/recenzije');
    }

    public function obrisiRecenziju(string $id): void
    {
        $this->proveriCsrf();
        (new Recenzija())->obrisi((int) $id);
        flash('uspeh', 'Recenzija je obrisana.');
        $this->preusmeri('admin/recenzije');
    }

    /* --- Interno --------------------------------------------------- */

    private function validirajRad(array $ulaz): array
    {
        $g = [];
        $naziv     = trim((string) ($ulaz['naziv'] ?? ''));
        $opis      = trim((string) ($ulaz['opis'] ?? ''));
        $materijal = trim((string) ($ulaz['materijal'] ?? ''));
        $cena      = trim((string) ($ulaz['cena_od'] ?? ''));
        $katId     = (int) ($ulaz['kategorija_id'] ?? 0);

        if (mb_strlen($naziv) < 3)  $g['naziv'] = 'Naziv je obavezan (min. 3 karaktera).';
        if (mb_strlen($opis) < 10)  $g['opis'] = 'Opis je obavezan (min. 10 karaktera).';
        if (!$katId || !(new Kategorija())->nadji($katId)) $g['kategorija_id'] = 'Izaberite kategoriju.';
        if ($cena !== '' && (!is_numeric($cena) || (float) $cena < 0)) $g['cena_od'] = 'Cena mora biti broj.';

        return [$g, [
            'naziv'         => mb_substr($naziv, 0, 160),
            'opis'          => $opis,
            'materijal'     => mb_substr($materijal, 0, 160),
            'cena_od'       => $cena,
            'kategorija_id' => $katId,
            'istaknut'      => !empty($ulaz['istaknut']),
        ]];
    }

    private function uz(string $sql, array $par): mixed
    {
        $st = Database::veza()->prepare($sql);
        $st->execute($par);
        return $st->fetchColumn();
    }
}
