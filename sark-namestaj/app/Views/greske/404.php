<?php /** @var string $naslov @var ?string $poruka */ ?>
<section class="sn-section">
    <div class="container text-center py-5">
        <p class="sn-eyebrow">Greška</p>
        <h1 class="sn-serif display-5"><?= e($naslov ?? 'Stranica nije pronađena') ?></h1>
        <p class="text-muted-2"><?= e($poruka ?? 'Tražena stranica ne postoji ili je premeštena.') ?></p>
        <a href="<?= url('/') ?>" class="btn btn-sn mt-2">Nazad na početnu</a>
    </div>
</section>
