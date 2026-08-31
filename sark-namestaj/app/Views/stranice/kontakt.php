<?php
$u = current_user();
?>
<section class="sn-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <p class="sn-eyebrow mb-1">Kontakt</p>
                <h1 class="sn-serif mb-3">Zatražite ponudu</h1>
                <p class="text-muted-2">Opišite šta vam treba — dimenzije prostora, željeni stil i materijal,
                    okvirni rok. Odgovaramo u roku od jednog radnog dana.</p>

                <div class="sn-panel">
                    <form method="post" action="<?= url('upit') ?>" data-ajax="upit" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="povratak" value="<?= e(url('kontakt')) ?>">
                        <div data-poruka></div>

                        <div class="mb-3">
                            <label class="form-label">Ime i prezime</label>
                            <input name="ime" class="form-control <?= ima_gresku('ime') ? 'is-invalid' : '' ?>"
                                   required value="<?= e(stare('ime') ?: ($u['ime'] ?? '')) ?>">
                            <div class="invalid-feedback d-block"><?= e(greska('ime')) ?></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" name="email" class="form-control <?= ima_gresku('email') ? 'is-invalid' : '' ?>"
                                       required value="<?= e(stare('email')) ?>">
                                <div class="invalid-feedback d-block"><?= e(greska('email')) ?></div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Telefon <span class="text-muted-2">(opciono)</span></label>
                                <input name="telefon" class="form-control <?= ima_gresku('telefon') ? 'is-invalid' : '' ?>"
                                       value="<?= e(stare('telefon')) ?>">
                                <div class="invalid-feedback d-block"><?= e(greska('telefon')) ?></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Poruka</label>
                            <textarea name="poruka" rows="5" class="form-control <?= ima_gresku('poruka') ? 'is-invalid' : '' ?>"
                                      required><?= e(stare('poruka')) ?></textarea>
                            <div class="invalid-feedback d-block"><?= e(greska('poruka')) ?></div>
                        </div>
                        <button type="submit" class="btn btn-sn btn-lg px-4">Pošalji upit</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="sn-panel mb-4">
                    <h2 class="h5 sn-serif">Radionica</h2>
                    <ul class="list-unstyled small mb-0">
                        <li class="py-1">📍 Bulevar oslobođenja 12, Novi Sad</li>
                        <li class="py-1">📞 <a href="tel:+381641234567">064 123 4567</a></li>
                        <li class="py-1">✉️ <a href="mailto:info@sark-namestaj.rs">info@sark-namestaj.rs</a></li>
                        <li class="py-1">🕗 Pon–Pet 08–16h, Sub 09–13h</li>
                    </ul>
                </div>

                <div class="sn-panel">
                    <h2 class="h5 sn-serif">Ostavite utisak</h2>
                    <p class="small text-muted-2">Ako smo za vas nešto radili, podelite iskustvo.</p>
                    <form method="post" action="<?= url('recenzija') ?>" data-ajax="recenzija" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="povratak" value="<?= e(url('kontakt')) ?>">
                        <div data-poruka></div>
                        <div class="mb-2">
                            <label class="form-label small">Ime</label>
                            <input name="ime" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <span class="form-label small d-block">Ocena</span>
                            <span class="sn-rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="ocena" id="oc<?= $i ?>" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                    <label for="oc<?= $i ?>" title="<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Utisak</label>
                            <textarea name="tekst" rows="3" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-sn">Pošalji recenziju</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
