<?php /** @var ?array $rad @var array $slike @var array $kategorije */
$izmena = (bool) $rad;
$akcija = $izmena ? url('admin/radovi/' . $rad['id']) : url('admin/radovi');
$v = static fn(string $k, string $d = '') => e(stare($k) !== '' ? stare($k) : ($rad[$k] ?? $d));
?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <h1 class="h3 sn-serif mb-4"><?= $izmena ? 'Izmena rada' : 'Novi rad' ?></h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="sn-panel">
                <form method="post" action="<?= e($akcija) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Naziv</label>
                        <input name="naziv" class="form-control <?= ima_gresku('naziv') ? 'is-invalid' : '' ?>"
                               required value="<?= $v('naziv') ?>">
                        <div class="invalid-feedback d-block"><?= e(greska('naziv')) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategorija</label>
                        <select name="kategorija_id" class="form-select <?= ima_gresku('kategorija_id') ? 'is-invalid' : '' ?>" required>
                            <option value="">— izaberi —</option>
                            <?php foreach ($kategorije as $k): ?>
                                <option value="<?= (int) $k['id'] ?>"
                                    <?= (int) (stare('kategorija_id') ?: ($rad['kategorija_id'] ?? 0)) === (int) $k['id'] ? 'selected' : '' ?>>
                                    <?= e($k['naziv']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback d-block"><?= e(greska('kategorija_id')) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opis</label>
                        <textarea name="opis" rows="5" class="form-control <?= ima_gresku('opis') ? 'is-invalid' : '' ?>"
                                  required><?= $v('opis') ?></textarea>
                        <div class="invalid-feedback d-block"><?= e(greska('opis')) ?></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-7 mb-3">
                            <label class="form-label">Materijal</label>
                            <input name="materijal" class="form-control" value="<?= $v('materijal') ?>"
                                   placeholder="npr. hrast masiv + Egger ploča">
                        </div>
                        <div class="col-sm-5 mb-3">
                            <label class="form-label">Cena od (RSD)</label>
                            <input name="cena_od" type="number" min="0" step="100"
                                   class="form-control <?= ima_gresku('cena_od') ? 'is-invalid' : '' ?>"
                                   value="<?= $v('cena_od') ?>">
                            <div class="invalid-feedback d-block"><?= e(greska('cena_od')) ?></div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="istaknut" value="1" class="form-check-input" id="ist"
                            <?= !empty($rad['istaknut']) || stare('istaknut') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ist">Prikaži na početnoj (izdvojeni rad)</label>
                    </div>
                    <button class="btn btn-sn"><?= $izmena ? 'Sačuvaj izmene' : 'Kreiraj rad' ?></button>
                    <a href="<?= url('admin/radovi') ?>" class="btn btn-link">Nazad na listu</a>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="sn-panel">
                <h2 class="h5 sn-serif">Slike</h2>
                <?php if (!$izmena): ?>
                    <p class="small text-muted-2">Sačuvajte rad da biste dodali slike.</p>
                <?php else: ?>
                    <form id="sn-upload-forma">
                        <?= csrf_field() ?>
                        <input type="hidden" name="rad_id" value="<?= (int) $rad['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label small">Nova slika (JPG/PNG/WEBP, do 3 MB)</label>
                            <input type="file" name="slika" accept="image/jpeg,image/png,image/webp" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Opis slike (alt tekst)</label>
                            <input name="alt_tekst" class="form-control" placeholder="npr. kuhinja iz ugla">
                        </div>
                        <div id="sn-upload-pregled"></div>
                        <button type="submit" class="btn btn-outline-sn btn-sm mt-2">Otpremi sliku</button>
                    </form>

                    <div class="row g-2 mt-3" id="sn-slike-lista">
                        <?php foreach ($slike as $s): ?>
                            <div class="col-4 col-md-3" data-slika="<?= (int) $s['id'] ?>">
                                <div class="position-relative">
                                    <img src="<?= e(slika_url($s['putanja'])) ?>" alt="<?= e($s['alt_tekst']) ?>" class="img-fluid rounded">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                            data-obrisi-sliku="<?= (int) $s['id'] ?>" aria-label="Obriši">&times;</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
