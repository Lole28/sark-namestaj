<?php

class StraniceController extends Controller
{
    public function oNama(): void
    {
        $this->prikazi('stranice/o-nama', ['naslov' => 'O nama']);
    }

    public function usluge(): void
    {
        $kategorije = new Kategorija();
        $this->prikazi('stranice/usluge', [
            'naslov'     => 'Usluge',
            'kategorije' => $kategorije->sveSaBrojem(),
        ]);
    }

    public function kontakt(): void
    {
        $this->prikazi('stranice/kontakt', ['naslov' => 'Kontakt']);
        stare_ocisti();
    }
}
