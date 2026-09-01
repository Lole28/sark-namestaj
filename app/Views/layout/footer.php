</main>

<footer class="foot">
    <div class="wrap">
        <div class="foot__grid">
            <div>
                <a href="<?= url('/') ?>" class="foot__brand" style="display:inline-flex;align-items:center;gap:.8rem;margin-bottom:1.3rem">
                    <?php $klasa = 'sn-logo--foot'; $wordmark = false; require __DIR__ . '/_logo.php'; ?>
                    <span class="nav__wordmark" style="color:var(--paper)">Šark<span style="color:var(--stone)">Nameštaj po meri</span></span>
                </a>
                <p style="max-width:34ch">Porodična firma za izradu nameštaja po meri od pločastog materijala —
                    iverice i medijapana. Lojalni, istrajni, i sve radimo po dogovoru.</p>
            </div>
            <div>
                <h4>Stranice</h4>
                <ul>
                    <li><a href="<?= url('radovi') ?>">Radovi</a></li>
                    <li><a href="<?= url('o-nama') ?>">O nama</a></li>
                    <li><a href="<?= url('kontakt') ?>">Kontakt</a></li>
                    <?php if (!current_user()): ?><li><a href="<?= url('prijava') ?>">Prijava</a></li><?php endif; ?>
                </ul>
            </div>
            <div>
                <h4>Kontakt</h4>
                <ul>
                    <li>Radionica: Novi Sad</li>
                    <li><a href="tel:+381641234567">064 123 4567</a></li>
                    <li><a href="mailto:info@sark-namestaj.rs">info@sark-namestaj.rs</a></li>
                    <li>Pon–Pet 08–16h · Sub 09–13h</li>
                </ul>
            </div>
        </div>
        <div class="foot__bottom">
            <span>&copy; <?= date('Y') ?> Šark nameštaj po meri</span>
            <span>Nameštaj po meri od iverice i medijapana</span>
        </div>
    </div>
</footer>

<!-- Lightbox (deli se za sve galerije) -->
<div class="lightbox" id="sn-lightbox" role="dialog" aria-modal="true" aria-label="Uvećan prikaz rada">
    <div class="lightbox__inner">
        <button class="lightbox__close" type="button">Zatvori &times;</button>
        <div class="lightbox__figure">
            <button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="Prethodni">&#8249;</button>
            <img id="lb-img" src="" alt="">
            <button class="lightbox__nav lightbox__nav--next" type="button" aria-label="Sledeći">&#8250;</button>
        </div>
        <div class="lightbox__body">
            <div class="lightbox__text">
                <div class="cat" id="lb-cat"></div>
                <h3 id="lb-title"></h3>
                <p id="lb-desc"></p>
                <a id="lb-vidi" href="<?= url('radovi') ?>" style="font-size:.8rem;letter-spacing:.06em;text-decoration:underline;color:var(--stone-deep);display:inline-block;margin-top:.7rem">Pogledaj ceo rad →</a>
            </div>
            <a class="mbtn mbtn--solid mbtn--sm" href="<?= url('kontakt') ?>" id="lb-kontakt">
                <span class="mbtn__track"><span>Kontaktirajte nas</span><span>Kontaktirajte nas</span></span>
            </a>
        </div>
    </div>
</div>

<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
