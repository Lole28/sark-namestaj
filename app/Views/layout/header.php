<?php
/** @var string|null $naslov */
$u = current_user();
$overlayNav = $overlayNav ?? false;
$appName = $GLOBALS['config']['app']['name'];
$punNaslov = $naslov ? "{$naslov} — {$appName}" : "{$appName} — nameštaj od pločastog materijala po meri";
?>
<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($punNaslov) ?></title>
    <meta name="description" content="Šark nameštaj po meri — porodična firma za izradu kuhinja, plakara i enterijera od pločastog materijala (iverica i medijapan). Radimo po dogovoru, od mere do montaže.">
    <link rel="icon" href="<?= asset('img/logo-mark.png') ?>">
    <link rel="preload" as="font" type="font/woff2" href="<?= asset('fonts/fraunces-latin.woff2') ?>" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="<?= asset('fonts/hanken-latin.woff2') ?>" crossorigin>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="base-url" content="<?= e(BASE_URL) ?>">
    <script>
        document.documentElement.className += ' js';
        // sigurnosna mreža: ako app.js zakaže, sadržaj se ipak prikaže
        addEventListener('load', function () {
            setTimeout(function () {
                document.querySelectorAll('.reveal:not(.is-in), .reveal-img:not(.is-in), .reveal-line:not(.is-in)')
                    .forEach(function (el) { el.classList.add('is-in'); });
            }, 2600);
        });
    </script>
</head>
<body<?= $overlayNav ? '' : '' ?>>
<a class="skip" href="#glavni">Preskoči na sadržaj</a>

<nav class="nav <?= $overlayNav ? 'nav--over' : 'is-solid' ?>">
    <a class="nav__brand" href="<?= url('/') ?>" aria-label="Šark nameštaj po meri — početna">
        <?php $klasa = 'sn-logo--nav'; $wordmark = true; require __DIR__ . '/_logo.php'; ?>
    </a>

    <button class="nav__toggle" type="button" aria-label="Meni" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>

    <div class="nav__panel">
        <ul class="nav__links">
            <li><a class="nav__link" href="<?= url('/') ?>" <?= nav_active('') ? 'aria-current="page"' : '' ?>>Početna</a></li>
            <li><a class="nav__link" href="<?= url('radovi') ?>" <?= nav_active('radovi') ? 'aria-current="page"' : '' ?>>Radovi</a></li>
            <li><a class="nav__link" href="<?= url('o-nama') ?>" <?= nav_active('o-nama') ? 'aria-current="page"' : '' ?>>O nama</a></li>
            <li><a class="nav__link" href="<?= url('kontakt') ?>" <?= nav_active('kontakt') ? 'aria-current="page"' : '' ?>>Kontakt</a></li>
        </ul>
        <div class="nav__account">
            <?php if (!$u): ?>
                <a class="nav__link" href="<?= url('prijava') ?>">Prijava</a>
            <?php else: ?>
                <?php if ($u['uloga'] === 'admin'): ?>
                    <a class="nav__link" href="<?= url('admin') ?>">Admin</a>
                <?php else: ?>
                    <a class="nav__link" href="<?= url('nalog/upiti') ?>">Moji upiti</a>
                <?php endif; ?>
                <form method="post" action="<?= url('odjava') ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button class="nav__link" style="background:none;border:0;cursor:pointer;font:inherit;letter-spacing:.1em;text-transform:uppercase">Odjava</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php $poruke = flash(); if ($poruke): ?>
<script id="sn-flash" type="application/json"><?= json_encode($poruke, JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>

<main id="glavni">
