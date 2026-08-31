<?php /** @var array $istaknuti @var array $kategorije @var array $recenzije */ ?>

<header class="sn-hero" style="--hero-img:url('<?= asset('img/hero.svg') ?>')">
    <div class="container">
        <p class="sn-eyebrow text-white-50">Stolarska radionica · Novi Sad</p>
        <h1 class="sn-serif">Nameštaj po vašoj meri, izrađen ručno</h1>
        <p>Kuhinje, plakari, ormari i kompletni enterijeri. Od ideje i mere na licu mesta,
            preko 3D predloga, do ugradnje — sve na jednom mestu.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="<?= url('radovi') ?>" class="btn btn-sn btn-lg px-4">Pogledaj radove</a>
            <a href="<?= url('kontakt') ?>" class="btn btn-outline-light btn-lg px-4">Zatraži ponudu</a>
        </div>
    </div>
</header>

<section class="sn-section">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <p class="sn-eyebrow">Zašto Šark</p>
                <h2 class="sn-serif mb-3">Precizna izrada, iskren dogovor</h2>
                <p class="text-muted-2">Radionicu vodi porodica Šarković već petnaest godina. Svaki komad
                    pravimo od kvalitetnih ploča i masiva, uz garanciju na izradu i okov.</p>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    <?php foreach ([
                        ['Mera i savet besplatno', 'Dolazimo na adresu, uzimamo mere i predlažemo rešenje.'],
                        ['3D predlog pre izrade', 'Vidite kako će nameštaj izgledati u vašem prostoru.'],
                        ['Rok 3–5 nedelja', 'Jasan termin ugradnje dogovoren unapred.'],
                        ['Garancija 24 meseca', 'Na izradu, okov i mehanizme.'],
                    ] as [$n, $o]): ?>
                        <div class="col-sm-6">
                            <div class="sn-panel h-100">
                                <h3 class="h6 mb-1"><?= e($n) ?></h3>
                                <p class="small text-muted-2 mb-0"><?= e($o) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($istaknuti): ?>
<section class="sn-section pt-0">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <p class="sn-eyebrow mb-1">Iz radionice</p>
                <h2 class="sn-serif mb-0">Izdvojeni radovi</h2>
            </div>
            <a href="<?= url('radovi') ?>" class="btn btn-outline-sn btn-sm">Svi radovi</a>
        </div>
        <div class="row g-4">
            <?php foreach ($istaknuti as $r): ?>
                <div class="col-sm-6 col-lg-3">
                    <a class="sn-card d-block text-reset" href="<?= url('radovi/' . $r['slug']) ?>">
                        <img class="sn-thumb" src="<?= e(slika_url($r['naslovna'])) ?>" alt="<?= e($r['naziv']) ?>" loading="lazy">
                        <div class="card-body">
                            <span class="sn-badge"><?= e($r['kategorija_naziv']) ?></span>
                            <h3 class="h6 mt-2 mb-1"><?= e($r['naziv']) ?></h3>
                            <?php if ($r['cena_od'] !== null): ?>
                                <span class="small fw-bold">od <?= e(dinar($r['cena_od'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="sn-section pt-0">
    <div class="container">
        <p class="sn-eyebrow mb-1">Šta radimo</p>
        <h2 class="sn-serif mb-4">Kategorije nameštaja</h2>
        <div class="row g-3">
            <?php foreach ($kategorije as $k): ?>
                <div class="col-6 col-md-4">
                    <a href="<?= url('radovi?kategorija=' . $k['slug']) ?>" class="sn-panel d-block text-reset h-100">
                        <h3 class="h6 mb-1"><?= e($k['naziv']) ?></h3>
                        <p class="small text-muted-2 mb-0"><?= e(skrati((string) $k['opis'], 80)) ?></p>
                        <span class="small text-muted-2"><?= (int) $k['broj_radova'] ?> radova</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($recenzije): ?>
<section class="sn-section pt-0">
    <div class="container">
        <p class="sn-eyebrow mb-1">Utisci klijenata</p>
        <h2 class="sn-serif mb-4">Recenzije</h2>
        <div class="row g-4">
            <?php foreach ($recenzije as $rec): ?>
                <div class="col-md-4">
                    <div class="sn-panel h-100">
                        <div class="sn-stars mb-2" aria-label="Ocena <?= (int) $rec['ocena'] ?> od 5">
                            <?= str_repeat('★', (int) $rec['ocena']) . str_repeat('☆', 5 - (int) $rec['ocena']) ?>
                        </div>
                        <p class="mb-2">„<?= e($rec['tekst']) ?>”</p>
                        <p class="small text-muted-2 mb-0">— <?= e($rec['ime']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="sn-section">
    <div class="container">
        <div class="sn-panel text-center py-5">
            <h2 class="sn-serif">Imate ideju? Napravimo je.</h2>
            <p class="text-muted-2 mb-4">Pošaljite nam nekoliko rečenica o tome šta vam treba i dobićete okvirnu ponudu.</p>
            <a href="<?= url('kontakt') ?>" class="btn btn-sn btn-lg px-4">Zatraži besplatnu ponudu</a>
        </div>
    </div>
</section>
