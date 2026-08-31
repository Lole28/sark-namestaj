<?php /** @var array $rad @var array $slike @var array $slicni */
$u = current_user();
$prva = $slike[0]['putanja'] ?? $rad['naslovna'] ?? null;
?>

<section class="sn-section">
    <div class="container">
        <nav aria-label="breadcrumb" class="small mb-3">
            <a href="<?= url('radovi') ?>">Radovi</a>
            <span class="text-muted-2">/</span>
            <a href="<?= url('radovi?kategorija=' . $rad['kategorija_slug']) ?>"><?= e($rad['kategorija_naziv']) ?></a>
        </nav>

        <div class="row g-4 g-lg-5">
            <div class="col-lg-7">
                <img id="sn-slika-glavna" class="sn-gallery-main"
                     src="<?= e(slika_url($prva)) ?>" alt="<?= e($rad['naziv']) ?>">
                <?php if (count($slike) > 1): ?>
                    <div class="sn-thumbs">
                        <?php foreach ($slike as $i => $s): ?>
                            <img src="<?= e(slika_url($s['putanja'])) ?>"
                                 data-pun="<?= e(slika_url($s['putanja'])) ?>"
                                 alt="<?= e($s['alt_tekst'] ?: $rad['naziv']) ?>"
                                 class="<?= $i === 0 ? 'active' : '' ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-5">
                <span class="sn-badge"><?= e($rad['kategorija_naziv']) ?></span>
                <h1 class="sn-serif mt-2"><?= e($rad['naziv']) ?></h1>
                <p class="text-muted-2"><?= nl2br(e($rad['opis'])) ?></p>

                <dl class="row small mt-3">
                    <?php if ($rad['materijal']): ?>
                        <dt class="col-4 text-muted-2">Materijal</dt>
                        <dd class="col-8"><?= e($rad['materijal']) ?></dd>
                    <?php endif; ?>
                    <?php if ($rad['cena_od'] !== null): ?>
                        <dt class="col-4 text-muted-2">Cena</dt>
                        <dd class="col-8"><span class="fw-bold" data-rsd="<?= e((string) $rad['cena_od']) ?>">od <?= e(dinar($rad['cena_od'])) ?></span></dd>
                    <?php endif; ?>
                </dl>

                <div class="sn-panel mt-4">
                    <h2 class="h5 sn-serif">Zatraži ponudu za sličan komad</h2>
                    <form method="post" action="<?= url('upit') ?>" data-ajax="upit" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="rad_id" value="<?= (int) $rad['id'] ?>">
                        <input type="hidden" name="povratak" value="<?= e(url('radovi/' . $rad['slug'])) ?>">
                        <div data-poruka></div>

                        <div class="mb-2">
                            <label class="form-label small">Ime i prezime</label>
                            <input name="ime" class="form-control" required value="<?= e($u['ime'] ?? '') ?>">
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6 mb-2">
                                <label class="form-label small">E-mail</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label class="form-label small">Telefon</label>
                                <input name="telefon" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Poruka</label>
                            <textarea name="poruka" rows="3" class="form-control" required
                                      placeholder="Dimenzije prostora, željeni materijal, rok…"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sn w-100">Pošalji upit</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($slicni): ?>
            <h2 class="sn-serif mt-5 mb-3">Slični radovi</h2>
            <div class="row g-4">
                <?php foreach ($slicni as $r): ?>
                    <div class="col-sm-6 col-lg-4">
                        <a class="sn-card d-block text-reset" href="<?= url('radovi/' . $r['slug']) ?>">
                            <img class="sn-thumb" src="<?= e(slika_url($r['naslovna'])) ?>" alt="<?= e($r['naziv']) ?>" loading="lazy">
                            <div class="card-body">
                                <h3 class="h6 mb-0"><?= e($r['naziv']) ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
