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

    /** Sve slike + podaci o radu, za galeriju/marquee na početnoj. */
    public function zaGaleriju(): array
    {
        return $this->upit("
            SELECT s.id, s.putanja, s.alt_tekst,
                   r.naziv AS rad_naziv, r.slug AS rad_slug, r.opis AS rad_opis,
                   k.naziv AS kategorija_naziv
            FROM {$this->tabela} s
            JOIN radovi_ls r ON r.id = s.rad_id
            JOIN kategorije_ls k ON k.id = r.kategorija_id
            ORDER BY r.istaknut DESC, s.rad_id, s.redosled
        ")->fetchAll();
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
