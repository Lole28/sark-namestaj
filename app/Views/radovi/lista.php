<?php /** @var array $kategorije @var array $radovi @var ?string $izabrana */ ?>

<div class="page-top wrap">
    <p class="eyebrow reveal">Portfolio</p>
    <h1 class="serif-xl reveal" style="--i:1">Radovi</h1>
    <p class="lead reveal" style="--i:2;margin-top:1rem">
        Kuhinje, plakari, dnevne sobe i poslovni prostori — sve od pločastog materijala,
        izrađeno po meri i po dogovoru.
    </p>

    <div class="filter" id="sn-filter">
        <button data-slug="" class="<?= $izabrana ? '' : 'active' ?>">Sve</button>
        <?php foreach ($kategorije as $k): ?>
            <button data-slug="<?= e($k['slug']) ?>" class="<?= $izabrana === $k['slug'] ? 'active' : '' ?>">
                <?= e($k['naziv']) ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<div class="wrap section" style="padding-top:0">
    <div class="grid grid--offset" id="sn-grid">
        <?php if (!$radovi): ?>
            <p class="text-stone">Trenutno nema radova u ovoj kategoriji.</p>
        <?php endif; ?>
        <?php foreach ($radovi as $i => $r): ?>
            <a class="tile reveal" style="--i:<?= $i % 3 ?>" href="<?= url('radovi/' . $r['slug']) ?>">
                <div class="tile__img">
                    <img src="<?= e(slika_thumb($r['naslovna'])) ?>" alt="<?= e($r['naziv']) ?>" loading="lazy">
                </div>
                <div class="tile__cat"><?= e($r['kategorija_naziv']) ?></div>
                <div class="tile__name"><?= e($r['naziv']) ?></div>
                <?php if ($r['cena_od'] !== null): ?>
                    <div class="tile__meta">od <?= e(dinar($r['cena_od'])) ?></div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<section class="cta section--tight section">
    <div class="wrap center">
        <h2 class="reveal" style="font-size:clamp(1.6rem,4vw,2.6rem)">Ne vidite baš to što tražite?</h2>
        <p class="reveal" style="--i:1">Napravićemo ga po vašoj meri.</p>
        <div class="reveal center" style="--i:2;margin-top:1.6rem">
            <a class="mbtn mbtn--light" href="<?= url('kontakt') ?>">
                <span class="mbtn__track"><span>Kontaktirajte nas</span><span>Kontaktirajte nas</span></span>
            </a>
        </div>
    </div>
</section>
