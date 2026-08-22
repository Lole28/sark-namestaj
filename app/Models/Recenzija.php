<?php

class Recenzija extends Model
{
    protected string $tabela = 'recenzije_ls';

    public function odobrene(int $limit = 6): array
    {
        $limit = max(1, min(20, $limit));
        return $this->upit("
            SELECT * FROM {$this->tabela}
            WHERE odobrena = 1
            ORDER BY kreiran DESC LIMIT {$limit}
        ")->fetchAll();
    }

    public function sve(string $poredak = 'odobrena ASC, kreiran DESC'): array
    {
        return $this->upit("SELECT * FROM {$this->tabela} ORDER BY {$poredak}")->fetchAll();
    }

    public function kreiraj(string $ime, int $ocena, string $tekst): int
    {
        return $this->ubaci([
            'ime'      => $ime,
            'ocena'    => max(1, min(5, $ocena)),
            'tekst'    => $tekst,
            'odobrena' => 0,
        ]);
    }

    public function odobri(int $id): void
    {
        $this->upit("UPDATE {$this->tabela} SET odobrena = 1 WHERE id = ?", [$id]);
    }
}
