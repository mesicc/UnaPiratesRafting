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