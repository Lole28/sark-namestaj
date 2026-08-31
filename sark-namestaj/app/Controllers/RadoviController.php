<?php

class RadoviController extends Controller
{
    public function lista(): void
    {
        $rad        = new Rad();
        $kategorije = new Kategorija();

        $izabrana = $this->polje('kategorija') ?: null;

        $this->prikazi('radovi/lista', [
            'naslov'     => 'Naši radovi',
            'kategorije' => $kategorije->sveSaBrojem(),
            'radovi'     => $rad->lista($izabrana),
            'izabrana'   => $izabrana,
        ]);
    }

    public function detalj(string $slug): void
    {
        $rad    = new Rad();
        $stavka = $rad->poSlugu($slug);

        if (!$stavka) {
            http_response_code(404);
            $this->prikazi('greske/404', ['naslov' => 'Rad nije pronađen']);
            return;
        }

        $slike = (new Slika())->zaRad((int) $stavka['id']);

        $this->prikazi('radovi/detalj', [
            'naslov' => $stavka['naziv'],
            'rad'    => $stavka,
            'slike'  => $slike,
            'slicni' => $rad->slicni((int) $stavka['kategorija_id'], (int) $stavka['id'], 3),
        ]);
        stare_ocisti();
    }
}
