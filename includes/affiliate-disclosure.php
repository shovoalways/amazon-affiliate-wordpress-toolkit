<?php

/**
 * Add an affiliate disclosure to single blog posts.
 */

add_filter( 'the_content', 'ah_affiliate_disclosure', 5 );

function ah_affiliate_disclosure( $content ) {

	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$notice = '<p class="ah-disclosure"><em>
	এই পোস্টে অ্যাফিলিয়েট লিংক আছে। আপনি লিংক থেকে কিনলে আমি একটি ছোট কমিশন পাই,
	আপনার বাড়তি কোনো খরচ হয় না।
	</em></p>';

	return $notice . $content;
}
