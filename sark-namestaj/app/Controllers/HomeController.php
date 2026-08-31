<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $radovi     = new Rad();
        $kategorije = new Kategorija();
        $recenzije  = new Recenzija();

        $this->prikazi('home/index', [
            'naslov'     => 'Nameštaj po meri — kuhinje, plakari i enterijeri',
            'istaknuti'  => $radovi->istaknuti(4),
            'kategorije' => $kategorije->sveSaBrojem(),
            'recenzije'  => $recenzije->odobrene(3),
        ]);
    }
}
