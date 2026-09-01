<?php /** @var array $kategorije */ ?>
<?php require __DIR__ . '/_nav.php'; ?>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 sn-serif mb-0">Kategorije</h1>
        <a href="<?= url('admin/kategorije/nova') ?>" class="btn btn-sn btn-sm">+ Nova</a>
    </div>

    <div class="sn-panel">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Naziv</th><th>Slug</th><th class="text-center">Radova</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($kategorije as $k): ?>
                    <tr>
                        <td><strong><?= e($k['naziv']) ?></strong><br><span class="small text-muted-2"><?= e(skrati((string) $k['opis'], 70)) ?></span></td>
                        <td class="small text-muted-2"><?= e($k['slug']) ?></td>
                        <td class="text-center"><?= (int) $k['broj_radova'] ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= url('admin/kategorije/' . $k['id'] . '/izmena') ?>" class="btn btn-outline-sn btn-sm">Izmeni</a>
                            <form method="post" action="<?= url('admin/kategorije/' . $k['id'] . '/brisanje') ?>" class="d-inline"
                                  onsubmit="return confirm('Obrisati kategoriju „<?= e($k['naziv']) ?>”?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger btn-sm">Obriši</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
