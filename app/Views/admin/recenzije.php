<?php /** @var array $recenzije */ ?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <h1 class="h3 sn-serif mb-3">Recenzije</h1>

    <div class="sn-panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Datum</th><th>Ime</th><th>Ocena</th><th>Tekst</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($recenzije as $r): ?>
                    <tr>
                        <td class="small text-nowrap"><?= e(date('d.m.Y.', strtotime($r['kreiran']))) ?></td>
                        <td><?= e($r['ime']) ?></td>
                        <td class="sn-stars"><?= str_repeat('★', (int) $r['ocena']) ?></td>
                        <td class="small" style="max-width:320px"><?= e($r['tekst']) ?></td>
                        <td>
                            <?php if ($r['odobrena']): ?>
                                <span class="sn-status zavrsen">Objavljena</span>
                            <?php else: ?>
                                <span class="sn-status u_obradi">Na čekanju</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if (!$r['odobrena']): ?>
                                <form method="post" action="<?= url('admin/recenzije/' . $r['id'] . '/odobri') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sn btn-sm">Objavi</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="<?= url('admin/recenzije/' . $r['id'] . '/brisanje') ?>" class="d-inline"
                                  onsubmit="return confirm('Obrisati recenziju?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger btn-sm">Obriši</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recenzije): ?>
                    <tr><td colspan="6" class="text-muted-2">Nema recenzija.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
