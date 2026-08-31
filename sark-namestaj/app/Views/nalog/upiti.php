<?php /** @var array $upiti */ ?>
<section class="sn-section">
    <div class="container">
        <p class="sn-eyebrow mb-1">Moj nalog</p>
        <h1 class="sn-serif mb-4">Moji upiti</h1>

        <?php if (!$upiti): ?>
            <div class="sn-panel">
                <p class="mb-2">Još nemate poslatih upita.</p>
                <a href="<?= url('radovi') ?>" class="btn btn-sn btn-sm">Pogledajte radove</a>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($upiti as $up): ?>
                    <div class="col-12">
                        <div class="sn-panel">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <div>
                                    <?php if ($up['rad_naziv']): ?>
                                        <h2 class="h6 mb-1">Rad:
                                            <a href="<?= url('radovi/' . $up['rad_slug']) ?>"><?= e($up['rad_naziv']) ?></a>
                                        </h2>
                                    <?php else: ?>
                                        <h2 class="h6 mb-1">Opšti upit</h2>
                                    <?php endif; ?>
                                    <p class="small text-muted-2 mb-2">Poslato: <?= e(date('d.m.Y. H:i', strtotime($up['kreiran']))) ?></p>
                                </div>
                                <span class="sn-status <?= e($up['status']) ?>" style="height:fit-content">
                                    <?= e(Upit::STATUS_NAZIV[$up['status']] ?? $up['status']) ?>
                                </span>
                            </div>
                            <p class="mb-0"><?= nl2br(e($up['poruka'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
