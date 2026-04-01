(function($) {
	'use strict';

	// ── Mobile navigation toggle ──────────────────────────────
	var $toggle = $('.nav-toggle');
	var $nav    = $('.main-nav');

	$toggle.on('click', function() {
		var expanded = $(this).attr('aria-expanded') === 'true';
		$(this).attr('aria-expanded', !expanded);
		$nav.toggleClass('is-open');
	});

	// Chiudi menu su click fuori
	$(document).on('click', function(e) {
		if (!$(e.target).closest('#masthead').length) {
			$nav.removeClass('is-open');
			$toggle.attr('aria-expanded', 'false');
		}
	});

})(jQuery);
