<?php

class Slika extends Model
{
    protected string $tabela = 'slike_ls';

    public function zaRad(int $radId): array
    {
        return $this->upit(
            "SELECT * FROM {$this->tabela} WHERE rad_id = ? ORDER BY redosled, id",
            [$radId]
        )->fetchAll();
    }

    public function dodaj(int $radId, string $putanja, string $altTekst): int
    {
        $sledeci = (int) $this->upit(
            "SELECT COALESCE(MAX(redosled), 0) + 1 FROM {$this->tabela} WHERE rad_id = ?",
            [$radId]
        )->fetchColumn();

        return $this->ubaci([
            'rad_id'    => $radId,
            'putanja'   => $putanja,
            'alt_tekst' => $altTekst,
            'redosled'  => $sledeci,
        ]);
    }
}
