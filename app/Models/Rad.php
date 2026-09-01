<?php

class Rad extends Model
{
    protected string $tabela = 'radovi_ls';

    private const IZBOR = "
        r.*,
        k.naziv AS kategorija_naziv,
        k.slug  AS kategorija_slug,
        (SELECT s.putanja FROM slike_ls s WHERE s.rad_id = r.id ORDER BY s.redosled, s.id LIMIT 1) AS naslovna
    ";

    /** Lista radova, opciono filtrirana po slug-u kategorije. */
    public function lista(?string $kategorijaSlug = null): array
    {
        $sql = "SELECT " . self::IZBOR . "
                FROM {$this->tabela} r
                JOIN kategorije_ls k ON k.id = r.kategorija_id";
        $par = [];
        if ($kategorijaSlug) {
            $sql .= " WHERE k.slug = ?";
            $par[] = $kategorijaSlug;
        }
        $sql .= " ORDER BY r.istaknut DESC, r.kreiran DESC, r.id DESC";
        return $this->upit($sql, $par)->fetchAll();
    }

    public function istaknuti(int $limit = 4): array
    {
        $limit = max(1, min(12, $limit));
        return $this->upit("
            SELECT " . self::IZBOR . "
            FROM {$this->tabela} r
            JOIN kategorije_ls k ON k.id = r.kategorija_id
            WHERE r.istaknut = 1
            ORDER BY r.kreiran DESC, r.id DESC
            LIMIT {$limit}
        ")->fetchAll();
    }

    public function poSlugu(string $slug): ?array
    {
        return $this->upit("
            SELECT " . self::IZBOR . "
            FROM {$this->tabela} r
            JOIN kategorije_ls k ON k.id = r.kategorija_id
            WHERE r.slug = ?
        ", [$slug])->fetch() ?: null;
    }

    public function slicni(int $kategorijaId, int $osimId, int $limit = 3): array
    {
        $limit = max(1, min(6, $limit));
        return $this->upit("
            SELECT " . self::IZBOR . "
            FROM {$this->tabela} r
            JOIN kategorije_ls k ON k.id = r.kategorija_id
            WHERE r.kategorija_id = ? AND r.id <> ?
            ORDER BY RAND() LIMIT {$limit}
        ", [$kategorijaId, $osimId])->fetchAll();
    }

    public function kreiraj(array $p): int
    {
        return $this->ubaci([
            'kategorija_id' => (int) $p['kategorija_id'],
            'naziv'         => $p['naziv'],
            'slug'          => $this->jedinstvenSlug(slugify($p['naziv'])),
            'opis'          => $p['opis'],
            'materijal'     => $p['materijal'],
            'cena_od'       => $p['cena_od'] !== '' ? (float) $p['cena_od'] : null,
            'istaknut'      => !empty($p['istaknut']) ? 1 : 0,
        ]);
    }

    public function azuriraj(int $id, array $p): void
    {
        $this->izmeni($id, [
            'kategorija_id' => (int) $p['kategorija_id'],
            'naziv'         => $p['naziv'],
            'opis'          => $p['opis'],
            'materijal'     => $p['materijal'],
            'cena_od'       => $p['cena_od'] !== '' ? (float) $p['cena_od'] : null,
            'istaknut'      => !empty($p['istaknut']) ? 1 : 0,
        ]);
    }

    private function jedinstvenSlug(string $osnova): string
    {
        $slug = $osnova;
        $i = 2;
        while ($this->upit("SELECT id FROM {$this->tabela} WHERE slug = ?", [$slug])->fetch()) {
            $slug = $osnova . '-' . $i++;
        }
        return $slug;
    }
}
