/* Šark nameštaj po meri — front-end interakcije (Ajax, DOM izmene) */
(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const BASE = document.querySelector('meta[name="base-url"]')?.content || '/';
    const api = (p) => BASE + 'api/' + p.replace(/^\//, '');

    const escapeHtml = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
    const dinar = (n) => new Intl.NumberFormat('sr-RS', { maximumFractionDigits: 0 }).format(n) + ' RSD';

    /* --- Toast poruke ------------------------------------------------ */
    function toast(poruka, tip) {
        let box = document.getElementById('sn-toasts');
        if (!box) {
            box = document.createElement('div');
            box.id = 'sn-toasts';
            box.className = 'position-fixed top-0 end-0 p-3';
            box.style.zIndex = '1090';
            document.body.appendChild(box);
        }
        const el = document.createElement('div');
        el.className = 'alert alert-' + (tip === 'greska' ? 'danger' : 'success') + ' shadow-sm';
        el.setAttribute('role', 'alert');
        el.textContent = poruka;
        box.appendChild(el);
        setTimeout(() => { el.classList.add('fade'); el.style.opacity = '0'; }, 4000);
        setTimeout(() => el.remove(), 4600);
    }

    /* --- 1. Filter galerije radova (Ajax) -------------------------- */
    const filter = document.getElementById('sn-filter');
    const mreza = document.getElementById('sn-radovi-mreza');

    if (filter && mreza) {
        filter.addEventListener('click', async (e) => {
            const dugme = e.target.closest('button[data-slug]');
            if (!dugme) return;

            filter.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
            dugme.classList.add('active');

            const slug = dugme.dataset.slug;
            mreza.style.opacity = '.4';

            try {
                const url = api('radovi') + (slug ? '?kategorija=' + encodeURIComponent(slug) : '');
                const r = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await r.json();
                nacrtajRadove(data.radovi || []);
                history.replaceState(null, '', slug ? '?kategorija=' + slug : location.pathname);
            } catch (err) {
                toast('Greška pri učitavanju radova.', 'greska');
            } finally {
                mreza.style.opacity = '1';
            }
        });
    }

    function nacrtajRadove(radovi) {
        if (!radovi.length) {
            mreza.innerHTML = '<div class="col-12"><p class="text-muted-2 py-4">Nema radova u ovoj kategoriji.</p></div>';
            return;
        }
        mreza.innerHTML = radovi.map((r) => `
            <div class="col-sm-6 col-lg-4">
              <a class="sn-card d-block text-reset" href="${escapeHtml(r.url)}">
                <img class="sn-thumb" src="${escapeHtml(r.slika)}" alt="${escapeHtml(r.naziv)}" loading="lazy">
                <div class="card-body">
                  <span class="sn-badge">${escapeHtml(r.kategorija)}</span>
                  <h3 class="h5 mt-2 mb-1">${escapeHtml(r.naziv)}</h3>
                  <p class="small text-muted-2 mb-2">${escapeHtml(r.opis_kratak)}</p>
                  ${r.cena_od ? `<span class="fw-bold">od ${dinar(r.cena_od)}</span>` : ''}
                </div>
              </a>
            </div>`).join('');
    }

    /* --- 2. Ajax slanje formi (upit / recenzija) ------------------ */
    document.querySelectorAll('form[data-ajax]').forEach((forma) => {
        forma.addEventListener('submit', async (e) => {
            e.preventDefault();
            const tip = forma.dataset.ajax;             // 'upit' | 'recenzija'
            const dugme = forma.querySelector('[type="submit"]');
            const staroSlovo = dugme ? dugme.textContent : '';
            ocistiGreske(forma);

            if (dugme) { dugme.disabled = true; dugme.textContent = 'Šaljem…'; }

            try {
                const r = await fetch(api(tip === 'upit' ? 'upiti' : 'recenzije'), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF },
                    body: new FormData(forma)
                });
                const data = await r.json();

                if (r.ok && data.ok) {
                    forma.reset();
                    const box = forma.querySelector('[data-poruka]') || forma;
                    if (forma.querySelector('[data-poruka]')) {
                        box.innerHTML = '<div class="alert alert-success mb-0">' + escapeHtml(data.poruka) + '</div>';
                    } else {
                        toast(data.poruka, 'uspeh');
                    }
                } else if (data.greske) {
                    prikaziGreske(forma, data.greske);
                } else {
                    toast(data.greska || 'Došlo je do greške.', 'greska');
                }
            } catch (err) {
                toast('Nije moguće poslati formu. Pokušajte ponovo.', 'greska');
            } finally {
                if (dugme) { dugme.disabled = false; dugme.textContent = staroSlovo; }
            }
        });
    });

    function ocistiGreske(forma) {
        forma.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        forma.querySelectorAll('.invalid-feedback.js').forEach((el) => el.remove());
    }
    function prikaziGreske(forma, greske) {
        Object.entries(greske).forEach(([polje, poruka]) => {
            const input = forma.querySelector('[name="' + polje + '"]');
            if (!input) return;
            input.classList.add('is-invalid');
            const fb = document.createElement('div');
            fb.className = 'invalid-feedback js d-block';
            fb.textContent = poruka;
            input.insertAdjacentElement('afterend', fb);
        });
    }

    /* --- 3. Galerija na strani rada ------------------------------- */
    const glavna = document.getElementById('sn-slika-glavna');
    if (glavna) {
        document.querySelectorAll('.sn-thumbs img').forEach((t) => {
            t.addEventListener('click', () => {
                glavna.src = t.dataset.pun || t.src;
                glavna.alt = t.alt;
                document.querySelectorAll('.sn-thumbs img').forEach((x) => x.classList.remove('active'));
                t.classList.add('active');
            });
        });
    }

    /* --- 4. Prikaz cene u evrima (web servis /api/kurs) ---------- */
    const cene = document.querySelectorAll('[data-rsd]');
    if (cene.length) {
        fetch(api('kurs')).then((r) => r.json()).then((k) => {
            if (!k.ok || !k.kurs) return;
            cene.forEach((el) => {
                const rsd = parseFloat(el.dataset.rsd);
                if (!rsd) return;
                const eur = Math.round(rsd / k.kurs);
                el.insertAdjacentHTML('beforeend',
                    ` <span class="text-muted-2 small">(~${eur} €, kurs ${k.kurs})</span>`);
            });
        }).catch(() => {});
    }

    /* --- 5. Admin: promena statusa upita (Ajax PATCH) ----------- */
    document.querySelectorAll('select[data-upit-status]').forEach((sel) => {
        sel.addEventListener('change', async () => {
            const id = sel.dataset.upitStatus;
            const staro = sel.dataset.trenutno;
            sel.disabled = true;
            try {
                const r = await fetch(api('upiti/' + id), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': CSRF
                    },
                    body: JSON.stringify({ status: sel.value })
                });
                const data = await r.json();
                if (r.ok && data.ok) {
                    sel.dataset.trenutno = data.status;
                    const oznaka = document.querySelector('[data-status-oznaka="' + id + '"]');
                    if (oznaka) {
                        oznaka.textContent = data.status_naziv;
                        oznaka.className = 'sn-status ' + data.status;
                    }
                    toast('Status je promenjen u: ' + data.status_naziv, 'uspeh');
                } else {
                    sel.value = staro;
                    toast(data.greska || 'Promena nije uspela.', 'greska');
                }
            } catch (err) {
                sel.value = staro;
                toast('Greška u komunikaciji sa serverom.', 'greska');
            } finally {
                sel.disabled = false;
            }
        });
    });

    /* --- 6. Admin: upload slike uz rad (web servis + preview) --- */
    const uploadForma = document.getElementById('sn-upload-forma');
    if (uploadForma) {
        const input = uploadForma.querySelector('input[type="file"]');
        const pregled = document.getElementById('sn-upload-pregled');
        const lista = document.getElementById('sn-slike-lista');

        input.addEventListener('change', () => {
            pregled.innerHTML = '';
            const f = input.files[0];
            if (f) {
                const img = document.createElement('img');
                img.style.cssText = 'max-width:160px;border-radius:.4rem;margin-top:.5rem';
                img.src = URL.createObjectURL(f);
                pregled.appendChild(img);
            }
        });

        uploadForma.addEventListener('submit', async (e) => {
            e.preventDefault();
            const dugme = uploadForma.querySelector('[type="submit"]');
            dugme.disabled = true; dugme.textContent = 'Otpremam…';
            try {
                const r = await fetch(api('slike'), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF },
                    body: new FormData(uploadForma)
                });
                const data = await r.json();
                if (r.ok && data.ok) {
                    dodajSliku(lista, data.slika);
                    uploadForma.reset();
                    pregled.innerHTML = '';
                    toast('Slika je dodata.', 'uspeh');
                } else {
                    toast(data.greska || 'Otpremanje nije uspelo.', 'greska');
                }
            } catch (err) {
                toast('Greška pri otpremanju.', 'greska');
            } finally {
                dugme.disabled = false; dugme.textContent = 'Otpremi sliku';
            }
        });

        if (lista) {
            lista.addEventListener('click', async (e) => {
                const b = e.target.closest('button[data-obrisi-sliku]');
                if (!b) return;
                if (!confirm('Obrisati ovu sliku?')) return;
                const id = b.dataset.obrisiSliku;
                const r = await fetch(api('slike/' + id + '/brisanje'), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF }
                });
                const data = await r.json();
                if (data.ok) { b.closest('[data-slika]')?.remove(); toast('Slika je obrisana.', 'uspeh'); }
            });
        }
    }

    function dodajSliku(lista, s) {
        if (!lista) return;
        const div = document.createElement('div');
        div.className = 'col-4 col-md-3';
        div.dataset.slika = s.id;
        div.innerHTML = `
            <div class="position-relative">
              <img src="${escapeHtml(s.url)}" alt="${escapeHtml(s.alt)}" class="img-fluid rounded">
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                      data-obrisi-sliku="${s.id}" aria-label="Obriši">&times;</button>
            </div>`;
        lista.appendChild(div);
    }
})();
