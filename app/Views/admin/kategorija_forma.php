<?php /** @var ?array $kategorija */
$izmena = (bool) $kategorija;
$akcija = $izmena ? url('admin/kategorije/' . $kategorija['id']) : url('admin/kategorije');
?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5" style="max-width: 620px;">
    <h1 class="h3 sn-serif mb-4"><?= $izmena ? 'Izmena kategorije' : 'Nova kategorija' ?></h1>

    <div class="sn-panel">
        <form method="post" action="<?= e($akcija) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Naziv</label>
                <input name="naziv" class="form-control <?= ima_gresku('naziv') ? 'is-invalid' : '' ?>"
                       required value="<?= e(stare('naziv') ?: ($kategorija['naziv'] ?? '')) ?>">
                <div class="invalid-feedback d-block"><?= e(greska('naziv')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Opis</label>
                <textarea name="opis" rows="3" class="form-control"><?= e(stare('opis') ?: ($kategorija['opis'] ?? '')) ?></textarea>
            </div>
            <button class="btn btn-sn"><?= $izmena ? 'Sačuvaj' : 'Dodaj kategoriju' ?></button>
            <a href="<?= url('admin/kategorije') ?>" class="btn btn-link">Odustani</a>
        </form>
    </div>
</div>
