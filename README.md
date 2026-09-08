# Amazon Affiliate WordPress Toolkit

A small, copy-paste-friendly WordPress toolkit for Amazon affiliate websites.

This repository contains three practical snippets:

1. **Amazon link attributes**  
   Automatically adds `rel="sponsored nofollow noopener"` and `target="_blank"` to Amazon links in post content.

2. **Automatic affiliate disclosure**  
   Adds a disclosure notice automatically to single blog posts.

3. **Affiliate click tracking**  
   Sends Amazon affiliate-link clicks to Google Analytics 4 through `gtag()`.

It also includes the Elementor conversion prompt used in the video.

---

## Repository structure

```text
amazon-affiliate-wordpress-toolkit/
├── README.md
├── LICENSE
├── .gitignore
├── prompts/
│   └── elementor-conversion-prompt.txt
└── includes/
    ├── amazon-link-attributes.php
    ├── affiliate-disclosure.php
    └── affiliate-click-tracking.php
```

## Installation

### Option 1: Add the snippets to a child theme

Copy the contents of the three PHP files from `includes/` into your child theme's `functions.php`.

**Recommended:** keep the snippets separated in your project while developing, then combine them only if you have a reason to.

### Option 2: Use a snippets plugin

Create three separate snippets and paste each file's code into the corresponding snippet.

Make sure PHP opening tags are handled correctly by the snippets plugin. If the plugin expects code without `<?php`, remove that opening tag.

### Option 3: Convert this into a custom plugin

For a production site, a small custom plugin is usually cleaner than putting business logic into `functions.php`.

The snippets in this repository are intentionally kept simple so they can be copied into a theme, snippets plugin, or custom plugin.

---

# 1. Amazon link attributes

File:

```text
includes/amazon-link-attributes.php
```

The filter scans post content for links containing an Amazon domain and automatically adds:

```html
rel="sponsored nofollow noopener"
target="_blank"
```

This helps avoid manually editing every affiliate link.

### Important behavior

The code does **not** overwrite an existing `rel` or `target` attribute.

If a link already has `rel`, the snippet leaves it unchanged.

If a link already has `target`, the snippet leaves it unchanged.

### Code

```php
<?php

add_filter( 'the_content', 'ah_amazon_link_attrs', 20 );

function ah_amazon_link_attrs( $content ) {

	if ( is_admin() || empty( $content ) ) {
		return $content;
	}

	return preg_replace_callback(
		'#<a\s([^>]*href=["\'][^"\']*amazon\.[^"\']*["\'][^>]*)>#i',
		static function ( $matches ) {

			$attrs = $matches[1];

			if ( stripos( $attrs, 'rel=' ) === false ) {
				$attrs .= ' rel="sponsored nofollow noopener"';
			}

			if ( stripos( $attrs, 'target=' ) === false ) {
				$attrs .= ' target="_blank"';
			}

			return '<a ' . $attrs . '>';
		},
		$content
	);
}
```

### Example

Before:

```html
<a href="https://www.amazon.com/dp/EXAMPLE">Buy on Amazon</a>
```

After:

```html
<a href="https://www.amazon.com/dp/EXAMPLE" rel="sponsored nofollow noopener" target="_blank">
	Buy on Amazon
</a>
```

---

# 2. Automatic affiliate disclosure

File:

```text
includes/affiliate-disclosure.php
```

This automatically places a disclosure at the beginning of every single WordPress post.

### Code

```php
<?php

add_filter( 'the_content', 'ah_affiliate_disclosure', 5 );

function ah_affiliate_disclosure( $content ) {

	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$notice = '<p class="ah-disclosure"><em>
		This post contains affiliate links. If you purchase through a link,
		I may earn a small commission at no additional cost to you.
	</em></p>';

	return $notice . $content;
}
```

### Bengali version

If your website is Bengali, replace the `$notice` value with:

