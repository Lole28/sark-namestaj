<?php /** @var array $upiti */ ?>
<div class="page-top wrap wrap--narrow">
    <p class="eyebrow reveal">Moj nalog</p>
    <h1 class="serif-l reveal" style="--i:1">Moji upiti</h1>

    <?php if (!$upiti): ?>
        <div class="notice" style="margin-top:2rem">
            Još nemate poslatih upita.
            <a href="<?= url('radovi') ?>" style="text-decoration:underline">Pogledajte radove</a>.
        </div>
    <?php else: ?>
        <div class="stack" style="margin-top:2rem">
            <?php foreach ($upiti as $up): ?>
                <div class="panel">
                    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:.6rem;align-items:baseline">
                        <div>
                            <?php if ($up['rad_naziv']): ?>
                                <strong><a href="<?= url('radovi/' . $up['rad_slug']) ?>" style="text-decoration:underline"><?= e($up['rad_naziv']) ?></a></strong>
                            <?php else: ?>
                                <strong>Opšti upit</strong>
                            <?php endif; ?>
                            <div class="text-stone" style="font-size:.82rem"><?= e(date('d.m.Y. H:i', strtotime($up['kreiran']))) ?></div>
                        </div>
                        <span class="pill <?= e($up['status']) ?>"><?= e(Upit::STATUS_NAZIV[$up['status']] ?? $up['status']) ?></span>
                    </div>
                    <p style="margin:.8rem 0 0"><?= nl2br(e($up['poruka'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
