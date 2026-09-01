<?php
/** @var array $galerija @var array $recenzije @var int $brojRadova */

// JSON za lightbox
$lb = array_map(static function (array $s): array {
    return [
        'slika'      => slika_url($s['putanja']),
        'naziv'      => $s['rad_naziv'],
        'opis'       => skrati((string) $s['rad_opis'], 200),
        'kategorija' => $s['kategorija_naziv'],
        'url'        => url('radovi/' . $s['rad_slug']),
    ];
}, $galerija);
?>

<header class="hero">
    <div class="hero__media">
        <img src="<?= asset('img/portfolio/p01.jpg') ?>" alt="Kuhinja po meri u belom i dekoru hrasta">
    </div>
    <div class="wrap hero__inner">
        <h1 class="reveal-line">
            <span>Nameštaj po meri,</span> <span>od table do doma.</span>
        </h1>
        <p class="hero__sub reveal" style="--i:2">
            Porodična radionica za izradu kuhinja, plakara i kompletnih enterijera
            od iverice i medijapana. Od zajedničke ideje i mere na licu mesta —
            do montaže bez stresa.
        </p>
        <div class="reveal" style="--i:3">
            <a class="mbtn mbtn--light" href="<?= url('kontakt') ?>">
                <span class="mbtn__track"><span>Kontaktirajte nas</span><span>Kontaktirajte nas</span></span>
            </a>
        </div>
    </div>
    <span class="hero__scroll">Skrolujte</span>
</header>

<section class="section wrap wrap--narrow">
    <p class="eyebrow reveal">Šta radimo</p>
    <p class="statement reveal" style="--i:1">
        Projektujemo, izrađujemo i montiramo nameštaj koji tačno odgovara
        vašem prostoru — <em>i vašem načinu života</em>.
    </p>
</section>

<section class="section split split--wide wrap" style="padding-top:0">
    <div class="split__media reveal-img">
        <img src="<?= asset('img/portfolio/p09.jpg') ?>" alt="Trpezarijski sto sa lamela zidom">
    </div>
    <div>
        <p class="eyebrow reveal">O nama</p>
        <h2 class="serif-l reveal" style="--i:1">Mala porodična firma sa velikom pažnjom prema detalju</h2>
        <p class="reveal" style="--i:2">
            Šark je porodična radionica. Nismo najveći i ne želimo da budemo —
            želimo da svaki klijent dobije nameštaj napravljen kao za nas same.
            Radimo isključivo po dogovoru: prvo saslušamo šta vam treba, izađemo na
            teren, uzmemo mere i predložimo rešenje, a tek onda krećemo u izradu.
        </p>
        <ul class="about-values reveal" style="--i:3">
            <li><span class="n">01</span><div><h3>Porodično</h3><p>Iza svakog projekta stoji ime porodice, ne samo firma.</p></div></li>
            <li><span class="n">02</span><div><h3>Lojalni</h3><p>Uz klijenta smo i posle montaže — servis, dopune, savet.</p></div></li>
            <li><span class="n">03</span><div><h3>Istrajni</h3><p>Ne odustajemo od komplikovanih mera i netipičnih prostora.</p></div></li>
            <li><span class="n">04</span><div><h3>Po dogovoru</h3><p>Sve — od materijala i rokova do cene — dogovaramo unapred i jasno.</p></div></li>
        </ul>
        <div class="mt-cta reveal" style="--i:4">
            <a class="mbtn" href="<?= url('o-nama') ?>">
                <span class="mbtn__track"><span>Više o nama</span><span>Više o nama</span></span>
            </a>
        </div>
    </div>
</section>

<section class="projects section" id="projekti" data-lb-scope>
    <div class="wrap">
        <div class="projects__head">
            <div>
                <p class="eyebrow reveal">Naši radovi</p>
                <h2 class="serif-l reveal" style="--i:1">Preciznost u praksi</h2>
            </div>
            <div class="reveal" style="--i:2">
                <a class="mbtn mbtn--light" href="<?= url('radovi') ?>">
                    <span class="mbtn__track"><span>Svi radovi</span><span>Svi radovi</span></span>
                </a>
            </div>
        </div>
    </div>

    <div class="marquee">
        <div class="marquee__track">
            <?php for ($pass = 0; $pass < 2; $pass++): ?>
                <?php foreach ($galerija as $i => $s): ?>
                    <button class="pcard" type="button" data-lb-idx="<?= $i ?>"
                            <?= $pass ? 'aria-hidden="true" tabindex="-1"' : '' ?>>
                        <div class="pcard__img">
                            <img src="<?= e(slika_thumb($s['putanja'])) ?>" alt="<?= e($s['alt_tekst'] ?: $s['rad_naziv']) ?>" loading="lazy">
                        </div>
                        <div class="pcard__meta">
                            <div>
                                <div class="cat"><?= e($s['kategorija_naziv']) ?></div>
                                <div class="pcard__name"><?= e($s['rad_naziv']) ?></div>
                            </div>
                            <span aria-hidden="true">↗</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php if ($recenzije): ?>
<section class="section wrap" id="sn-tst-wrap">
    <p class="eyebrow center reveal" style="justify-content:center">Šta kažu klijenti</p>
    <div class="tst" id="sn-tst">
        <div class="tst__quote">
            <?php foreach ($recenzije as $r): ?>
                <div class="tst__slide">
                    <span>„<?= e($r['tekst']) ?>”</span>
                    <div class="tst__stars" aria-hidden="true"><?= str_repeat('★', (int) $r['ocena']) ?></div>
                    <div class="tst__author"><?= e($r['ime']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="tst__dots" aria-hidden="true"></div>
    </div>
</section>
<?php endif; ?>

<section class="cta section">
    <div class="wrap">
        <p class="eyebrow center reveal" style="justify-content:center;color:var(--stone)">Krenimo</p>
        <h2 class="reveal" style="--i:1">Imate ideju? Napravimo je.</h2>
        <p class="reveal" style="--i:2">Pošaljite nam par rečenica o prostoru i želji — dobićete okvirnu ponudu, bez obaveze.</p>
        <div class="reveal center" style="--i:3;margin-top:2rem">
            <a class="mbtn mbtn--light" href="<?= url('kontakt') ?>">
                <span class="mbtn__track"><span>Zatražite ponudu</span><span>Zatražite ponudu</span></span>
            </a>
        </div>
    </div>
</section>

<script id="sn-gallery-data" type="application/json"><?= json_encode($lb, JSON_UNESCAPED_UNICODE) ?></script>
