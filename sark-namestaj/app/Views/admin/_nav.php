<div class="container sn-section pb-0">
    <p class="sn-eyebrow mb-1">Administracija</p>
    <div class="d-flex flex-wrap gap-2 border-bottom pb-3 mb-4">
        <a href="<?= url('admin') ?>" class="btn btn-sm <?= nav_active('admin') ? 'btn-sn' : 'btn-outline-sn' ?>">Pregled</a>
        <a href="<?= url('admin/radovi') ?>" class="btn btn-sm <?= str_contains($_SERVER['REQUEST_URI'], '/admin/radovi') ? 'btn-sn' : 'btn-outline-sn' ?>">Radovi</a>
        <a href="<?= url('admin/kategorije') ?>" class="btn btn-sm <?= str_contains($_SERVER['REQUEST_URI'], '/admin/kategorije') ? 'btn-sn' : 'btn-outline-sn' ?>">Kategorije</a>
        <a href="<?= url('admin/upiti') ?>" class="btn btn-sm <?= str_contains($_SERVER['REQUEST_URI'], '/admin/upiti') ? 'btn-sn' : 'btn-outline-sn' ?>">Upiti</a>
        <a href="<?= url('admin/recenzije') ?>" class="btn btn-sm <?= str_contains($_SERVER['REQUEST_URI'], '/admin/recenzije') ? 'btn-sn' : 'btn-outline-sn' ?>">Recenzije</a>
    </div>
</div>
