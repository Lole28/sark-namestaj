<section class="sn-section">
    <div class="container" style="max-width: 460px;">
        <h1 class="sn-serif mb-4 text-center">Prijava</h1>
        <div class="sn-panel">
            <form method="post" action="<?= url('prijava') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Korisničko ime ili e-mail</label>
                    <input name="korisnicko_ime" class="form-control" required autofocus
                           value="<?= e(stare('korisnicko_ime')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Lozinka</label>
                    <input type="password" name="lozinka" class="form-control" required>
                </div>
                <button class="btn btn-sn w-100">Prijavi se</button>
            </form>
        </div>
        <p class="text-center small mt-3">
            Nemate nalog? <a href="<?= url('registracija') ?>">Registrujte se</a>
        </p>
    </div>
</section>
