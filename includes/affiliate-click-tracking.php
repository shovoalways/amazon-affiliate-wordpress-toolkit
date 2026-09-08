<?php

/**
 * Track Amazon affiliate link clicks in Google Analytics 4.
 *
 * Requires GA4 gtag() to already be loaded on the page.
 */

add_action( 'wp_footer', 'ah_amazon_affiliate_click_tracking' );

function ah_amazon_affiliate_click_tracking() {
	?>
	<script>
	document.addEventListener('click', function (event) {

		const link = event.target.closest('a[href*="amazon."]');

		if (!link || typeof gtag !== 'function') {
			return;
		}

		gtag('event', 'affiliate_click', {
			link_url: link.href,
			link_text: link.innerText.trim().slice(0, 100),
			page_path: window.location.pathname
		});

	});
	</script>
	<?php
}
