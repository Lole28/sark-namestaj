<?php

/**
 * Zajednička osnova za sve modele.
 * Nudi kratke metode za rad sa jednom tabelom preko pripremljenih upita.
 */
abstract class Model
{
    protected PDO $db;

    /** Ime tabele u bazi (sa sufiksom _ls). */
    protected string $tabela = '';

    public function __construct()
    {
        $this->db = Database::veza();
    }

    /** Pokreni pripremljeni upit i vrati PDOStatement. */
    protected function upit(string $sql, array $parametri = []): PDOStatement
    {
        $st = $this->db->prepare($sql);
        $st->execute($parametri);
        return $st;
    }

    public function sve(string $poredak = 'id DESC'): array
    {
        return $this->upit("SELECT * FROM {$this->tabela} ORDER BY {$poredak}")->fetchAll();
    }

    public function nadji(int $id): ?array
    {
        $red = $this->upit("SELECT * FROM {$this->tabela} WHERE id = ?", [$id])->fetch();
        return $red ?: null;
    }

    public function obrisi(int $id): void
    {
        $this->upit("DELETE FROM {$this->tabela} WHERE id = ?", [$id]);
    }

    /** Ubaci red iz asocijativnog niza i vrati novi ID. */
    protected function ubaci(array $podaci): int
    {
        $kolone = array_keys($podaci);
        $placeholderi = array_map(static fn($k) => ':' . $k, $kolone);
        $sql = "INSERT INTO {$this->tabela} (" . implode(',', $kolone) . ') VALUES (' . implode(',', $placeholderi) . ')';
        $this->upit($sql, $podaci);
        return (int) $this->db->lastInsertId();
    }

    /** Izmeni red po ID-u iz asocijativnog niza. */
    protected function izmeni(int $id, array $podaci): void
    {
        $set = implode(', ', array_map(static fn($k) => "{$k} = :{$k}", array_keys($podaci)));
        $podaci['id'] = $id;
        $this->upit("UPDATE {$this->tabela} SET {$set} WHERE id = :id", $podaci);
    }
}
