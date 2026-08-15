<?php
/**
 * Affiliate click tracking — data layer.
 *
 * Pushes an `affiliate_click` event to the GTM data layer whenever a visitor
 * clicks a cloaked /go/ link. GTM picks it up and forwards to GA4.
 *
 * Full specification, including the GA4 property config and GTM container
 * structure, lives in tracking.md at the repo root.
 *
 * Design decision: this listens for clicks on the DOCUMENT rather than binding
 * to each link. That means links inserted later — by a block, a lazy-loaded
 * section, or a future component — are tracked automatically, with no
 * requirement for anyone to remember to add tracking attributes.
 *
 * @package PrimeTours
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Tag Manager container.
 *
 * Overridable per environment with a constant in wp-config.php, e.g.
 *   define( 'PRIMETOURS_GTM_ID', 'GTM-XXXXXXX' );
 */
if ( ! defined( 'PRIMETOURS_GTM_ID' ) ) {
	define( 'PRIMETOURS_GTM_ID', 'GTM-WHSX6CTM' );
}

/**
 * Should GTM load on this request?
 *
 * Production only by default. Local and staging must NOT send hits to the
 * production GA4 property — a handful of developer sessions is enough to
 * distort a low-traffic launch period, and there is no way to remove them
 * from GA4 afterwards.
 *
 * To test tracking on staging deliberately, set in that environment's
 * wp-config.php:
 *   define( 'PRIMETOURS_GTM_FORCE', true );
 */
function primetours_should_load_gtm(): bool {
	if ( defined( 'PRIMETOURS_GTM_FORCE' ) && PRIMETOURS_GTM_FORCE ) {
		return true;
	}

	if ( ! PRIMETOURS_GTM_ID || is_admin() ) {
		return false;
	}

	// Never track logged-in editors — that is our own traffic.
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return function_exists( 'wp_get_environment_type' )
		? 'production' === wp_get_environment_type()
		: false;
}

/**
 * Consent Mode v2 defaults, then the GTM container — in that order.
 *
 * ORDER IS THE WHOLE POINT. Consent defaults must be in the page BEFORE the
 * container loads, or tags can fire once before consent is known. Emitting
 * both from the same function at the same hook priority is what guarantees
 * it, rather than hoping the consent plugin happens to enqueue first.
 *
 * Complianz then issues gtag('consent','update',...) when the visitor
 * chooses, which unblocks the GA4 tags.
 */
function primetours_gtm_head(): void {
	if ( ! primetours_should_load_gtm() ) {
		return;
	}
	?>
	<script id="pt-consent-default">
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('consent', 'default', {
		'ad_storage': 'denied',
		'ad_user_data': 'denied',
		'ad_personalization': 'denied',
		'analytics_storage': 'denied',
		'functionality_storage': 'granted',
		'security_storage': 'granted',
		'wait_for_update': 500
	});
	</script>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo esc_js( PRIMETOURS_GTM_ID ); ?>');</script>
	<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'primetours_gtm_head', 1 );

/**
 * GTM noscript fallback, immediately after <body>.
 */
function primetours_gtm_body(): void {
	if ( ! primetours_should_load_gtm() ) {
		return;
	}
	printf(
		'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
		esc_attr( PRIMETOURS_GTM_ID )
	);
}
add_action( 'wp_body_open', 'primetours_gtm_body', 1 );

/**
 * The closed list of valid link positions.
 *
 * Keep this closed. An open-ended list produces reports nobody can read,
 * because the same placement ends up recorded under three different names.
 * Adding a value here is a deliberate act — see tracking.md Part 2.
 */
const PRIMETOURS_LINK_POSITIONS = array(
	'booking_module',
	'booking_secondary',
	'inline',
	'quick_answer',
	'verdict',
	'comparison_table',
	'homepage_card',
);

/**
 * Map a /go/ slug to its affiliate destination.
 *
 * Kept server-side and in code rather than parsed from the URL, because the
 * destination is not visible in a cloaked link — that is the whole point of
 * cloaking. Update this when a link's destination is switched.
 *
 * Source of truth: affiliates.md link register.
 *
 * @return array<string, string> slug => destination
 */
function primetours_go_destinations(): array {
	return array(
		'cape-peninsula-full-day' => 'viator',
		'cape-peninsula-budget'   => 'viator',
		// Add as each /go/ link is created:
		// 'table-mountain-cable-car' => '',
		// 'robben-island'            => '',
		// 'cape-winelands-tour'      => '',
		// 'safari-day-trip'          => '',
		// 'shark-cage-gansbaai'      => '',
	);
}

