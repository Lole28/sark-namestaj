<?php /** @var string $naslov @var ?string $poruka */ ?>
<div class="page-top wrap center" style="min-height:60vh;display:flex;flex-direction:column;justify-content:center">
    <p class="eyebrow" style="justify-content:center">Greška</p>
    <h1 class="serif-xl"><?= e($naslov ?? 'Stranica nije pronađena') ?></h1>
    <p class="lead" style="margin:1rem auto 2rem"><?= e($poruka ?? 'Tražena stranica ne postoji ili je premeštena.') ?></p>
    <div class="center">
        <a class="mbtn mbtn--solid" href="<?= url('/') ?>">
            <span class="mbtn__track"><span>Na početnu</span><span>Na početnu</span></span>
        </a>
    </div>
</div>
