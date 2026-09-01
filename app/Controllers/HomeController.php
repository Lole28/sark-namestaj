<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $slika     = new Slika();
        $rad       = new Rad();
        $recenzije = new Recenzija();

        $this->prikazi('home/index', [
            'naslov'     => null,
            'overlayNav' => true,
            'galerija'   => $slika->zaGaleriju(),
            'brojRadova' => (int) Database::veza()->query('SELECT COUNT(*) FROM radovi_ls')->fetchColumn(),
            'recenzije'  => $recenzije->odobrene(6),
        ]);
    }
}
