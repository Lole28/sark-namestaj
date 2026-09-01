<?php
/**
 * Logo firme — rasterski grb iz assets/img/logo.png.
 * Da zameniš logo: prebaci svoj PNG preko te datoteke (po mogućstvu providna pozadina).
 *
 * $klasa     dodatne CSS klase za <img>
 * $wordmark  true = pored grba ispiši i tekstualni logotip
 */
$klasa    = $klasa ?? '';
$wordmark = $wordmark ?? false;
?>
<img src="<?= asset('img/logo.png') ?>" alt="Šark nameštaj po meri" class="sn-logo <?= e($klasa) ?>">
<?php if ($wordmark): ?>
    <span class="nav__wordmark">Šark<span>Nameštaj po meri</span></span>
<?php endif; ?>
