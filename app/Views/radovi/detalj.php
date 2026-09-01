<?php
/** @var array $rad @var array $slike @var array $slicni */
$u = current_user();
$naslovna = $slike[0]['putanja'] ?? $rad['naslovna'] ?? null;

$lbDetail = array_map(static function (array $s) use ($rad): array {
    return [
        'slika'      => slika_url($s['putanja']),
        'naziv'      => $rad['naziv'],
        'opis'       => skrati((string) $rad['opis'], 200),
        'kategorija' => $rad['kategorija_naziv'],
        'url'        => url('radovi/' . $rad['slug']),
    ];
}, $slike ?: [['putanja' => $naslovna]]);
?>

<section class="detail__hero">
    <img src="<?= e(slika_url($naslovna)) ?>" alt="<?= e($rad['naziv']) ?>">
</section>

<div class="wrap section" style="padding-top:clamp(2.5rem,5vw,4rem)">
    <p class="crumb">
        <a href="<?= url('radovi') ?>">Radovi</a> /
        <a href="<?= url('radovi?kategorija=' . $rad['kategorija_slug']) ?>"><?= e($rad['kategorija_naziv']) ?></a>
    </p>

    <div class="split split--text-first">
        <div>
            <h1 class="serif-l reveal"><?= e($rad['naziv']) ?></h1>
            <p class="reveal" style="--i:1"><?= nl2br(e($rad['opis'])) ?></p>
            <ul class="spec reveal" style="--i:2">
                <li><span class="k">Kategorija</span><span><?= e($rad['kategorija_naziv']) ?></span></li>
                <?php if ($rad['materijal']): ?>
                    <li><span class="k">Materijal</span><span><?= e($rad['materijal']) ?></span></li>
                <?php endif; ?>
                <?php if ($rad['cena_od'] !== null): ?>
                    <li><span class="k">Cena</span><span>od <?= e(dinar($rad['cena_od'])) ?></span></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="form-card reveal" style="--i:1">
            <p class="eyebrow">Zatražite ponudu</p>
            <h2 style="font-size:1.5rem;margin-bottom:1rem">Sličan komad za vaš prostor</h2>
            <form method="post" action="<?= url('upit') ?>" data-ajax="upit" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="rad_id" value="<?= (int) $rad['id'] ?>">
                <input type="hidden" name="povratak" value="<?= e(url('radovi/' . $rad['slug'])) ?>">
                <div data-msg></div>
                <div class="field">
                    <label>Ime i prezime</label>
                    <input name="ime" required value="<?= e($u['ime'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="field">
                    <label>Telefon <span style="text-transform:none;letter-spacing:0">(opciono)</span></label>
                    <input name="telefon">
                </div>
                <div class="field">
                    <label>Poruka</label>
                    <textarea name="poruka" required placeholder="Dimenzije prostora, željeni dekor, rok…"></textarea>
                </div>
                <button type="submit" class="mbtn mbtn--solid" style="width:100%;justify-content:center">
                    <span class="mbtn__track"><span>Pošalji upit</span><span>Pošalji upit</span></span>
                </button>
            </form>
        </div>
    </div>

    <?php if (count($slike) > 1): ?>
        <div style="margin-top:clamp(3rem,6vw,5rem)" data-lb-scope data-lb-source="detail">
            <p class="eyebrow reveal">Galerija</p>
            <div class="detail__gallery">
                <?php foreach ($slike as $i => $s): ?>
                    <img class="reveal-img" style="--i:<?= $i % 3 ?>"
                         src="<?= e(slika_thumb($s['putanja'])) ?>"
                         alt="<?= e($s['alt_tekst'] ?: $rad['naziv']) ?>"
                         data-lb-idx="<?= $i ?>" loading="lazy">
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($slicni): ?>
<section class="projects section" data-lb-scope>
    <div class="wrap">
        <p class="eyebrow">Slični radovi</p>
        <div class="grid" style="margin-top:2rem">
            <?php foreach ($slicni as $r): ?>
                <a class="tile" href="<?= url('radovi/' . $r['slug']) ?>" style="color:var(--paper)">
                    <div class="tile__img"><img src="<?= e(slika_thumb($r['naslovna'])) ?>" alt="<?= e($r['naziv']) ?>" loading="lazy"></div>
                    <div class="tile__cat" style="color:var(--stone)"><?= e($r['kategorija_naziv']) ?></div>
                    <div class="tile__name"><?= e($r['naziv']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script id="sn-detail-data" type="application/json"><?= json_encode($lbDetail, JSON_UNESCAPED_UNICODE) ?></script>
