/* =================================================================
   assets/js/rute.js
   Ruta detalj stranica – sticky booking box
================================================================= */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {

		/* Sticky booking box – dodaj sjenu kad se počne scrollati */
		var box = document.getElementById('rBookingBox');
		if (!box) return;

		function onScroll() {
			if (window.scrollY > 80) {
				box.style.boxShadow = '0 8px 48px rgba(0,0,0,0.13)';
			} else {
				box.style.boxShadow = '0 4px 32px rgba(0,0,0,0.08)';
			}
		}

		window.addEventListener('scroll', onScroll, { passive: true });

	});
})();

/* =================================================================
   assets/js/istrazi.js
   Istraži rutu – interaktivna mapa sa modalnim prozorom
================================================================= */
(function () {
	'use strict';

	/* ---- Podaci za svaku tačku ---- */
	var tacke = {
		strbacki: {
			naslov: '📍 Štrbački Buk',
			tekst: 'Veličanstveni vodopad visok 23,5 metara – početna tačka rafting avanture. Čist adrenalin od prvog metra!',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/stbackibuk/strbackibuk1.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/stbackibuk/strbackibuk2.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/stbackibuk/strbackibuk3.jpeg' },
				{ tip: 'video', src: '../../assets/videos/rute/strbackibuk1.mp4' }
			]
		},
		prevrtac: {
			naslov: '📍 Prevrtač',
			tekst: 'Jedan od najuzbudljivijih brzaka na ruti – ovdje rijeka testira tvoje vještine i hrabrost!',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/prevrtac/prevrtac1.jpeg' }
				// { tip: 'video', src: '../../assets/videos/rute/prevrtac1.mp4' }
			]
		},
		zirin: {
			naslov: '📍 Zirin Slap',
			tekst: 'Lijepi mali slap usred kanjona – savršeno mjesto za kratku pauzu i fotografiju.',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/zirinslap/zirinslap1.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/zirinslap/zirinslap2.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/zirinslap/zirinslap3.jpeg' },
                { tip: 'slika', src: '../../assets/images/rute/r1/zirinslap/zirinslap4.jpeg' },
                { tip: 'slika', src: '../../assets/images/rute/r1/zirinslap/zirinslap5.jpeg' }
				// { tip: 'video', src: '../../assets/videos/rute/r1/zirin1.mp4' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/zirin2.mp4' }
			]
		},
		veliki: {
			naslov: '📍 Veliki Labirinti',
			tekst: 'Kompleksni sistem brzaka gdje rafting čamac prolazi kroz uzak kanjon – pravo iskustvo za cijeli tim.',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/labirinti/velikilabirint1.jpeg' }
				// { tip: 'slika', src: '../../assets/images/rute/r1/labirinti/veliki2.jpeg' },
				// { tip: 'slika', src: '../../assets/images/rute/r1/labirinti/veliki3.jpeg' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/veliki1.mp4' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/veliki2.mp4' }
			]
		},
		vrela: {
			naslov: '📍 Vrela (Pećina)',
			tekst: 'Misteriozna pećina sa izvorom kristalno čiste vode temperature 7°C – obavezna atrakcija na ruti.',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/pecina/pecina1.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/pecina/pecina2.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/pecina/pecina3.jpeg' },
                { tip: 'slika', src: '../../assets/images/rute/r1/pecina/pecina4.jpeg' }
                // { tip: 'slika', src: '../../assets/images/rute/r1/pecina/pecina5.jpeg' }
				// { tip: 'video', src: '../../assets/videos/rute/r1/vrela1.mp4' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/vrela2.mp4' }
			]
		},
		mali: {
			naslov: '📍 Mali Labirinti',
			tekst: 'Niz manjih brzaka koji nude zabavu i adrenalinski ubrzaj – savršeni za skokove sa stijena!',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/labirinti/velikilabirint2.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/labirinti/velikilabirint3.jpeg' }
				// { tip: 'slika', src: '../../assets/images/rute/r1/labirinti/mali3.jpeg' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/mali1.mp4' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/mali2.mp4' }
			]
		},
		loskunski: {
			naslov: '📍 Loskunski Most',
			tekst: 'Historijski most iznad Une – popularna tačka za skokove i nezaboravne fotografije.',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/loskun/loskun1.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/loskun/loskun2.jpeg' },
				{ tip: 'slika', src: '../../assets/images/rute/r1/loskun/loskun3.jpeg' },
                { tip: 'slika', src: '../../assets/images/rute/r1/loskun/loskun4.jpeg' },
				{ tip: 'video', src: '../../assets/videos/rute/loskunski1.mp4' },
				{ tip: 'video', src: '../../assets/videos/rute/loskunski2.mp4' },
                { tip: 'video', src: '../../assets/videos/rute/loskunski3.mp4' },
                { tip: 'video', src: '../../assets/videos/rute/loskunski4.mp4' },
                { tip: 'video', src: '../../assets/videos/rute/loskunski5.mp4' }
			]
		},
		troslap: {
			naslov: '📍 Troslap (Kraj)',
			tekst: 'Završna tačka rute R1 – spektakularan trostruki slap koji označava kraj nezaboravne avanture.',
			media: [
				{ tip: 'slika', src: '../../assets/images/rute/r1/troslap/troslap1.jpeg' }
				// { tip: 'slika', src: '../../assets/images/rute/r1/troslap/troslap2.jpeg' },
				// { tip: 'slika', src: '../../assets/images/rute/r1/troslap/troslap3.jpeg' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/troslap1.mp4' },
				// { tip: 'video', src: '../../assets/videos/rute/r1/troslap2.mp4' }
			]
		}
	};

	/* ---- Elementi ---- */
	var overlay   = document.getElementById('rModalOverlay');
	var slider    = document.getElementById('rModalSlider');
	var dotsWrap  = document.getElementById('rModalDots');
	var naslov    = document.getElementById('rModalNaslov');
	var tekst     = document.getElementById('rModalTekst');
	var btnZatvori = document.getElementById('rModalZatvori');
	var btnPrev   = document.getElementById('rModalPrev');
	var btnNext   = document.getElementById('rModalNext');

	if (!overlay) return;

	var trenutni = 0;
	var ukupno   = 0;

	/* ---- Otvori modal ---- */
	function otvoriModal(id) {
		var tacka = tacke[id];
		if (!tacka) return;

		naslov.textContent = tacka.naslov;
		tekst.textContent  = tacka.tekst;

		/* Generiši slides */
		slider.innerHTML  = '';
		dotsWrap.innerHTML = '';
		ukupno = tacka.media.length;
		trenutni = 0;

		tacka.media.forEach(function (m, i) {
			if (m.tip === 'slika') {
				var img = document.createElement('img');
				img.src = m.src;
				img.alt = tacka.naslov;
				img.className = 'r-modal-slide';
				slider.appendChild(img);
			} else {
				/* Video slide */
				var wrap = document.createElement('div');
				wrap.className = 'r-modal-slide-video';

				var vid = document.createElement('video');
				vid.src = m.src;
				vid.preload = 'metadata';
				vid.playsInline = true;
				vid.loop = true;
				wrap.appendChild(vid);

				/* Play overlay */
				var playDiv = document.createElement('div');
				playDiv.className = 'r-video-play';
				var playBtn = document.createElement('div');
				playBtn.className = 'r-video-play-btn';
				playBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
				playDiv.appendChild(playBtn);
				wrap.appendChild(playDiv);

				playDiv.addEventListener('click', function () {
					if (vid.paused) {
						vid.play();
						playDiv.style.display = 'none';
					}
				});

				vid.addEventListener('pause', function () {
					playDiv.style.display = 'flex';
				});

				slider.appendChild(wrap);
			}

			/* Dot */
			var dot = document.createElement('button');
			dot.className = 'r-modal-dot' + (i === 0 ? ' active' : '');
			dot.setAttribute('aria-label', 'Slide ' + (i + 1));
			dot.addEventListener('click', function () { ididiSlide(i); });
			dotsWrap.appendChild(dot);
		});

		azurirajSlider();
		overlay.classList.add('open');
		document.body.style.overflow = 'hidden';
	}

	/* ---- Zatvori modal ---- */
	function zatvoriModal() {
		overlay.classList.remove('open');
		document.body.style.overflow = '';

		/* Pauziraj sve videe */
		slider.querySelectorAll('video').forEach(function (v) { v.pause(); });
	}

	/* ---- Navigacija ---- */
	function ididiSlide(n) {
		/* Pauziraj trenutni video */
		var slides = slider.children;
		var curSlide = slides[trenutni];
		if (curSlide) {
			var vid = curSlide.querySelector('video');
			if (vid) {
				vid.pause();
				var playDiv = curSlide.querySelector('.r-video-play');
				if (playDiv) playDiv.style.display = 'flex';
			}
		}

		trenutni = (n + ukupno) % ukupno;
		azurirajSlider();
	}

	function azurirajSlider() {
		slider.style.transform = 'translateX(-' + (trenutni * 100) + '%)';

		/* Dots */
		var dots = dotsWrap.querySelectorAll('.r-modal-dot');
		dots.forEach(function (d, i) {
			d.classList.toggle('active', i === trenutni);
		});
	}

	/* ---- Event listeneri ---- */
	btnPrev.addEventListener('click', function () { ididiSlide(trenutni - 1); });
	btnNext.addEventListener('click', function () { ididiSlide(trenutni + 1); });
	btnZatvori.addEventListener('click', zatvoriModal);
	overlay.addEventListener('click', function (e) {
		if (e.target === overlay) zatvoriModal();
	});

	/* Swipe na mobilnom */
	var swipeStartX = 0;
	slider.addEventListener('touchstart', function (e) {
		swipeStartX = e.touches[0].clientX;
	}, { passive: true });
	slider.addEventListener('touchend', function (e) {
		var diff = swipeStartX - e.changedTouches[0].clientX;
		if (Math.abs(diff) > 40) {
			ididiSlide(diff > 0 ? trenutni + 1 : trenutni - 1);
		}
	}, { passive: true });

	/* Escape tipka */
	document.addEventListener('keydown', function (e) {
		if (!overlay.classList.contains('open')) return;
		if (e.key === 'Escape') zatvoriModal();
		if (e.key === 'ArrowLeft')  ididiSlide(trenutni - 1);
		if (e.key === 'ArrowRight') ididiSlide(trenutni + 1);
	});

	/* ---- Tačke na mapi ---- */
	document.querySelectorAll('.r-tacka').forEach(function (btn) {
		btn.addEventListener('click', function () {
			otvoriModal(btn.dataset.id);
		});
	});

})();

	/* ---- Lista tačaka ispod mape ---- */
	document.querySelectorAll('.r-lista-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			otvoriModal(btn.dataset.id);
		});
	});