// ============================================================
// main.js – Una Pirates
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // --- Navbar scroll: transparentna → bijela ---
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
    });

    // --- Hamburger / Mobile menu ---
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobileMenu');
    const menuOverlay = document.getElementById('menuOverlay');
    const closeMenu   = document.getElementById('closeMenu');

    function openMenu() {
        mobileMenu.classList.add('open');
        menuOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeMenuFn() {
        mobileMenu.classList.remove('open');
        menuOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', openMenu);
    closeMenu.addEventListener('click', closeMenuFn);
    menuOverlay.addEventListener('click', closeMenuFn);
    document.querySelectorAll('.mobile-nav-link, .btn-rezervisi-full')
        .forEach(link => link.addEventListener('click', closeMenuFn));

    // --- Language switcher ---
    setupLang('langBtn', 'langDropdown', 'currentLang');
    setupLang('langBtnMobile', 'langDropdownMobile', 'currentLangMobile');

    function setupLang(btnId, dropdownId, currentId) {
        const btn      = document.getElementById(btnId);
        const dropdown = document.getElementById(dropdownId);
        const current  = document.getElementById(currentId);
        if (!btn) return;

        btn.addEventListener('click', e => {
            e.stopPropagation();
            const isOpen = dropdown.classList.toggle('open');
            btn.classList.toggle('open', isOpen);
        });

        dropdown.querySelectorAll('.lang-option').forEach(option => {
            option.addEventListener('click', () => {
                dropdown.querySelectorAll('.lang-option').forEach(o => {
                    o.classList.remove('active');
                    const chk = o.querySelector('.fa-check');
                    if (chk) chk.remove();
                });
                option.classList.add('active');
                const check = document.createElement('i');
                check.className = 'fa-solid fa-check';
                option.appendChild(check);
                current.textContent = option.dataset.lang;
                dropdown.classList.remove('open');
                btn.classList.remove('open');
            });
        });

        document.addEventListener('click', e => {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
                btn.classList.remove('open');
            }
        });
    }

    // --- Active nav link na scroll ---
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            if (window.scrollY >= section.offsetTop - 100)
                current = section.getAttribute('id');
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`)
                link.classList.add('active');
        });
    });

});

// --- Quiz logika ---
(function () {
    const odgovori = {};

    const rezultati = {
        r1: {
            emoji: '🔥',
            tip: 'Adrenalin Junkie',
            opis: 'Živiš za uzbuđenje i izazove! Štrbački Buk ruta (R1) je savršena za tebe.',
            features: [
                'Start ispod spektakularnog 24.5m vodopada',
                'Snažni brzaci',
                'Skokovi sa stijena',
                'Netaknuta priroda NP Una'
            ],
            trajanje: 'Trajanje: oko 3 sata',
            socialProof: '95% gostiju koji vole avanturu bira ovu rutu.',
            btnLabel: '🚣 Rezerviši R1 Avanturu',
            btnHref: '#rezervacija'
        },
        r2: {
            emoji: '🌴',
            tip: 'Nature Relaxer',
            opis: 'Ti želiš uživati u rijeci, suncu i prirodi. Paradise ruta (R2 – Kostela) je idealna za tebe.',
            features: [
                '13km lagane vožnje',
                'Kristalno čistu toplu vodu',
                'Pješčane plaže',
                'Idealno kupanje'
            ],
            trajanje: 'Trajanje: 2–3 sata',
            socialProof: 'Ovo je najpopularnija ruta za parove i porodice.',
            btnLabel: '🚣 Rezerviši R2 Paradise',
            btnHref: '#rezervacija'
        },
        kayak: {
            emoji: '🚣',
            tip: 'Kayak Explorer',
            opis: 'Voliš slobodu i vlastiti tempo. Kayak Safari je tvoja idealna avantura na Uni.',
            features: [
                'Slobodan tempo istraživanja',
                'Skrivene plaže i uvale',
                'Kristalno čista voda',
                'Nezaboravni panoramski pogledi'
            ],
            trajanje: 'Trajanje: 2 sata',
            socialProof: 'Idealan izbor za one koji žele intimno iskustvo s prirodom.',
            btnLabel: '🚣 Rezerviši Kayak Safari',
            btnHref: '#rezervacija'
        }
    };

    function izracunajRezultat() {
        let score = { r1: 0, r2: 0, kayak: 0 };

        // Pitanje 1
        if (odgovori[1] === 'adrenalin') score.r1 += 2;
        if (odgovori[1] === 'relax')     score.r2 += 2;
        if (odgovori[1] === 'photo')     score.kayak += 2;

        // Pitanje 2
        if (odgovori[2] === 'maksimalno') score.r1 += 2;
        if (odgovori[2] === 'malo')       score.r2 += 2;
        if (odgovori[2] === 'srednje')    score.kayak += 2;

        // Pitanje 3
        if (odgovori[3] === 'avantura') score.r1 += 2;
        if (odgovori[3] === 'kupanje')  score.r2 += 2;
        if (odgovori[3] === 'priroda')  score.kayak += 2;

        return Object.entries(score).sort((a, b) => b[1] - a[1])[0][0];
    }

    function prikaziRezultat(kljuc) {
        const r = rezultati[kljuc];
        const stepRez = document.getElementById('stepRezultat');

        stepRez.innerHTML = `
            <div class="quiz-rezultat-header">
                <div class="quiz-rezultat-emoji-wrap">${r.emoji}</div>
                <h3 class="quiz-rezultat-tip">${r.tip}</h3>
                <p class="quiz-rezultat-opis">${r.opis}</p>
            </div>
            <div class="quiz-rezultat-body">
                <div class="quiz-features-box">
                    <p class="quiz-features-title">Na ovoj ruti te čeka:</p>
                    <ul class="quiz-features-list">
                        ${r.features.map(f => `<li>${f}</li>`).join('')}
                    </ul>
                    <p class="quiz-trajanje">${r.trajanje}</p>
                </div>
                <p class="quiz-social-proof">${r.socialProof}</p>
                <div class="quiz-rezultat-btns">
                    <a href="${r.btnHref}" class="btn-quiz-rezervisi">${r.btnLabel}</a>
                    <button class="btn-quiz-ponovi" id="ponoviKviz">↺ Ponovi kviz</button>
                </div>
            </div>
        `;

        prikaziStep('stepRezultat');

        document.getElementById('ponoviKviz').addEventListener('click', resetKviz);
    }

    function prikaziStep(id) {
        document.querySelectorAll('.quiz-step').forEach(s => s.classList.add('hidden'));
        document.getElementById(id).classList.remove('hidden');
    }

    function resetKviz() {
        odgovori[1] = odgovori[2] = odgovori[3] = null;

        // Ukloni sve selected klase sa svih opcija
        document.querySelectorAll('.quiz-opcija').forEach(b => {
            b.classList.remove('selected');
        });

        prikaziStep('step1');
    }

    // Klik na opciju
    document.querySelectorAll('.quiz-opcija').forEach(btn => {
        btn.addEventListener('click', function () {
            const step = parseInt(this.dataset.step);
            const value = this.dataset.value;

            // Visual feedback
            const parent = this.closest('.quiz-opcije');
            parent.querySelectorAll('.quiz-opcija').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');

            odgovori[step] = value;

            // Pređi na sljedeći step sa kratkim delay-om
            setTimeout(() => {
                if (step === 1) prikaziStep('step2');
                else if (step === 2) prikaziStep('step3');
                else if (step === 3) prikaziRezultat(izracunajRezultat());
            }, 300);
        });
    });
})();


// --- Galerija – Vidi više / Vidi manje ---
(function () {
    const btnVise   = document.getElementById('btnVidiVise');
    const btnManje  = document.getElementById('btnVidiManje');
    const skrivene  = document.querySelectorAll('.galerija-item--hidden');

    if (!btnVise) return;

    btnVise.addEventListener('click', () => {
        skrivene.forEach(el => el.style.display = 'block');
        btnVise.classList.add('hidden');
        btnManje.classList.remove('hidden');
        btnVise.setAttribute('aria-expanded', 'true');
    });

    btnManje.addEventListener('click', () => {
        skrivene.forEach(el => el.style.display = 'none');
        btnManje.classList.add('hidden');
        btnVise.classList.remove('hidden');
        btnVise.setAttribute('aria-expanded', 'false');

        // Scroll nazad na vrh galerije
        document.getElementById('galerija').scrollIntoView({ behavior: 'smooth' });
    });
})();

// --- Lightbox ---
(function () {
    const lightbox    = document.getElementById('lightbox');
    const lbImg       = document.getElementById('lightboxImg');
    const lbCounter   = document.getElementById('lightboxCounter');
    const lbClose     = document.getElementById('lightboxClose');
    const lbPrev      = document.getElementById('lightboxPrev');
    const lbNext      = document.getElementById('lightboxNext');
    const lbOverlay   = document.getElementById('lightboxOverlay');

    if (!lightbox) return;

    const items = document.querySelectorAll('.galerija-item');
    let trenutni = 0;

    function getSlike() {
        // Uzima sve slike koje su trenutno vidljive i skrivene (sve)
        return Array.from(document.querySelectorAll('.galerija-item img'));
    }

    function otvoriLightbox(index) {
        const slike = getSlike();
        trenutni = index;
        lbImg.src = slike[trenutni].src;
        lbImg.alt = slike[trenutni].alt;
        lbCounter.textContent = `${trenutni + 1} / ${slike.length}`;
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden'; // Blokira scroll
    }

    function zatvoriLightbox() {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = ''; // Vraća scroll
        lbImg.src = '';
    }

    function sljedeca() {
        const slike = getSlike();
        trenutni = (trenutni + 1) % slike.length;
        lbImg.src = slike[trenutni].src;
        lbImg.alt = slike[trenutni].alt;
        lbCounter.textContent = `${trenutni + 1} / ${slike.length}`;
    }

    function prethodna() {
        const slike = getSlike();
        trenutni = (trenutni - 1 + slike.length) % slike.length;
        lbImg.src = slike[trenutni].src;
        lbImg.alt = slike[trenutni].alt;
        lbCounter.textContent = `${trenutni + 1} / ${slike.length}`;
    }

    // Klik na sliku
    items.forEach((item, i) => {
        item.addEventListener('click', () => otvoriLightbox(i));
    });

    // Zatvaranje
    lbClose.addEventListener('click', zatvoriLightbox);
    lbOverlay.addEventListener('click', zatvoriLightbox);

    // Navigacija
    lbNext.addEventListener('click', sljedeca);
    lbPrev.addEventListener('click', prethodna);

    // Tipkovnica
    document.addEventListener('keydown', e => {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape')     zatvoriLightbox();
        if (e.key === 'ArrowRight') sljedeca();
        if (e.key === 'ArrowLeft')  prethodna();
    });

    // Swipe podrška za mobilne uređaje
    let touchStartX = 0;
    lightbox.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    lightbox.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? sljedeca() : prethodna();
        }
    }, { passive: true });
})();


// ============================================================
// RECENZIJE SLIDER – main.js dodatak
// Dodaj unutar DOMContentLoaded
// ============================================================

(function () {

    const slider   = document.getElementById('reviewsSlider');
    const btnPrev  = document.getElementById('reviewsPrev');
    const btnNext  = document.getElementById('reviewsNext');
    const dotsWrap = document.getElementById('reviewsDots');

    if (!slider) return;

    // Sve kartice u nizu
    const kartice = Array.from(slider.querySelectorAll('.review-card'));
    const ukupno  = kartice.length;
    let trenutni  = 0;

    // ---- Koliko kartica pokazati ovisno o širini ----
    function vidljivih() {
        if (window.innerWidth <= 600)  return 1;
        if (window.innerWidth <= 1024) return 2;
        return 3;
    }

    // ---- Generiraj dots dinamički ----
    function generisiDots() {
        dotsWrap.innerHTML = '';
        const n      = vidljivih();
        const stranicy = Math.ceil(ukupno / n);

        for (let i = 0; i < stranicy; i++) {
            const dot = document.createElement('button');
            dot.className   = 'reviews-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('role', 'tab');
            dot.setAttribute('aria-label', `Stranica ${i + 1}`);
            dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
            dot.dataset.index = i;

            dot.addEventListener('click', () => {
                trenutni = i * vidljivih();
                render();
            });

            dotsWrap.appendChild(dot);
        }
    }

    // ---- Render – prikaži n kartica od trenutnog indexa ----
    function render() {
        const n       = vidljivih();
        const maxStart = ukupno - n;
        if (trenutni > maxStart) trenutni = maxStart;
        if (trenutni < 0)        trenutni = 0;

        kartice.forEach((card, i) => {
            const vidljiva = i >= trenutni && i < trenutni + n;
            card.style.display = vidljiva ? 'flex' : 'none';
        });

        // Strelice
        btnPrev.disabled = trenutni === 0;
        btnNext.disabled = trenutni >= maxStart;

        // Dots
        const aktivniDot = Math.floor(trenutni / n);
        const dots = dotsWrap.querySelectorAll('.reviews-dot');
        dots.forEach((d, i) => {
            d.classList.toggle('active', i === aktivniDot);
            d.setAttribute('aria-selected', i === aktivniDot ? 'true' : 'false');
        });
    }

    // ---- Navigacija ----
    btnPrev.addEventListener('click', () => {
        if (trenutni > 0) {
            trenutni--;
            render();
        }
    });

    btnNext.addEventListener('click', () => {
        if (trenutni < ukupno - vidljivih()) {
            trenutni++;
            render();
        }
    });

    // ---- Swipe na mobilnim ----
    let touchStartX = 0;

    slider.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    slider.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) < 50) return;

        if (diff > 0 && trenutni < ukupno - vidljivih()) {
            trenutni++;
            render();
        } else if (diff < 0 && trenutni > 0) {
            trenutni--;
            render();
        }
    }, { passive: true });

    // ---- Resize ----
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            generisiDots();
            render();
        }, 150);
    });

    // ---- Read More / Read Less ----
    slider.addEventListener('click', e => {
        const btn = e.target.closest('.review-read-more');
        if (!btn) return;

        const tekst    = btn.previousElementSibling;
        const prosiren = tekst.classList.contains('review-tekst--prosiren');

        if (prosiren) {
            tekst.classList.remove('review-tekst--prosiren');
            tekst.classList.add('review-tekst--skracen');
            btn.textContent = 'Read more';
            btn.setAttribute('aria-expanded', 'false');
        } else {
            tekst.classList.remove('review-tekst--skracen');
            tekst.classList.add('review-tekst--prosiren');
            btn.textContent = 'Read less';
            btn.setAttribute('aria-expanded', 'true');
        }
    });

    // ---- Init ----
    generisiDots();
    render();

})();

// ============================================================
// VIDEO SEKCIJA
// ============================================================
(function () {
    const video         = document.getElementById('videoPlayer');
    const videoWrap     = document.getElementById('videoWrap');
    const overlay       = document.getElementById('videoOverlay');
    const playBtn       = document.getElementById('videoPlayBtn');
    const vcPlayPause   = document.getElementById('vcPlayPause');
    const vcPlayIcon    = document.getElementById('vcPlayIcon');
    const vcProgressFill = document.getElementById('vcProgressFill');
    const vcProgressWrap = document.getElementById('vcProgressWrap');
    const vcTime        = document.getElementById('vcTime');
    const vcMute        = document.getElementById('vcMute');
    const vcVolumeIcon  = document.getElementById('vcVolumeIcon');
    const vcFullscreen  = document.getElementById('vcFullscreen');

    // Lightbox
    const lightbox      = document.getElementById('videoLightbox');
    const lbOverlay     = document.getElementById('videoLightboxOverlay');
    const lbClose       = document.getElementById('videoLightboxClose');
    const lbVideo       = document.getElementById('videoLightboxPlayer');

    if (!video) return;

    // ---- Helpers ----
    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function updatePlayIcon(playing) {
        vcPlayIcon.className = playing ? 'fa-solid fa-pause' : 'fa-solid fa-play';
        playBtn.querySelector('i').className = playing ? 'fa-solid fa-pause' : 'fa-solid fa-play';
    }

    // ---- Play / Pause ----
    function togglePlay() {
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }

    video.addEventListener('play', () => {
        overlay.classList.add('hidden');
        videoWrap.classList.add('playing');
        updatePlayIcon(true);
    });

    video.addEventListener('pause', () => {
        overlay.classList.remove('hidden');
        videoWrap.classList.remove('playing');
        updatePlayIcon(false);
    });

    video.addEventListener('ended', () => {
        overlay.classList.remove('hidden');
        videoWrap.classList.remove('playing');
        updatePlayIcon(false);
        vcProgressFill.style.width = '0%';
    });

    // Klik na overlay (play btn na sredini)
    overlay.addEventListener('click', togglePlay);
    playBtn.addEventListener('click', e => { e.stopPropagation(); togglePlay(); });

    // Klik na controls play/pause
    vcPlayPause.addEventListener('click', e => { e.stopPropagation(); togglePlay(); });

    // ---- Progress ----
    video.addEventListener('timeupdate', () => {
        if (!video.duration) return;
        const pct = (video.currentTime / video.duration) * 100;
        vcProgressFill.style.width = pct + '%';
        vcTime.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
        vcProgressWrap.setAttribute('aria-valuenow', Math.round(pct));
    });

    vcProgressWrap.addEventListener('click', e => {
        e.stopPropagation();
        const rect = vcProgressWrap.getBoundingClientRect();
        const pct  = (e.clientX - rect.left) / rect.width;
        video.currentTime = pct * video.duration;
    });

    // ---- Mute ----
    vcMute.addEventListener('click', e => {
        e.stopPropagation();
        video.muted = !video.muted;
        vcVolumeIcon.className = video.muted
            ? 'fa-solid fa-volume-xmark'
            : 'fa-solid fa-volume-high';
    });

    // ---- Fullscreen (lightbox) ----
    function otvoriLightbox() {
        const trenutnoVrijeme = video.currentTime;
        const bjesePlay = !video.paused;

        video.pause();

        lbVideo.currentTime = trenutnoVrijeme;
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        if (bjesePlay) lbVideo.play();
    }

    function zatvoriLightbox() {
        const trenutnoVrijeme = lbVideo.currentTime;
        lbVideo.pause();

        video.currentTime = trenutnoVrijeme;
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    vcFullscreen.addEventListener('click', e => { e.stopPropagation(); otvoriLightbox(); });

    lbClose.addEventListener('click', zatvoriLightbox);
    lbOverlay.addEventListener('click', zatvoriLightbox);

    document.addEventListener('keydown', e => {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') zatvoriLightbox();
    });

})();


// ---- FAQ Accordion ----
(function () {
    const pitanja = document.querySelectorAll('.faq-pitanje');
    if (!pitanja.length) return;

    pitanja.forEach(btn => {
        btn.addEventListener('click', function () {
            const item     = this.closest('.faq-item');
            const odgovor  = document.getElementById(this.getAttribute('aria-controls'));
            const jeOtvoren = this.getAttribute('aria-expanded') === 'true';

            // Zatvori sve ostale
            pitanja.forEach(drugi => {
                if (drugi === this) return;
                drugi.setAttribute('aria-expanded', 'false');
                drugi.closest('.faq-item').classList.remove('open');
                const drugOdgovor = document.getElementById(drugi.getAttribute('aria-controls'));
                drugOdgovor.classList.remove('otvoren');
                drugOdgovor.hidden = false; // hidden ne koristimo za animaciju
            });

            // Toggle kliknuto
            if (jeOtvoren) {
                this.setAttribute('aria-expanded', 'false');
                item.classList.remove('open');
                odgovor.classList.remove('otvoren');
            } else {
                this.setAttribute('aria-expanded', 'true');
                item.classList.add('open');
                odgovor.classList.add('otvoren');
            }
        });
    });
})();