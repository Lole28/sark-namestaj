<?php
$u = current_user();
$tema = isset($_GET['tema']) ? trim((string) $_GET['tema']) : '';
$temaPoruka = ($tema !== '' && stare('poruka') === '')
    ? 'Interesuje me nešto slično kao: „' . mb_substr($tema, 0, 120) . "”.\n\n"
    : '';
?>

<div class="page-top wrap">
    <p class="eyebrow reveal">Kontakt</p>
    <h1 class="serif-xl reveal" style="--i:1">Zatražite ponudu</h1>
    <p class="lead reveal" style="--i:2;margin-top:1rem">
        Opišite prostor i ideju — dimenzije, željeni dekor, okvirni rok. Odgovaramo
        u roku od jednog radnog dana.
    </p>
</div>

<div class="wrap section" style="padding-top:clamp(2rem,4vw,3rem)">
    <div class="split split--text-first">
        <div class="form-card reveal">
            <form method="post" action="<?= url('upit') ?>" data-ajax="upit" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="povratak" value="<?= e(url('kontakt')) ?>">
                <div data-msg></div>
                <div class="field <?= ima_gresku('ime') ? 'has-error' : '' ?>">
                    <label>Ime i prezime</label>
                    <input name="ime" required value="<?= e(stare('ime') ?: ($u['ime'] ?? '')) ?>">
                    <?php if (ima_gresku('ime')): ?><div class="field__err"><?= e(greska('ime')) ?></div><?php endif; ?>
                </div>
                <div class="field <?= ima_gresku('email') ? 'has-error' : '' ?>">
                    <label>E-mail</label>
                    <input type="email" name="email" required value="<?= e(stare('email')) ?>">
                    <?php if (ima_gresku('email')): ?><div class="field__err"><?= e(greska('email')) ?></div><?php endif; ?>
                </div>
                <div class="field <?= ima_gresku('telefon') ? 'has-error' : '' ?>">
                    <label>Telefon <span style="text-transform:none;letter-spacing:0">(opciono)</span></label>
                    <input name="telefon" value="<?= e(stare('telefon')) ?>">
                    <?php if (ima_gresku('telefon')): ?><div class="field__err"><?= e(greska('telefon')) ?></div><?php endif; ?>
                </div>
                <div class="field <?= ima_gresku('poruka') ? 'has-error' : '' ?>">
                    <label>Poruka</label>
                    <textarea name="poruka" required><?= e(stare('poruka') ?: $temaPoruka) ?></textarea>
                    <?php if (ima_gresku('poruka')): ?><div class="field__err"><?= e(greska('poruka')) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="mbtn mbtn--solid">
                    <span class="mbtn__track"><span>Pošalji upit</span><span>Pošalji upit</span></span>
                </button>
            </form>
        </div>

        <div class="stack">
            <p class="eyebrow">Radionica</p>
            <ul class="spec" style="margin-top:0">
                <li><span class="k">Adresa</span><span>Bulevar oslobođenja 12, Novi Sad</span></li>
                <li><span class="k">Telefon</span><span><a href="tel:+381641234567">064 123 4567</a></span></li>
                <li><span class="k">E-mail</span><span><a href="mailto:info@sark-namestaj.rs">info@sark-namestaj.rs</a></span></li>
                <li><span class="k">Radno vreme</span><span>Pon–Pet 08–16h · Sub 09–13h</span></li>
            </ul>

            <div style="margin-top:2.5rem">
                <p class="eyebrow">Ostavite utisak</p>
                <form method="post" action="<?= url('recenzija') ?>" data-ajax="recenzija" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="povratak" value="<?= e(url('kontakt')) ?>">
                    <div data-msg></div>
                    <div class="field"><label>Ime</label><input name="ime" required></div>
                    <div class="field">
                        <label>Ocena</label>
                        <select name="ocena">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>
                    <div class="field"><label>Utisak</label><textarea name="tekst" required></textarea></div>
                    <button type="submit" class="mbtn mbtn--sm">
                        <span class="mbtn__track"><span>Pošalji recenziju</span><span>Pošalji recenziju</span></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
