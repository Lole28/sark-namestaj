<?php /** @var array $kategorije @var array $radovi @var ?string $izabrana */ ?>

<section class="sn-section">
    <div class="container">
        <p class="sn-eyebrow mb-1">Portfolio</p>
        <h1 class="sn-serif mb-4">Naši radovi</h1>

        <div class="sn-filter mb-4" id="sn-filter">
            <button data-slug="" class="<?= $izabrana ? '' : 'active' ?>">Sve</button>
            <?php foreach ($kategorije as $k): ?>
                <button data-slug="<?= e($k['slug']) ?>" class="<?= $izabrana === $k['slug'] ? 'active' : '' ?>">
                    <?= e($k['naziv']) ?> <span class="opacity-75">(<?= (int) $k['broj_radova'] ?>)</span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4" id="sn-radovi-mreza">
            <?php if (!$radovi): ?>
                <div class="col-12"><p class="text-muted-2 py-4">Trenutno nema radova u ovoj kategoriji.</p></div>
            <?php endif; ?>
            <?php foreach ($radovi as $r): ?>
                <div class="col-sm-6 col-lg-4">
                    <a class="sn-card d-block text-reset" href="<?= url('radovi/' . $r['slug']) ?>">
                        <img class="sn-thumb" src="<?= e(slika_url($r['naslovna'])) ?>" alt="<?= e($r['naziv']) ?>" loading="lazy">
                        <div class="card-body">
                            <span class="sn-badge"><?= e($r['kategorija_naziv']) ?></span>
                            <h2 class="h5 mt-2 mb-1"><?= e($r['naziv']) ?></h2>
                            <p class="small text-muted-2 mb-2"><?= e(skrati((string) $r['opis'], 110)) ?></p>
                            <?php if ($r['cena_od'] !== null): ?>
                                <span class="fw-bold">od <?= e(dinar($r['cena_od'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