```php
$notice = '<p class="ah-disclosure"><em>
	এই পোস্টে অ্যাফিলিয়েট লিংক আছে। আপনি লিংক থেকে কিনলে আমি একটি ছোট কমিশন পাই,
	আপনার বাড়তি কোনো খরচ হয় না।
</em></p>';
```

### Optional styling

Add this CSS to your theme/customizer:

```css
.ah-disclosure {
	margin-bottom: 24px;
	padding: 12px 16px;
	font-size: 14px;
	line-height: 1.6;
}
```

---

# 3. Affiliate click tracking

File:

```text
includes/affiliate-click-tracking.php
```

This listens for clicks on Amazon links and sends an `affiliate_click` event to Google Analytics 4 using `gtag()`.

### Code

```php
<?php

add_action( 'wp_footer', 'ah_amazon_affiliate_click_tracking' );

function ah_amazon_affiliate_click_tracking() {
	?>
	<script>
	document.addEventListener('click', function (event) {

		const link = event.target.closest('a[href*="amazon."]');

		if ( ! link || typeof gtag !== 'function' ) {
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
```

## Google Analytics requirement

This snippet assumes Google Analytics 4 is already installed and exposes:

```js
gtag()
```

If `gtag()` is not available, the click-tracking code simply does nothing.

You can verify the event in GA4 using DebugView or the Events section.

---

# Recommended production improvement

The basic version matches the video and is intentionally easy to understand.

For a larger affiliate site, consider improving the implementation by:

- detecting Amazon domains more strictly
- supporting Amazon regional domains
- avoiding duplicate tracking when the same code is loaded twice
- using a dedicated JavaScript file instead of inline JavaScript
- loading tracking only where needed
- adding an affiliate-link CSS class
- adding event parameters such as post ID, post title, and link position
- using a proper WordPress plugin instead of `functions.php`

For example, instead of relying only on:

```js
a[href*="amazon."]
```

you can add your own class:

```html
<a class="ah-affiliate-link" href="...">
```

and track:

```js
a.ah-affiliate-link
```

That gives you much more control.

---

# Elementor Website Conversion Prompt

The original workflow prompt is stored here:

```text
prompts/elementor-conversion-prompt.txt
```

The prompt is designed to instruct an automation/browser agent to:

- convert an existing design into WordPress
- use Elementor
- use native Elementor elements instead of HTML widgets
- use Containers instead of Inner Sections
- avoid Atomic Editor elements
- use Novamira as a reference for Elementor elements
- create and save Header/Footer templates
- use UAE for Header/Footer
- avoid Elementor Pro widgets for Header/Footer
- save Header/Footer templates to the UAE library
- use Contact Form on the contact page
- set the homepage
- create a menu
- connect/push the finished site to the provided domain

Before using the prompt, replace:

```text
[website link]
```

with the actual website URL.

---

# Testing checklist

After installing the snippets, test the following.

## Amazon links

- [ ] Amazon link receives `rel="sponsored nofollow noopener"`
- [ ] Amazon link receives `target="_blank"`
- [ ] Existing `rel` is not overwritten
- [ ] Existing `target` is not overwritten
- [ ] Non-Amazon links remain unchanged

## Disclosure

- [ ] Disclosure appears on single blog posts
- [ ] Disclosure does not appear in wp-admin
- [ ] Disclosure does not appear on pages
- [ ] Disclosure does not duplicate inside loops

## Analytics

- [ ] GA4 is installed
- [ ] `gtag()` exists on the page
- [ ] Clicking an Amazon link creates `affiliate_click`
- [ ] `link_url` is captured
- [ ] `link_text` is captured
- [ ] `page_path` is captured

---

# Notes

These snippets modify rendered WordPress post content through `the_content`. They do not modify the original post content stored in the database.

Always test the code on a staging site before deploying it to a production affiliate site.

The Amazon affiliate program has its own operating requirements. Make sure your site's disclosures, link implementation, analytics, and content comply with the applicable Amazon Associates program policies and your local legal requirements.

## License

MIT. See `LICENSE`.
