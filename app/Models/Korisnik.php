<?php

class Korisnik extends Model
{
    protected string $tabela = 'korisnici_ls';

    public function poKorisnickomImenu(string $ime): ?array
    {
        return $this->upit(
            "SELECT * FROM {$this->tabela} WHERE korisnicko_ime = ?",
            [$ime]
        )->fetch() ?: null;
    }

    public function poEmailu(string $email): ?array
    {
        return $this->upit(
            "SELECT * FROM {$this->tabela} WHERE email = ?",
            [$email]
        )->fetch() ?: null;
    }

    public function registruj(string $ime, string $korisnicko, string $email, string $lozinka): int
    {
        return $this->ubaci([
            'ime'            => $ime,
            'korisnicko_ime' => $korisnicko,
            'email'          => $email,
            'lozinka_hash'   => password_hash($lozinka, PASSWORD_BCRYPT),
            'uloga'          => 'klijent',
        ]);
    }
}
