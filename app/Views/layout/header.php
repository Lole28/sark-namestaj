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
<body>
<a class="skip" href="#glavni">Preskoči na sadržaj</a>

<?php
// Nalog link (prikazuje se malim, „utility" stilom sa desne strane)
$nalogLink = static function () use ($u): void {
    if (!$u) {
        echo '<a class="nav__link nav__link--util" href="' . url('prijava') . '">Prijava</a>';
        return;
    }
    $cilj = $u['uloga'] === 'admin' ? url('admin') : url('nalog/upiti');
    $tekst = $u['uloga'] === 'admin' ? 'Admin' : 'Moji upiti';
    echo '<a class="nav__link nav__link--util" href="' . $cilj . '">' . $tekst . '</a>';
    echo '<form method="post" action="' . url('odjava') . '" style="display:inline">' . csrf_field()
       . '<button class="nav__link nav__link--util" style="background:none;border:0;cursor:pointer;font:inherit">Odjava</button></form>';
};
?>
<nav class="nav <?= $overlayNav ? 'nav--over' : 'is-solid' ?>">
    <button class="nav__toggle" type="button" aria-label="Meni" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>

    <div class="nav__side nav__side--left">
        <a class="nav__link" href="<?= url('radovi') ?>" <?= nav_active('radovi') ? 'aria-current="page"' : '' ?>>Radovi</a>
    </div>

    <a class="nav__brand" href="<?= url('/') ?>" aria-label="Šark nameštaj po meri — početna">
        <?php $klasa = 'sn-logo--nav'; $wordmark = false; require __DIR__ . '/_logo.php'; ?>
    </a>

    <div class="nav__side nav__side--right">
        <a class="nav__link" href="<?= url('o-nama') ?>" <?= nav_active('o-nama') ? 'aria-current="page"' : '' ?>>O nama</a>
        <a class="nav__link" href="<?= url('kontakt') ?>" <?= nav_active('kontakt') ? 'aria-current="page"' : '' ?>>Kontakt</a>
        <span class="nav__util"><?php $nalogLink(); ?></span>
    </div>

    <div class="nav__panel">
        <ul class="nav__links">
            <li><a class="nav__link" href="<?= url('radovi') ?>">Radovi</a></li>
            <li><a class="nav__link" href="<?= url('o-nama') ?>">O nama</a></li>
            <li><a class="nav__link" href="<?= url('kontakt') ?>">Kontakt</a></li>
        </ul>
        <div class="nav__account"><?php $nalogLink(); ?></div>
    </div>
</nav>

<?php $poruke = flash(); if ($poruke): ?>
<script id="sn-flash" type="application/json"><?= json_encode($poruke, JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>

<main id="glavni">
