<?php $uri = $_SERVER['REQUEST_URI']; ?>
<div class="wrap admin-shell">
    <p class="eyebrow">Administracija</p>
    <nav class="admin-nav">
        <a href="<?= url('admin') ?>" class="<?= rtrim($uri, '/') === rtrim(BASE_URL . 'admin', '/') ? 'active' : '' ?>">Pregled</a>
        <a href="<?= url('admin/radovi') ?>" class="<?= str_contains($uri, '/admin/radovi') ? 'active' : '' ?>">Radovi</a>
        <a href="<?= url('admin/kategorije') ?>" class="<?= str_contains($uri, '/admin/kategorije') ? 'active' : '' ?>">Kategorije</a>
        <a href="<?= url('admin/upiti') ?>" class="<?= str_contains($uri, '/admin/upiti') ? 'active' : '' ?>">Upiti</a>
        <a href="<?= url('admin/recenzije') ?>" class="<?= str_contains($uri, '/admin/recenzije') ? 'active' : '' ?>">Recenzije</a>
    </nav>
</div>
