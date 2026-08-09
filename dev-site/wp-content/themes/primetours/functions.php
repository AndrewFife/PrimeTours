<?php
/**
 * Prime Tours child theme.
 *
 * Presentation only. Content model, post types and schema live in
 * mu-plugins/primetours-core.php so they survive a theme change.
 *
 * @package PrimeTours
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PRIMETOURS_THEME_VERSION = '0.1.0';

/**
 * Enqueue parent and child stylesheets.
 */
function primetours_enqueue_styles(): void {
	wp_enqueue_style(
		'generatepress-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'generatepress' )->get( 'Version' )
	);

	wp_enqueue_style(
		'primetours',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'generatepress-parent' ),
		PRIMETOURS_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'primetours_enqueue_styles', 20 );

/**
 * Trim front-end weight.
 *
 * Every kilobyte matters — long-haul readers on mobile networks are the
 * audience, and Core Web Vitals is a ranking input.
 */
function primetours_dequeue_bloat(): void {
	// Block library CSS is only needed where blocks actually render.
	if ( ! is_singular() ) {
		wp_dequeue_style( 'wp-block-library' );
	}
	// Emoji support: never used, always loaded.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'wp_enqueue_scripts', 'primetours_dequeue_bloat', 100 );

/**
 * Remove the WordPress version generator tag.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Theme supports.
 */
function primetours_theme_support(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	// Editorial image sizes. Original photography only — see identity.md §5.
	add_image_size( 'pt-hero', 1600, 900, true );
	add_image_size( 'pt-card', 800, 533, true );
	add_image_size( 'pt-byline', 96, 96, true );
}
add_action( 'after_setup_theme', 'primetours_theme_support' );

/**
 * Editor colour palette, mirroring the CSS custom properties.
 */
function primetours_editor_palette(): void {
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Granite', 'primetours' ),
				'slug'  => 'pt-granite',
				'color' => '#2E3438',
			),
			array(
				'name'  => __( 'Fynbos', 'primetours' ),
				'slug'  => 'pt-fynbos',
				'color' => '#3F5B4C',
			),
			array(
				'name'  => __( 'Atlantic', 'primetours' ),
				'slug'  => 'pt-atlantic',
				'color' => '#4A6572',
			),
			array(
				'name'  => __( 'Sand', 'primetours' ),
				'slug'  => 'pt-sand',
				'color' => '#EDE7DC',
			),
			array(
				'name'  => __( 'Off White', 'primetours' ),
				'slug'  => 'pt-off-white',
				'color' => '#FBFAF7',
			),
			array(
				'name'  => __( 'Ochre', 'primetours' ),
				'slug'  => 'pt-ochre',
				'color' => '#B5762E',
			),
		)
	);
	add_theme_support( 'disable-custom-colors' );
}
add_action( 'after_setup_theme', 'primetours_editor_palette' );
