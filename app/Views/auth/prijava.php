<div class="page-top wrap" style="max-width:480px">
    <p class="eyebrow center" style="justify-content:center">Nalog</p>
    <h1 class="serif-l center" style="margin-bottom:2rem">Prijava</h1>
    <div class="form-card">
        <form method="post" action="<?= url('prijava') ?>" novalidate>
            <?= csrf_field() ?>
            <div class="field">
                <label>Korisničko ime ili e-mail</label>
                <input name="korisnicko_ime" required autofocus value="<?= e(stare('korisnicko_ime')) ?>">
            </div>
            <div class="field">
                <label>Lozinka</label>
                <input type="password" name="lozinka" required>
            </div>
            <button class="mbtn mbtn--solid" style="width:100%;justify-content:center">
                <span class="mbtn__track"><span>Prijavi se</span><span>Prijavi se</span></span>
            </button>
        </form>
    </div>
    <p class="center" style="margin-top:1.5rem;font-size:.9rem">
        Nemate nalog? <a href="<?= url('registracija') ?>" style="text-decoration:underline">Registrujte se</a>
    </p>
</div>