/**
 * Output the data layer listener.
 *
 * Deliberately vanilla JS with no dependencies — this runs on every page and
 * is not worth a library.
 */
function primetours_affiliate_tracking(): void {
	// No listener without a container to receive the push.
	if ( ! primetours_should_load_gtm() ) {
		return;
	}

	$destinations = wp_json_encode( primetours_go_destinations() );
	$positions    = wp_json_encode( PRIMETOURS_LINK_POSITIONS );

	// Current page context, resolved server-side where it is reliable.
	$experience = '';
	if ( is_singular() ) {
		$experience = get_post_field( 'post_name', get_the_ID() );
	} elseif ( is_front_page() ) {
		$experience = 'homepage';
	}
	$experience = wp_json_encode( $experience );

	?>
	<script id="pt-affiliate-tracking">
	(function () {
		'use strict';

		var DESTINATIONS   = <?php echo $destinations; // phpcs:ignore WordPress.Security.EscapeOutput ?>;
		var VALID_POSITION = <?php echo $positions;    // phpcs:ignore WordPress.Security.EscapeOutput ?>;
		var PAGE_SLUG      = <?php echo $experience;   // phpcs:ignore WordPress.Security.EscapeOutput ?>;

		window.dataLayer = window.dataLayer || [];

		/**
		 * Work out where on the page a link sits.
		 *
		 * Preference order:
		 *   1. An explicit data-pt-position attribute (author override)
		 *   2. The nearest recognised component wrapper
		 *   3. 'inline' — a link in body copy
		 */
		function resolvePosition(link) {
			var explicit = link.getAttribute('data-pt-position');
			if (explicit && VALID_POSITION.indexOf(explicit) !== -1) {
				return explicit;
			}

			if (link.closest('.pt-booking__cta'))       { return 'booking_module'; }
			if (link.closest('.pt-booking__secondary')) { return 'booking_secondary'; }
			if (link.closest('.pt-booking'))            { return 'booking_module'; }
			if (link.closest('.pt-quick-answer'))       { return 'quick_answer'; }
			if (link.closest('.pt-verdict'))            { return 'verdict'; }
			if (link.closest('.pt-table'))              { return 'comparison_table'; }
			if (link.closest('.pt-experience-card'))    { return 'homepage_card'; }

			return 'inline';
		}

		/**
		 * Pull the slug out of a /go/ URL.
		 */
		function resolveSlug(href) {
			var match = href.match(/\/go\/([a-z0-9\-]+)/i);
			return match ? match[1].toLowerCase() : '';
		}

		document.addEventListener('click', function (e) {
			var link = e.target.closest ? e.target.closest('a[href*="/go/"]') : null;
			if (!link) { return; }

			var slug = resolveSlug(link.getAttribute('href') || '');
			if (!slug) { return; }

			window.dataLayer.push({
				event: 'affiliate_click',
				affiliate_destination: DESTINATIONS[slug] || 'unknown',
				experience: PAGE_SLUG,
				link_position: resolvePosition(link),
				go_slug: slug
			});
		}, true); // Capture phase, so it fires before any navigation handler.
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'primetours_affiliate_tracking', 20 );

/**
 * Warn in the admin if a /go/ slug has no destination mapped.
 *
 * An unmapped slug still tracks, but reports 'unknown' as its destination —
 * which quietly ruins the which-OTA-converts-better analysis that
 * affiliates.md depends on. Better to be told now than to discover it in a
 * quarter's worth of data.
 */
function primetours_check_go_mappings(): void {
	if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'get_posts' ) ) {
		return;
	}

	$mapped = array_keys( primetours_go_destinations() );
	$links  = get_posts(
		array(
			'post_type'      => 'thirstylink',
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'post_status'    => 'publish',
		)
	);

	$unmapped = array();
	foreach ( $links as $id ) {
		$slug = get_post_field( 'post_name', $id );
		if ( $slug && ! in_array( $slug, $mapped, true ) ) {
			$unmapped[] = $slug;
		}
	}

	if ( empty( $unmapped ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p><strong>Prime Tours tracking:</strong> %d affiliate link(s) have no destination mapped and will report as <code>unknown</code> in GA4: <code>%s</code>. Add them to <code>primetours_go_destinations()</code> in <code>inc/tracking.php</code>.</p></div>',
		count( $unmapped ),
		esc_html( implode( '</code>, <code>', $unmapped ) )
	);
}
add_action( 'admin_notices', 'primetours_check_go_mappings' );
