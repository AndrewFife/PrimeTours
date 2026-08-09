<?php
/**
 * Plugin Name: Prime Tours Core
 * Description: Content model, ACF JSON sync, schema and crawler policy. Lives in mu-plugins so it survives theme changes and cannot be deactivated by accident.
 * Version:     0.1.0
 * Author:      Utility Cloud Consulting
 *
 * @package PrimeTours
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * 1. ACF JSON SYNC
 *
 * This is what stops local, staging and production drifting apart. Field
 * groups are saved as JSON into the repo and loaded from it. Must be
 * enabled BEFORE the first field group is created — see build.md §2.
 * ========================================================================= */

add_filter(
	'acf/settings/save_json',
	static fn(): string => WP_CONTENT_DIR . '/acf-json'
);

add_filter(
	'acf/settings/load_json',
	static function ( array $paths ): array {
		unset( $paths[0] );
		$paths[] = WP_CONTENT_DIR . '/acf-json';
		return $paths;
	}
);

/* =========================================================================
 * 2. CONTENT MODEL — see build.md §5
 * ========================================================================= */

/**
 * Register the `experience` post type — tour review pages.
 *
 * Note the deliberate labelling: these are reviews of other operators'
 * tours, never products Prime Tours sells.
 */
function primetours_register_experience(): void {
	register_post_type(
		'experience',
		array(
			'labels'        => array(
				'name'               => __( 'Experiences', 'primetours' ),
				'singular_name'      => __( 'Experience', 'primetours' ),
				'add_new_item'       => __( 'Add New Experience Review', 'primetours' ),
				'edit_item'          => __( 'Edit Experience Review', 'primetours' ),
				'not_found'          => __( 'No experience reviews yet', 'primetours' ),
			),
			'public'        => true,
			'has_archive'   => 'cape-town-tours',
			'rewrite'       => array(
				'slug'       => '',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-location-alt',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'show_in_rest'  => true, // Required for the content pipeline.
			'taxonomies'    => array( 'region', 'experience_type' ),
		)
	);
}
add_action( 'init', 'primetours_register_experience' );

/**
 * Register taxonomies.
 *
 * `region` is hierarchical by design — it is what makes expansion beyond
 * Cape Town structural rather than a rewrite.
 */
function primetours_register_taxonomies(): void {
	register_taxonomy(
		'region',
		array( 'experience', 'post' ),
		array(
			'labels'            => array(
				'name'          => __( 'Regions', 'primetours' ),
				'singular_name' => __( 'Region', 'primetours' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'region' ),
		)
	);

	register_taxonomy(
		'experience_type',
		array( 'experience' ),
		array(
			'labels'            => array(
				'name'          => __( 'Experience Types', 'primetours' ),
				'singular_name' => __( 'Experience Type', 'primetours' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'type' ),
		)
	);
}
add_action( 'init', 'primetours_register_taxonomies' );

/* =========================================================================
 * 3. SCHEMA
 *
 * Organization <-> Person (Andrew) is the highest-value markup on the site.
 * It is what lets Google and the AI engines resolve Andrew as a real,
 * credentialed entity rather than a byline.
 *
 * DELIBERATELY ABSENT: Product, Offer, and aggregate Review schema.
 * Prime Tours does not sell these tours. Marking them up as products on
 * offer would be both a structured-data violation and a repeat of the
 * operator-impersonation problem the rebrand exists to fix.
 * ========================================================================= */

/**
 * Author entity. Values marked [CONFIRM] must be verified before launch —
 * see identity.md §3.
 */
function primetours_author_entity(): array {
	return array(
		'@type'       => 'Person',
		'@id'         => home_url( '/#andrew' ),
		'name'        => 'Andrew',                       // [CONFIRM] surname
		'jobTitle'    => 'Founder and Editor',
		'description' => 'Ran private tours in Cape Town before founding Prime Tours as an independent travel guide.', // [CONFIRM] years
		'url'         => home_url( '/about/' ),
		'worksFor'    => array( '@id' => home_url( '/#organization' ) ),
		// 'sameAs'   => array(), // [CONFIRM] LinkedIn, industry bodies
	);
}

/**
 * Output Organization + Person graph in the head.
 */
function primetours_output_schema(): void {
	$graph = array(
		array(
			'@type'       => 'Organization',
			'@id'         => home_url( '/#organization' ),
			'name'        => 'Prime Tours',
			'alternateName' => 'Prime Tours — Independent Cape Town Travel Guide',
			'url'         => home_url( '/' ),
			'description' => 'Independent travel guide to Cape Town and South Africa. We are not a tour operator.',
			'founder'     => array( '@id' => home_url( '/#andrew' ) ),
		),
		primetours_author_entity(),
	);

	if ( is_singular( array( 'post', 'experience' ) ) ) {
		$graph[] = array(
			'@type'            => 'Article',
			'@id'              => get_permalink() . '#article',
			'headline'         => get_the_title(),
			'author'           => array( '@id' => home_url( '/#andrew' ) ),
			'publisher'        => array( '@id' => home_url( '/#organization' ) ),
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'mainEntityOfPage' => get_permalink(),
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}
add_action( 'wp_head', 'primetours_output_schema', 5 );

/* =========================================================================
 * 4. AI CRAWLER POLICY — see build.md §9
 *
 * Explicitly allow AI crawlers. The trade-off is real: they read without
 * always sending a click. For an affiliate site that is still the right
 * bargain — a citation carries our name into a high-intent planning
 * conversation, and a third of US trip-planners now research that way.
 * ========================================================================= */

function primetours_robots_txt( string $output, bool $public ): string {
	if ( ! $public ) {
		return $output; // Staging stays closed. Never remove this guard.
	}

	$bots = array( 'GPTBot', 'ChatGPT-User', 'ClaudeBot', 'anthropic-ai', 'PerplexityBot', 'Google-Extended', 'CCBot', 'Applebot-Extended', 'Bytespider' );

	$output .= "\n# AI answer engines — explicitly permitted\n";
	foreach ( $bots as $bot ) {
		$output .= "User-agent: {$bot}\nAllow: /\n\n";
	}

	$output .= "Sitemap: " . home_url( '/sitemap_index.xml' ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'primetours_robots_txt', 10, 2 );

/* =========================================================================
 * 5. AFFILIATE LINK SAFETY NET
 *
 * All booking links should go through ThirstyAffiliates as cloaked
 * internal /go/ URLs. This filter is a backstop for anything that slips
 * through: it forces rel="sponsored nofollow noopener" onto raw OTA links
 * so a missed link is never an SEO liability.
 * ========================================================================= */

function primetours_fix_raw_affiliate_links( string $content ): string {
	if ( is_admin() || empty( $content ) ) {
		return $content;
	}

	$domains = array( 'getyourguide.com', 'viator.com', 'tiqets.com', 'klook.com' );
	$pattern = '#<a\s+([^>]*href=["\']https?://[^"\']*(?:' . implode( '|', array_map( 'preg_quote', $domains ) ) . ')[^"\']*["\'][^>]*)>#i';

	return (string) preg_replace_callback(
		$pattern,
		static function ( array $m ): string {
			$attrs = $m[1];
			if ( stripos( $attrs, 'rel=' ) !== false ) {
				return '<a ' . $attrs . '>';
			}
			return '<a ' . $attrs . ' rel="sponsored nofollow noopener" target="_blank">';
		},
		$content
	);
}
add_filter( 'the_content', 'primetours_fix_raw_affiliate_links', 20 );

/* =========================================================================
 * 6. HOUSEKEEPING
 * ========================================================================= */

// Comments are not part of this content model.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// XML-RPC is an attack surface with no use here.
add_filter( 'xmlrpc_enabled', '__return_false' );
