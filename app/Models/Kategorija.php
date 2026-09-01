<?php

class Kategorija extends Model
{
    protected string $tabela = 'kategorije_ls';

    /** Sve kategorije abecedno, sa brojem radova u svakoj. */
    public function sveSaBrojem(): array
    {
        return $this->upit("
            SELECT k.*, COUNT(r.id) AS broj_radova
            FROM {$this->tabela} k
            LEFT JOIN radovi_ls r ON r.kategorija_id = k.id
            GROUP BY k.id
            ORDER BY k.naziv
        ")->fetchAll();
    }

    public function sveAbecedno(): array
    {
        return $this->sve('naziv ASC');
    }

    public function poSlugu(string $slug): ?array
    {
        return $this->upit("SELECT * FROM {$this->tabela} WHERE slug = ?", [$slug])->fetch() ?: null;
    }

    public function kreiraj(string $naziv, string $opis): int
    {
        return $this->ubaci([
            'naziv' => $naziv,
            'slug'  => $this->jedinstvenSlug(slugify($naziv)),
            'opis'  => $opis,
        ]);
    }

    public function azuriraj(int $id, string $naziv, string $opis): void
    {
        $this->izmeni($id, ['naziv' => $naziv, 'opis' => $opis]);
    }

    private function jedinstvenSlug(string $osnova, ?int $osim = null): string
    {
        $slug = $osnova;
        $i = 2;
        while (true) {
            $sql = "SELECT id FROM {$this->tabela} WHERE slug = ?" . ($osim ? ' AND id <> ?' : '');
            $par = $osim ? [$slug, $osim] : [$slug];
            if (!$this->upit($sql, $par)->fetch()) {
                return $slug;
            }
            $slug = $osnova . '-' . $i++;
        }
    }
}
