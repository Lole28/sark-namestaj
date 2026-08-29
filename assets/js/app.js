/* Šark nameštaj po meri — interakcije (nav, reveal, marquee, lightbox, forme) */
(function () {
    'use strict';

    var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var BASE = (document.querySelector('meta[name="base-url"]') || {}).content || '/';
    var api = function (p) { return BASE + 'api/' + p.replace(/^\//, ''); };
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    var fmtRsd = function (n) {
        return new Intl.NumberFormat('sr-RS', { maximumFractionDigits: 0 }).format(n) + ' RSD';
    };

    /* ---------------------------------------------------------------- Toasts */
    function toast(msg, kind) {
        var box = document.getElementById('sn-toasts');
        if (!box) { box = document.createElement('div'); box.id = 'sn-toasts'; document.body.appendChild(box); }
        var el = document.createElement('div');
        el.className = 'toast' + (kind === 'err' ? ' toast--err' : '');
        el.setAttribute('role', 'status');
        el.textContent = msg;
        box.appendChild(el);
        setTimeout(function () { el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; }, 4200);
        setTimeout(function () { el.remove(); }, 4700);
    }

    /* ---------------------------------------------------------------- Flash poruke -> toast */
    try {
        var fl = document.getElementById('sn-flash');
        if (fl) {
            JSON.parse(fl.textContent).forEach(function (p) {
                toast(p.poruka, p.tip === 'greska' ? 'err' : 'ok');
            });
        }
    } catch (e) {}

    /* ---------------------------------------------------------------- Nav */
    var nav = document.querySelector('.nav');
    if (nav) {
        var over = nav.classList.contains('nav--over');
        var onScroll = function () {
            if (!over) { nav.classList.add('is-solid'); return; }
            nav.classList.toggle('is-solid', window.scrollY > window.innerHeight * 0.72);
        };
        onScroll();
        if (over) window.addEventListener('scroll', onScroll, { passive: true });

        var toggle = nav.querySelector('.nav__toggle');
        var panel = nav.querySelector('.nav__panel');
        if (toggle && panel) {
            var setMenu = function (open) {
                document.body.classList.toggle('menu-open', open);
                panel.classList.toggle('is-open', open);
                toggle.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.style.overflow = open ? 'hidden' : '';
            };
            toggle.addEventListener('click', function () {
                setMenu(!panel.classList.contains('is-open'));
            });
            panel.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () { setMenu(false); });
            });
        }
    }

    /* ---------------------------------------------------------------- Smooth anchor scroll */
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); }
        });
    });

    /* ---------------------------------------------------------------- Reveal on scroll */
    if (!reduce && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
        document.querySelectorAll('.reveal, .reveal-img, .reveal-line').forEach(function (el, i) {
            if (!el.style.getPropertyValue('--i')) el.style.setProperty('--i', (i % 6));
            io.observe(el);
        });
    } else {
        document.querySelectorAll('.reveal, .reveal-img, .reveal-line').forEach(function (el) { el.classList.add('is-in'); });
    }

    /* ---------------------------------------------------------------- Marquee duration by width */
    document.querySelectorAll('.marquee').forEach(function (m) {
        var track = m.querySelector('.marquee__track');
        if (!track) return;
        var w = track.scrollWidth / 2;
        track.style.setProperty('--dur', Math.max(30, Math.round(w / 95)) + 's');
    });

    /* ---------------------------------------------------------------- Lightbox */
    var galleryData = [];
    try {
        var raw = document.getElementById('sn-gallery-data');
        if (raw) galleryData = JSON.parse(raw.textContent);
    } catch (e) {}

    var lb = document.getElementById('sn-lightbox');
    var lbState = { list: [], idx: 0 };

    function openLightbox(list, idx) {
        if (!lb || !list.length) return;
        lbState.list = list; lbState.idx = idx;
        renderLightbox();
        lb.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        lb.querySelector('.lightbox__close').focus();
    }
    function closeLightbox() {
        if (!lb) return;
        lb.classList.remove('is-open');
        document.body.style.overflow = '';
    }
    function stepLightbox(d) {
        lbState.idx = (lbState.idx + d + lbState.list.length) % lbState.list.length;
        renderLightbox();
    }
    function renderLightbox() {
        var it = lbState.list[lbState.idx];
        if (!it) return;
        lb.querySelector('#lb-img').src = it.slika;
        lb.querySelector('#lb-img').alt = it.naziv || '';
        lb.querySelector('#lb-cat').textContent = it.kategorija || '';
        lb.querySelector('#lb-title').textContent = it.naziv || '';
        lb.querySelector('#lb-desc').textContent = it.opis || '';
        var kontakt = lb.querySelector('#lb-kontakt');
        if (kontakt) kontakt.href = BASE + 'kontakt' + (it.naziv ? '?tema=' + encodeURIComponent(it.naziv) : '');
        var vidi = lb.querySelector('#lb-vidi');
        if (vidi) vidi.href = it.url || (BASE + 'radovi');
    }

    if (lb) {
        lb.querySelector('.lightbox__close').addEventListener('click', closeLightbox);
        lb.addEventListener('click', function (e) { if (e.target === lb) closeLightbox(); });
        lb.querySelector('.lightbox__nav--prev').addEventListener('click', function () { stepLightbox(-1); });
        lb.querySelector('.lightbox__nav--next').addEventListener('click', function () { stepLightbox(1); });
        document.addEventListener('keydown', function (e) {
            if (!lb.classList.contains('is-open')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') stepLightbox(-1);
            if (e.key === 'ArrowRight') stepLightbox(1);
        });
    }

    // triggeri: sve sa data-lb-idx unutar kontejnera koji nosi celu listu
    document.querySelectorAll('[data-lb-scope]').forEach(function (scope) {
        var list = galleryData;
        if (scope.dataset.lbSource === 'detail') {
            try { list = JSON.parse(document.getElementById('sn-detail-data').textContent); } catch (e) { list = []; }
        }
        scope.querySelectorAll('[data-lb-idx]').forEach(function (el) {
            el.addEventListener('click', function () {
                openLightbox(list, parseInt(el.dataset.lbIdx, 10) || 0);
            });
        });
    });

    /* ---------------------------------------------------------------- Testimonials */
    var tst = document.getElementById('sn-tst');
    if (tst) {
        var slides = Array.prototype.slice.call(tst.querySelectorAll('.tst__slide'));
        var dotsWrap = tst.querySelector('.tst__dots');
        var cur = 0, timer;
        slides.forEach(function (s, i) {
            var b = document.createElement('button');
            b.type = 'button';
            b.setAttribute('aria-label', 'Recenzija ' + (i + 1));
            if (i === 0) b.className = 'active';
            b.addEventListener('click', function () { show(i); restart(); });
            dotsWrap.appendChild(b);
        });
        function show(i) {
            slides[cur].hidden = true;
            dotsWrap.children[cur].classList.remove('active');
            cur = i;
            slides[cur].hidden = false;
            dotsWrap.children[cur].classList.add('active');
        }
        function next() { show((cur + 1) % slides.length); }
        function restart() { clearInterval(timer); if (!reduce && slides.length > 1) timer = setInterval(next, 6500); }
        slides.forEach(function (s, i) { s.hidden = i !== 0; });
        restart();
    }

    /* ---------------------------------------------------------------- Filter (Ajax) */
    var filter = document.getElementById('sn-filter');
    var mreza = document.getElementById('sn-grid');
    if (filter && mreza) {
        filter.addEventListener('click', function (e) {
            var b = e.target.closest('button[data-slug]');
            if (!b) return;
            filter.querySelectorAll('button').forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            var slug = b.dataset.slug;
            mreza.style.opacity = '.35';
            fetch(api('radovi') + (slug ? '?kategorija=' + encodeURIComponent(slug) : ''), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    drawGrid(d.radovi || []);
                    history.replaceState(null, '', slug ? '?kategorija=' + slug : location.pathname);
                })
                .catch(function () { toast('Greška pri učitavanju.', 'err'); })
                .then(function () { mreza.style.opacity = '1'; });
        });
    }
    function drawGrid(rows) {
        if (!rows.length) { mreza.innerHTML = '<p class="text-stone">Nema radova u ovoj kategoriji.</p>'; return; }
        mreza.innerHTML = rows.map(function (r) {
            return '<a class="tile reveal is-in" href="' + esc(r.url) + '">' +
                '<div class="tile__img"><img src="' + esc(r.slika) + '" alt="' + esc(r.naziv) + '" loading="lazy"></div>' +
                '<div class="tile__cat">' + esc(r.kategorija) + '</div>' +
                '<div class="tile__name">' + esc(r.naziv) + '</div>' +
                (r.cena_od ? '<div class="tile__meta">od ' + fmtRsd(r.cena_od) + '</div>' : '') +
                '</a>';
        }).join('');
    }

    /* ---------------------------------------------------------------- Ajax forme (upit / recenzija) */
    document.querySelectorAll('form[data-ajax]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var kind = form.dataset.ajax;
            var btn = form.querySelector('[type="submit"]');
            var label = btn ? btn.textContent : '';
            clearErrors(form);
            if (btn) { btn.disabled = true; btn.textContent = 'Šaljem…'; }
            fetch(api(kind === 'upit' ? 'upiti' : 'recenzije'), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF },
                body: new FormData(form)
            }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
              .then(function (res) {
                  if (res.ok && res.d.ok) {
                      form.reset();
                      var box = form.querySelector('[data-msg]');
                      if (box) { box.className = 'notice notice--ok'; box.textContent = res.d.poruka; }
                      else toast(res.d.poruka);
                  } else if (res.d.greske) {
                      showErrors(form, res.d.greske);
                  } else {
                      toast(res.d.greska || 'Greška.', 'err');
                  }
              }).catch(function () { toast('Slanje nije uspelo.', 'err'); })
              .then(function () { if (btn) { btn.disabled = false; btn.textContent = label; } });
        });
    });
    function clearErrors(form) {
        form.querySelectorAll('.field.has-error').forEach(function (f) { f.classList.remove('has-error'); });
        form.querySelectorAll('.field__err.js').forEach(function (e) { e.remove(); });
    }
    function showErrors(form, errs) {
        Object.keys(errs).forEach(function (name) {
            var input = form.querySelector('[name="' + name + '"]');
            if (!input) return;
            var field = input.closest('.field') || input.parentNode;
            field.classList.add('has-error');
            var d = document.createElement('div');
            d.className = 'field__err js';
            d.textContent = errs[name];
            field.appendChild(d);
        });
    }

    /* ---------------------------------------------------------------- Admin: status upita (PATCH) */
    document.querySelectorAll('select[data-upit-status]').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var id = sel.dataset.upitStatus, prev = sel.dataset.trenutno;
            sel.disabled = true;
            fetch(api('upiti/' + id), {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF },
                body: JSON.stringify({ status: sel.value })
            }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
              .then(function (res) {
                  if (res.ok && res.d.ok) {
                      sel.dataset.trenutno = res.d.status;
                      var tag = document.querySelector('[data-status-tag="' + id + '"]');
                      if (tag) { tag.textContent = res.d.status_naziv; tag.className = 'pill ' + res.d.status; }
                      toast('Status: ' + res.d.status_naziv);
                  } else { sel.value = prev; toast(res.d.greska || 'Neuspešno.', 'err'); }
              }).catch(function () { sel.value = prev; toast('Greška.', 'err'); })
              .then(function () { sel.disabled = false; });
        });
    });

    /* ---------------------------------------------------------------- Admin: upload slike */
    var up = document.getElementById('sn-upload');
    if (up) {
        var input = up.querySelector('input[type="file"]');
        var prev = document.getElementById('sn-upload-prev');
        var list = document.getElementById('sn-img-list');
        input.addEventListener('change', function () {
            prev.innerHTML = '';
            if (input.files[0]) {
                var img = document.createElement('img');
                img.style.cssText = 'max-width:180px;margin-top:.6rem';
                img.src = URL.createObjectURL(input.files[0]);
                prev.appendChild(img);
            }
        });
        up.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = up.querySelector('[type="submit"]');
            btn.disabled = true; btn.textContent = 'Otpremam…';
            fetch(api('slike'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF }, body: new FormData(up) })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    if (res.ok && res.d.ok) {
                        addImg(list, res.d.slika); up.reset(); prev.innerHTML = ''; toast('Slika dodata.');
                    } else toast(res.d.greska || 'Neuspešno.', 'err');
                }).catch(function () { toast('Greška.', 'err'); })
                .then(function () { btn.disabled = false; btn.textContent = 'Otpremi sliku'; });
        });
        if (list) {
            list.addEventListener('click', function (e) {
                var b = e.target.closest('button[data-del-img]');
                if (!b || !confirm('Obrisati sliku?')) return;
                fetch(api('slike/' + b.dataset.delImg + '/brisanje'), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF } })
                    .then(function (r) { return r.json(); })
                    .then(function (d) { if (d.ok) { var w = b.closest('[data-img]'); if (w) w.remove(); toast('Obrisano.'); } });
            });
        }
    }
    function addImg(list, s) {
        if (!list) return;
        var d = document.createElement('div');
        d.dataset.img = s.id;
        d.style.cssText = 'position:relative';
        d.innerHTML = '<img src="' + esc(s.url) + '" alt="' + esc(s.alt) + '" style="width:100%;aspect-ratio:3/2;object-fit:cover">' +
            '<button type="button" class="btn btn-sm btn-danger" data-del-img="' + s.id + '" style="position:absolute;top:.3rem;right:.3rem">&times;</button>';
        list.appendChild(d);
    }
})();
