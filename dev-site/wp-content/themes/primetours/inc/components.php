<?php
/**
 * Prime Tours — the seven reusable components (build.md §6).
 *
 * Each function returns escaped HTML using the CSS classes already defined
 * in style.css. Nothing here queries Product/Offer/Review schema — that is
 * deliberately absent per build.md §7.
 *
 * @package PrimeTours
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * Author identity — single source of truth for the byline and CTA copy.
 *
 * Andrew's surname, operating dates and trip volume are UNVERIFIED
 * (identity.md §3, CLAUDE.md). Do not add them here until Andrew confirms —
 * see primetours_author_entity() in primetours-core.php for the schema
 * equivalent, which carries the same restriction.
 * ========================================================================= */

const PRIMETOURS_AUTHOR_NAME       = 'Andrew';
const PRIMETOURS_AUTHOR_CREDENTIAL = 'Ran private tours in Cape Town. Now writes about which ones are worth booking.';

/**
 * URL of Andrew's byline photo, or empty string.
 *
 * No stock photography — identity.md §5 is explicit that this is a hard
 * rule. Leave empty until a real on-location photo exists; the byline
 * degrades gracefully without an <img>.
 */
function primetours_author_photo_url(): string {
	$attachment_id = (int) get_theme_mod( 'primetours_author_photo_id', 0 );
	if ( ! $attachment_id ) {
		return '';
	}
	$src = wp_get_attachment_image_url( $attachment_id, 'pt-byline' );
	return $src ? $src : '';
}

/* =========================================================================
 * 1. Quick Answer box — the GEO workhorse.
 * ========================================================================= */

/**
 * @param string $answer Plain text or minimal inline HTML, 40-60 words.
 */
function primetours_quick_answer( string $answer ): string {
	if ( '' === trim( $answer ) ) {
		return '';
	}
	return sprintf(
		'<div class="pt-quick-answer"><span class="pt-quick-answer__label">%s</span><p>%s</p></div>',
		esc_html__( 'Quick answer', 'primetours' ),
		wp_kses_post( $answer )
	);
}

/* =========================================================================
 * 2. Byline block — the primary E-E-A-T signal. Every substantial page.
 * ========================================================================= */

/**
 * @param int|null $post_id Defaults to the current post in the loop.
 */
function primetours_byline( ?int $post_id = null ): string {
	$post_id = $post_id ?? get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$photo = primetours_author_photo_url();
	$img   = $photo
		? sprintf( '<img class="pt-byline__photo" src="%s" alt="%s" width="48" height="48" loading="lazy">', esc_url( $photo ), esc_attr( PRIMETOURS_AUTHOR_NAME ) )
		: '';

	$published = get_the_date( get_option( 'date_format' ), $post_id );
	$modified  = get_the_modified_date( get_option( 'date_format' ), $post_id );
	$dates     = ( $modified !== $published )
		/* translators: 1: publish date, 2: last updated date */
		? sprintf( esc_html__( 'Published %1$s · Updated %2$s', 'primetours' ), esc_html( $published ), esc_html( $modified ) )
		/* translators: %s: publish date */
		: sprintf( esc_html__( 'Published %s', 'primetours' ), esc_html( $published ) );

	return sprintf(
		'<div class="pt-byline">%s<div><div class="pt-byline__name">%s</div><div class="pt-byline__credential">%s</div><div class="pt-byline__dates">%s</div></div></div>',
		$img,
		esc_html( PRIMETOURS_AUTHOR_NAME ),
		esc_html( PRIMETOURS_AUTHOR_CREDENTIAL ),
		$dates
	);
}

/* =========================================================================
 * 3. Booking handoff module — the highest-stakes component on the site.
 * identity.md §4c: always name the destination platform, state Prime Tours
 * is not the operator, and show price + cancellation terms.
 * ========================================================================= */

/**
 * @param int|null $post_id An `experience` post. Defaults to current post.
 */
function primetours_booking_module( ?int $post_id = null ): string {
	$post_id = $post_id ?? get_the_ID();
	if ( ! $post_id || 'experience' !== get_post_type( $post_id ) ) {
		return '';
	}

	$price        = get_field( 'price_from_zar', $post_id );
	$duration     = get_field( 'duration_hours', $post_id );
	$cancellation = get_field( 'cancellation_terms', $post_id );
	$gyg_link     = get_field( 'gyg_affiliate_link', $post_id );
	$viator_link  = get_field( 'viator_affiliate_link', $post_id );

	// Primary CTA prefers GetYourGuide (strategy.md §1); falls back to Viator.
	$primary_link     = $gyg_link ?: $viator_link;
	$primary_platform = $gyg_link ? 'GetYourGuide' : ( $viator_link ? 'Viator' : '' );

	if ( ! $primary_link || ! $primary_platform ) {
		return ''; // Nothing to send a reader to — don't render a dead CTA.
	}

	$slug = get_post_field( 'post_name', $post_id );

	$meta_parts = array();
	if ( $price ) {
		$meta_parts[] = sprintf( '<span class="pt-booking__price">%s</span>', esc_html( sprintf( 'From R%s pp', number_format_i18n( (float) $price ) ) ) );
	}
	if ( $duration ) {
		$meta_parts[] = sprintf( '<span>%s</span>', esc_html( sprintf( '%s hours', rtrim( rtrim( (string) $duration, '0' ), '.' ) ) ) );
	}
	if ( $cancellation ) {
		$meta_parts[] = sprintf( '<span>%s</span>', esc_html( (string) $cancellation ) );
	}

	$secondary = '';
	if ( $viator_link && $gyg_link ) {
		$secondary = sprintf(
			'<p class="pt-booking__secondary"><a href="%s" rel="sponsored nofollow noopener" target="_blank" data-affiliate-destination="viator" data-affiliate-experience="%s" data-affiliate-position="booking-module-secondary">%s</a></p>',
			esc_url( $viator_link ),
			esc_attr( $slug ),
			esc_html__( 'Also listed on Viator', 'primetours' )
		);
	}

	$disclaimer = sprintf(
		/* translators: %s: destination booking platform, e.g. GetYourGuide */
		esc_html__( "We're not the operator — %s handles the booking and your money; we earn a small commission if you book through this link, at no extra cost to you.", 'primetours' ),
		esc_html( $primary_platform )
	);

	return sprintf(
		'<div class="pt-booking"><div class="pt-booking__meta">%s</div><a href="%s" class="pt-booking__cta" rel="sponsored nofollow noopener" target="_blank" data-affiliate-destination="%s" data-affiliate-experience="%s" data-affiliate-position="booking-module">%s</a>%s<p class="pt-booking__disclaimer">%s</p></div>',
		implode( '', $meta_parts ),
		esc_url( $primary_link ),
		esc_attr( strtolower( $primary_platform ) ),
		esc_attr( $slug ),
		esc_html( sprintf( 'Check availability on %s', $primary_platform ) ),
		$secondary,
		$disclaimer
	);
}

