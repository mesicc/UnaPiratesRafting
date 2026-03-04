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