</main>

<footer class="sn-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <h3 class="h5 sn-serif">Šark nameštaj po meri</h3>
                <p class="small mb-2">Porodična stolarska radionica. Kuhinje, plakari, ormari i enterijeri
                    izrađeni po vašoj meri, od kvalitetnog materijala.</p>
            </div>
            <div class="col-6 col-md-4">
                <h4 class="h6 text-uppercase small fw-bold">Stranice</h4>
                <ul class="list-unstyled small">
                    <li><a href="<?= url('radovi') ?>">Radovi</a></li>
                    <li><a href="<?= url('usluge') ?>">Usluge</a></li>
                    <li><a href="<?= url('o-nama') ?>">O nama</a></li>
                    <li><a href="<?= url('kontakt') ?>">Kontakt</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h4 class="h6 text-uppercase small fw-bold">Kontakt</h4>
                <ul class="list-unstyled small">
                    <li>Radionica: Bulevar oslobođenja 12, Novi Sad</li>
                    <li>Telefon: <a href="tel:+381641234567">064 123 4567</a></li>
                    <li>E-mail: <a href="mailto:info@sark-namestaj.rs">info@sark-namestaj.rs</a></li>
                    <li>Pon–Pet 08–16h, Sub 09–13h</li>
                </ul>
            </div>
        </div>
        <hr class="border-light opacity-25">
        <div class="d-flex flex-wrap justify-content-between small">
            <span>&copy; <?= date('Y') ?> Šark nameštaj po meri</span>
            <span>Studentski projekat — MVC (PHP &amp; MySQL)</span>
        </div>
    </div>
</footer>

<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
