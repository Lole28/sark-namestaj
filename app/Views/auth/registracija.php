<div class="page-top wrap" style="max-width:520px">
    <p class="eyebrow center" style="justify-content:center">Nalog</p>
    <h1 class="serif-l center" style="margin-bottom:2rem">Registracija</h1>
    <div class="form-card">
        <form method="post" action="<?= url('registracija') ?>" novalidate>
            <?= csrf_field() ?>
            <div class="field <?= ima_gresku('ime') ? 'has-error' : '' ?>">
                <label>Ime i prezime</label>
                <input name="ime" required value="<?= e(stare('ime')) ?>">
                <?php if (ima_gresku('ime')): ?><div class="field__err"><?= e(greska('ime')) ?></div><?php endif; ?>
            </div>
            <div class="field <?= ima_gresku('korisnicko_ime') ? 'has-error' : '' ?>">
                <label>Korisničko ime</label>
                <input name="korisnicko_ime" required value="<?= e(stare('korisnicko_ime')) ?>">
                <?php if (ima_gresku('korisnicko_ime')): ?><div class="field__err"><?= e(greska('korisnicko_ime')) ?></div><?php endif; ?>
            </div>
            <div class="field <?= ima_gresku('email') ? 'has-error' : '' ?>">
                <label>E-mail</label>
                <input type="email" name="email" required value="<?= e(stare('email')) ?>">
                <?php if (ima_gresku('email')): ?><div class="field__err"><?= e(greska('email')) ?></div><?php endif; ?>
            </div>
            <div class="field <?= ima_gresku('lozinka') ? 'has-error' : '' ?>">
                <label>Lozinka</label>
                <input type="password" name="lozinka" required>
                <?php if (ima_gresku('lozinka')): ?><div class="field__err"><?= e(greska('lozinka')) ?></div><?php endif; ?>
            </div>
            <div class="field <?= ima_gresku('lozinka2') ? 'has-error' : '' ?>">
                <label>Ponovi lozinku</label>
                <input type="password" name="lozinka2" required>
                <?php if (ima_gresku('lozinka2')): ?><div class="field__err"><?= e(greska('lozinka2')) ?></div><?php endif; ?>
            </div>
            <button class="mbtn mbtn--solid" style="width:100%;justify-content:center">
                <span class="mbtn__track"><span>Kreiraj nalog</span><span>Kreiraj nalog</span></span>
            </button>
        </form>
    </div>
    <p class="center" style="margin-top:1.5rem;font-size:.9rem">
        Već imate nalog? <a href="<?= url('prijava') ?>" style="text-decoration:underline">Prijavite se</a>
    </p>
</div>
