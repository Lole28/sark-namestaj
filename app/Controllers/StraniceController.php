<?php

class StraniceController extends Controller
{
    public function oNama(): void
    {
        $this->prikazi('stranice/o-nama', ['naslov' => 'O nama', 'overlayNav' => true]);
    }

    public function kontakt(): void
    {
        $this->prikazi('stranice/kontakt', ['naslov' => 'Kontakt']);
        stare_ocisti();
    }
}
