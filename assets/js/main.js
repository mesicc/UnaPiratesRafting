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