/**
 * Shortcode wrapper so articles can embed an experience's booking module
 * inline — e.g. a planning guide linking to the Cape Point day tour.
 * Usage: [pt_booking id="123"]
 */
function primetours_booking_shortcode( $atts ): string {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'pt_booking' );
	return primetours_booking_module( (int) $atts['id'] ?: null );
}
add_shortcode( 'pt_booking', 'primetours_booking_shortcode' );

/* =========================================================================
 * 4. Verdict block — the signature editorial element.
 * ========================================================================= */

/**
 * @param string $worth_it 'yes' | 'no' | 'depends'.
 * @param string $reasoning The honest call, in Andrew's voice.
 */
function primetours_verdict( string $worth_it, string $reasoning ): string {
	$worth_it = strtolower( $worth_it );
	$labels   = array(
		'yes'     => __( 'Worth it', 'primetours' ),
		'no'      => __( 'Not worth it', 'primetours' ),
		'depends' => __( 'Depends', 'primetours' ),
	);
	if ( ! isset( $labels[ $worth_it ] ) || '' === trim( $reasoning ) ) {
		return '';
	}

	$modifier = in_array( $worth_it, array( 'yes', 'no' ), true ) ? ' pt-verdict--' . $worth_it : '';

	return sprintf(
		'<div class="pt-verdict%s"><span class="pt-verdict__label">%s</span><p>%s</p></div>',
		esc_attr( $modifier ),
		esc_html( $labels[ $worth_it ] ),
		wp_kses_post( $reasoning )
	);
}

/**
 * Convenience wrapper reading straight off an experience post's ACF fields.
 */
function primetours_the_verdict( ?int $post_id = null ): string {
	$post_id = $post_id ?? get_the_ID();
	if ( ! $post_id || 'experience' !== get_post_type( $post_id ) ) {
		return '';
	}
	$worth_it = (string) get_field( 'worth_it', $post_id );
	$verdict  = (string) get_field( 'verdict_short', $post_id );
	return primetours_verdict( $worth_it, $verdict );
}

/* =========================================================================
 * 5. Comparison table — serves readers and AI extraction equally.
 * ========================================================================= */

/**
 * @param string[]   $headers Column headings.
 * @param string[][] $rows    Each row an array of cell values, same length as $headers.
 *                            Cell values are escaped as plain text; pass pre-built
 *                            links etc. only from trusted, already-escaped sources.
 */
function primetours_table( array $headers, array $rows ): string {
	if ( empty( $headers ) || empty( $rows ) ) {
		return '';
	}

	$thead = '<tr>' . implode( '', array_map( static fn( $h ) => '<th>' . esc_html( $h ) . '</th>', $headers ) ) . '</tr>';

	$tbody = '';
	foreach ( $rows as $row ) {
		$tbody .= '<tr>' . implode( '', array_map( static fn( $c ) => '<td>' . esc_html( (string) $c ) . '</td>', $row ) ) . '</tr>';
	}

	return sprintf(
		'<div class="pt-table-wrap"><table class="pt-table"><thead>%s</thead><tbody>%s</tbody></table></div>',
		$thead,
		$tbody
	);
}

/* =========================================================================
 * 6. Inline affiliate disclosure — identity.md §4b.
 * ========================================================================= */

function primetours_disclosure( ?string $text = null ): string {
	$text = $text ?? __( "We earn a commission if you book through this link. It costs you nothing extra and doesn't affect what we recommend.", 'primetours' );
	return sprintf( '<p class="pt-disclosure">%s</p>', esc_html( $text ) );
}

/* =========================================================================
 * 7. Last verified stamp.
 * ========================================================================= */

/**
 * @param string|null $date_ymd Y-m-d. Defaults to the current post's
 *                               last_verified_date ACF field.
 */
function primetours_verified_stamp( ?string $date_ymd = null, ?int $post_id = null ): string {
	$post_id  = $post_id ?? get_the_ID();
	$date_ymd = $date_ymd ?? ( $post_id ? (string) get_field( 'last_verified_date', $post_id ) : '' );

	if ( '' === $date_ymd ) {
		return '';
	}

	$timestamp = strtotime( $date_ymd );
	if ( ! $timestamp ) {
		return '';
	}

	return sprintf(
		'<p class="pt-verified">%s</p>',
		esc_html( sprintf( 'Prices and details verified %s.', date_i18n( get_option( 'date_format' ), $timestamp ) ) )
	);
}
