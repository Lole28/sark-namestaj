<?php

class Upit extends Model
{
    protected string $tabela = 'upiti_ls';

    public const STATUSI = ['nov', 'u_obradi', 'zavrsen'];

    public const STATUS_NAZIV = [
        'nov'      => 'Nov',
        'u_obradi' => 'U obradi',
        'zavrsen'  => 'Završen',
    ];

    public function kreiraj(array $p): int
    {
        return $this->ubaci([
            'rad_id'      => $p['rad_id'] ?: null,
            'korisnik_id' => $p['korisnik_id'] ?: null,
            'ime'         => $p['ime'],
            'email'       => $p['email'],
            'telefon'     => $p['telefon'],
            'poruka'      => $p['poruka'],
            'status'      => 'nov',
        ]);
    }

    /** Svi upiti za admin pregled (sa nazivom rada, ako postoji). */
    public function sviDetaljno(): array
    {
        return $this->upit("
            SELECT u.*, r.naziv AS rad_naziv, r.slug AS rad_slug
            FROM {$this->tabela} u
            LEFT JOIN radovi_ls r ON r.id = u.rad_id
            ORDER BY FIELD(u.status,'nov','u_obradi','zavrsen'), u.kreiran DESC
        ")->fetchAll();
    }

    public function zaKorisnika(int $korisnikId): array
    {
        return $this->upit("
            SELECT u.*, r.naziv AS rad_naziv, r.slug AS rad_slug
            FROM {$this->tabela} u
            LEFT JOIN radovi_ls r ON r.id = u.rad_id
            WHERE u.korisnik_id = ?
            ORDER BY u.kreiran DESC
        ", [$korisnikId])->fetchAll();
    }

    public function promeniStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUSI, true)) {
            return false;
        }
        $this->upit("UPDATE {$this->tabela} SET status = ? WHERE id = ?", [$status, $id]);
        return true;
    }

    /** ['nov' => 3, 'u_obradi' => 1, 'zavrsen' => 5] */
    public function brojPoStatusu(): array
    {
        $osnova = array_fill_keys(self::STATUSI, 0);
        $redovi = $this->upit("SELECT status, COUNT(*) AS n FROM {$this->tabela} GROUP BY status")->fetchAll();
        foreach ($redovi as $r) {
            $osnova[$r['status']] = (int) $r['n'];
        }
        return $osnova;
    }
}
