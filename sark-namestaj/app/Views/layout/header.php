<?php
/** @var string|null $naslov */
$u = current_user();
$punNaslov = $naslov ? "{$naslov} · " . $GLOBALS['config']['app']['name'] : $GLOBALS['config']['app']['name'];
?>
<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($punNaslov) ?></title>
    <meta name="description" content="Šark nameštaj po meri — izrada kuhinja, plakara, ormara i enterijera po meri. Ručna izrada, kvalitetan materijal, dogovorena cena.">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="base-url" content="<?= e(BASE_URL) ?>">
</head>
<body>
<a class="visually-hidden-focusable skip-link" href="#glavni">Preskoči na sadržaj</a>

<nav class="navbar navbar-expand-lg sn-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="" width="34" height="34">
            <span>Šark<span class="text-muted-2"> nameštaj po meri</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
                aria-controls="nav" aria-expanded="false" aria-label="Meni">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= nav_active('') ?>" href="<?= url('/') ?>">Početna</a></li>
                <li class="nav-item"><a class="nav-link <?= nav_active('radovi') ?>" href="<?= url('radovi') ?>">Radovi</a></li>
                <li class="nav-item"><a class="nav-link <?= nav_active('usluge') ?>" href="<?= url('usluge') ?>">Usluge</a></li>
                <li class="nav-item"><a class="nav-link <?= nav_active('o-nama') ?>" href="<?= url('o-nama') ?>">O nama</a></li>
                <li class="nav-item"><a class="nav-link <?= nav_active('kontakt') ?>" href="<?= url('kontakt') ?>">Kontakt</a></li>
            </ul>
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <?php if (!$u): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('prijava') ?>">Prijava</a></li>
                    <li class="nav-item"><a class="btn btn-sn btn-sm px-3" href="<?= url('registracija') ?>">Registracija</a></li>
                <?php else: ?>
                    <?php if ($u['uloga'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= nav_active('admin') ?>" href="<?= url('admin') ?>">Administracija</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link <?= nav_active('nalog/upiti') ?>" href="<?= url('nalog/upiti') ?>">Moji upiti</a></li>
                    <?php endif; ?>
                    <li class="nav-item d-flex align-items-center">
                        <span class="navbar-text me-2 small">Zdravo, <?= e($u['ime']) ?></span>
                        <form method="post" action="<?= url('odjava') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-sn btn-sm">Odjava</button>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php $poruke = flash(); ?>
<?php if ($poruke): ?>
    <div class="container mt-3" id="flash-poruke">
        <?php foreach ($poruke as $p):
            $klasa = ['uspeh' => 'success', 'greska' => 'danger', 'info' => 'info'][$p['tip']] ?? 'secondary'; ?>
            <div class="alert alert-<?= $klasa ?> alert-dismissible fade show" role="alert">
                <?= e($p['poruka']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zatvori"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main id="glavni">
