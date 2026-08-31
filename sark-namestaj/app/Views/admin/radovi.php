<?php /** @var array $radovi */ ?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 sn-serif mb-0">Radovi</h1>
        <a href="<?= url('admin/radovi/novi') ?>" class="btn btn-sn btn-sm">+ Novi rad</a>
    </div>

    <div class="sn-panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th></th><th>Naziv</th><th>Kategorija</th><th>Cena od</th><th class="text-center">Izdvojen</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($radovi as $r): ?>
                    <tr>
                        <td style="width:64px">
                            <img src="<?= e(slika_url($r['naslovna'])) ?>" alt="" width="56" height="42"
                                 style="object-fit:cover;border-radius:.3rem">
                        </td>
                        <td><strong><?= e($r['naziv']) ?></strong></td>
                        <td class="small"><?= e($r['kategorija_naziv']) ?></td>
                        <td class="small"><?= $r['cena_od'] !== null ? e(dinar($r['cena_od'])) : '—' ?></td>
                        <td class="text-center"><?= $r['istaknut'] ? '★' : '' ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('radovi/' . $r['slug']) ?>" target="_blank" class="btn btn-link btn-sm">Vidi</a>
                            <a href="<?= url('admin/radovi/' . $r['id'] . '/izmena') ?>" class="btn btn-outline-sn btn-sm">Izmeni</a>
                            <form method="post" action="<?= url('admin/radovi/' . $r['id'] . '/brisanje') ?>" class="d-inline"
                                  onsubmit="return confirm('Obrisati rad „<?= e($r['naziv']) ?>” i sve njegove slike?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger btn-sm">Obriši</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$radovi): ?>
                    <tr><td colspan="6" class="text-muted-2">Još nema radova. Dodajte prvi.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
