<?php /** @var array $brojUpita @var int $brojRadova @var int $brojKategorija @var int $brojRecenzija @var array $poslednji */ ?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="sn-stat"><div class="n"><?= (int) $brojRadova ?></div><div class="l">Radova</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="sn-stat"><div class="n"><?= (int) $brojKategorija ?></div><div class="l">Kategorija</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="sn-stat"><div class="n"><?= (int) $brojUpita['nov'] ?></div><div class="l">Novih upita</div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="sn-stat"><div class="n"><?= (int) $brojRecenzija ?></div><div class="l">Recenzija na čekanju</div></div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <a href="<?= url('admin/radovi/novi') ?>" class="btn btn-sn">+ Novi rad</a>
        <a href="<?= url('admin/kategorije/nova') ?>" class="btn btn-outline-sn">+ Nova kategorija</a>
    </div>

    <div class="sn-panel">
        <h2 class="h5 sn-serif">Upiti po statusu</h2>
        <div class="d-flex gap-4 mb-3">
            <span>Novi: <strong><?= (int) $brojUpita['nov'] ?></strong></span>
            <span>U obradi: <strong><?= (int) $brojUpita['u_obradi'] ?></strong></span>
            <span>Završeni: <strong><?= (int) $brojUpita['zavrsen'] ?></strong></span>
        </div>

        <h3 class="h6">Poslednji upiti</h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Datum</th><th>Klijent</th><th>Rad</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($poslednji, 0, 8) as $u): ?>
                    <tr>
                        <td class="small text-nowrap"><?= e(date('d.m.Y.', strtotime($u['kreiran']))) ?></td>
                        <td><?= e($u['ime']) ?><br><span class="small text-muted-2"><?= e($u['email']) ?></span></td>
                        <td class="small"><?= e($u['rad_naziv'] ?? '—') ?></td>
                        <td><span class="sn-status <?= e($u['status']) ?>"><?= e(Upit::STATUS_NAZIV[$u['status']]) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$poslednji): ?>
                    <tr><td colspan="4" class="text-muted-2">Nema upita.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="<?= url('admin/upiti') ?>" class="btn btn-outline-sn btn-sm">Svi upiti</a>
    </div>
</div>
