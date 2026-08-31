<section class="sn-section">
    <div class="container" style="max-width: 500px;">
        <h1 class="sn-serif mb-4 text-center">Registracija</h1>
        <div class="sn-panel">
            <form method="post" action="<?= url('registracija') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Ime i prezime</label>
                    <input name="ime" class="form-control <?= ima_gresku('ime') ? 'is-invalid' : '' ?>"
                           required value="<?= e(stare('ime')) ?>">
                    <div class="invalid-feedback d-block"><?= e(greska('ime')) ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Korisničko ime</label>
                    <input name="korisnicko_ime" class="form-control <?= ima_gresku('korisnicko_ime') ? 'is-invalid' : '' ?>"
                           required value="<?= e(stare('korisnicko_ime')) ?>">
                    <div class="invalid-feedback d-block"><?= e(greska('korisnicko_ime')) ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control <?= ima_gresku('email') ? 'is-invalid' : '' ?>"
                           required value="<?= e(stare('email')) ?>">
                    <div class="invalid-feedback d-block"><?= e(greska('email')) ?></div>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Lozinka</label>
                        <input type="password" name="lozinka" class="form-control <?= ima_gresku('lozinka') ? 'is-invalid' : '' ?>" required>
                        <div class="invalid-feedback d-block"><?= e(greska('lozinka')) ?></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Ponovi lozinku</label>
                        <input type="password" name="lozinka2" class="form-control <?= ima_gresku('lozinka2') ? 'is-invalid' : '' ?>" required>
                        <div class="invalid-feedback d-block"><?= e(greska('lozinka2')) ?></div>
                    </div>
                </div>
                <button class="btn btn-sn w-100">Kreiraj nalog</button>
            </form>
        </div>
        <p class="text-center small mt-3">
            Već imate nalog? <a href="<?= url('prijava') ?>">Prijavite se</a>
        </p>
    </div>
</section>
