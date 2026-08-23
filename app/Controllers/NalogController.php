<?php

class NalogController extends Controller
{
    public function upiti(): void
    {
        $this->zahtevajPrijavu();

        $upiti = (new Upit())->zaKorisnika((int) current_user()['id']);

        $this->prikazi('nalog/upiti', [
            'naslov' => 'Moji upiti',
            'upiti'  => $upiti,
        ]);
    }
}
