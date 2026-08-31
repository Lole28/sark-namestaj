<?php /** @var array $kategorije */ ?>
<section class="sn-section">
    <div class="container">
        <p class="sn-eyebrow mb-1">Usluge</p>
        <h1 class="sn-serif mb-4">Šta izrađujemo</h1>

        <div class="row g-4">
            <?php foreach ($kategorije as $k): ?>
                <div class="col-md-6">
                    <div class="sn-panel h-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <h2 class="h4 sn-serif"><?= e($k['naziv']) ?></h2>
                            <span class="sn-badge"><?= (int) $k['broj_radova'] ?> radova</span>
                        </div>
                        <p class="text-muted-2"><?= e($k['opis']) ?></p>
                        <a href="<?= url('radovi?kategorija=' . $k['slug']) ?>" class="btn btn-outline-sn btn-sm">
                            Primeri iz ove kategorije
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sn-panel mt-4">
            <h2 class="h5 sn-serif">Cene</h2>
            <p class="text-muted-2 mb-0">Svaki projekat je jedinstven, pa cenu formiramo nakon uzimanja mere.
                Okvirno: kuhinje od 90.000 RSD/m′, ugradni plakari od 22.000 RSD/m², kupatilski elementi od 18.000 RSD.
                Ponuda je besplatna i bez obaveze.</p>
        </div>
    </div>
</section>
