/**
 * TRROL Nieruchomości — skrypty interfejsu.
 */
(function () {
	'use strict';

	/* Menu mobilne */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('menu-glowne');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
			nav.classList.toggle('is-open', !open);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				toggle.setAttribute('aria-expanded', 'false');
				nav.classList.remove('is-open');
				toggle.focus();
			}
		});
	}

	/* Najczęstsze pytania */
	var questions = document.querySelectorAll('.faq-item__q');

	Array.prototype.forEach.call(questions, function (button) {
		button.addEventListener('click', function () {
			var expanded = button.getAttribute('aria-expanded') === 'true';
			var answer = document.getElementById(button.getAttribute('aria-controls'));

			button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			if (answer) {
				answer.classList.toggle('is-open', !expanded);
			}

			var icon = button.querySelector('.faq-item__icon');
			if (icon) {
				icon.textContent = expanded ? '+' : '−';
			}
		});
	});

	/* Po wysłaniu formularza przewiń do komunikatu */
	if (window.location.search.indexOf('trrol_status=') !== -1) {
		var message = document.querySelector('.form-msg');
		if (message) {
			message.setAttribute('tabindex', '-1');
			message.scrollIntoView({ behavior: 'smooth', block: 'center' });
			message.focus({ preventScroll: true });
		}
	}
})();
