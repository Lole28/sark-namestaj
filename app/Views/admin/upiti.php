<?php /** @var array $upiti @var array $brojevi */ ?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <h1 class="h3 sn-serif mb-3">Upiti klijenata</h1>
    <div class="d-flex gap-3 small mb-3 text-muted-2">
        <span>Novi: <strong class="text-dark"><?= (int) $brojevi['nov'] ?></strong></span>
        <span>U obradi: <strong class="text-dark"><?= (int) $brojevi['u_obradi'] ?></strong></span>
        <span>Završeni: <strong class="text-dark"><?= (int) $brojevi['zavrsen'] ?></strong></span>
    </div>

    <div class="sn-panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Datum</th><th>Klijent</th><th>Rad</th><th>Poruka</th><th>Status</th><th>Promeni</th></tr>
                </thead>
                <tbody>
                <?php foreach ($upiti as $u): ?>
                    <tr>
                        <td class="small text-nowrap"><?= e(date('d.m.Y.', strtotime($u['kreiran']))) ?><br>
                            <span class="text-muted-2"><?= e(date('H:i', strtotime($u['kreiran']))) ?></span></td>
                        <td class="small">
                            <strong><?= e($u['ime']) ?></strong><br>
                            <a href="mailto:<?= e($u['email']) ?>"><?= e($u['email']) ?></a>
                            <?php if ($u['telefon']): ?><br><?= e($u['telefon']) ?><?php endif; ?>
                        </td>
                        <td class="small"><?= $u['rad_naziv'] ? e($u['rad_naziv']) : '<span class="text-muted-2">opšti</span>' ?></td>
                        <td class="small" style="max-width:280px"><?= nl2br(e(skrati($u['poruka'], 240))) ?></td>
                        <td><span class="pill <?= e($u['status']) ?>" data-status-tag="<?= (int) $u['id'] ?>">
                            <?= e(Upit::STATUS_NAZIV[$u['status']]) ?></span></td>
                        <td>
                            <select class="form-select form-select-sm" style="width:130px"
                                    data-upit-status="<?= (int) $u['id'] ?>" data-trenutno="<?= e($u['status']) ?>">
                                <?php foreach (Upit::STATUS_NAZIV as $k => $naziv): ?>
                                    <option value="<?= e($k) ?>" <?= $u['status'] === $k ? 'selected' : '' ?>><?= e($naziv) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$upiti): ?>
                    <tr><td colspan="6" class="text-muted-2">Još nema upita.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
