<?php
/**
 * Prime Tours — block patterns for article (Post) content.
 *
 * The `experience` CPT gets its components rendered straight from ACF
 * fields in single-experience.php — a reader never needs to hand-place a
 * byline or verified stamp there, so those aren't patterns. These are for
 * Posts, where an editor is writing free-form and needs to insert a
 * component at a chosen point. Custom HTML blocks are used deliberately:
 * the component markup (build.md §6) uses classes with no native block
 * equivalent, and this avoids needing a JS build step for real Gutenberg
 * blocks — see the ambiguity flagged when this build started.
 *
 * @package PrimeTours
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function primetours_register_block_pattern_category(): void {
	register_block_pattern_category(
		'primetours',
		array( 'label' => __( 'Prime Tours', 'primetours' ) )
	);
}
add_action( 'init', 'primetours_register_block_pattern_category' );

function primetours_register_block_patterns(): void {
	register_block_pattern(
		'primetours/quick-answer',
		array(
			'title'       => __( 'Quick Answer box', 'primetours' ),
			'description' => __( 'Front-loaded 40-60 word answer to the page\'s core question. Place immediately after the title.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:html -->\n" .
				'<div class="pt-quick-answer"><span class="pt-quick-answer__label">Quick answer</span><p>Write the direct 40&ndash;60 word answer here. AI retrieval systems weight opening content heavily &mdash; front-load the specific answer, save narrative build-up for later.</p></div>' .
				"\n<!-- /wp:html -->",
		)
	);

	register_block_pattern(
		'primetours/disclosure',
		array(
			'title'       => __( 'Affiliate disclosure', 'primetours' ),
			'description' => __( 'Place near the first affiliate link on the page — identity.md §4b.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:html -->\n" .
				'<p class="pt-disclosure">We earn a commission if you book through this link. It costs you nothing extra and doesn&rsquo;t affect what we recommend.</p>' .
				"\n<!-- /wp:html -->",
		)
	);

	register_block_pattern(
		'primetours/verified-stamp',
		array(
			'title'       => __( 'Last verified stamp', 'primetours' ),
			'description' => __( 'Update the date whenever you re-check the facts in this article.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:html -->\n" .
				'<p class="pt-verified">Prices and details verified [DATE — edit before publishing].</p>' .
				"\n<!-- /wp:html -->",
		)
	);

	register_block_pattern(
		'primetours/verdict',
		array(
			'title'       => __( 'Verdict block', 'primetours' ),
			'description' => __( 'The honest call: worth it, not worth it, or depends — and why.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:html -->\n" .
				'<div class="pt-verdict"><span class="pt-verdict__label">Depends</span><p>Write the honest call and the specific reasoning behind it here. Delete the pt-verdict--yes/pt-verdict--no note below if this stays a "depends".</p></div>' .
				"\n<!-- /wp:html -->" .
				"\n<!-- wp:paragraph -->\n<p><em>Add class <code>pt-verdict--yes</code> or <code>pt-verdict--no</code> to the div above (via the HTML) once the call is made — plain \"depends\" styling is the default.</em></p>\n<!-- /wp:paragraph -->",
		)
	);

	register_block_pattern(
		'primetours/comparison-table',
		array(
			'title'       => __( 'Comparison table', 'primetours' ),
			'description' => __( 'Responsive table styled for both readers and AI extraction.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:html -->\n" .
				'<div class="pt-table-wrap"><table class="pt-table"><thead><tr><th>Column A</th><th>Column B</th><th>Column C</th></tr></thead><tbody><tr><td>Row 1</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>Row 2</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></div>' .
				"\n<!-- /wp:html -->",
		)
	);

	register_block_pattern(
		'primetours/booking-handoff',
		array(
			'title'       => __( 'Booking handoff (embed an experience)', 'primetours' ),
			'description' => __( 'Pulls live price, duration, cancellation terms and the not-the-operator disclaimer from an experience post. Replace the id with the target experience\'s post ID.', 'primetours' ),
			'categories'  => array( 'primetours' ),
			'content'     => "<!-- wp:shortcode -->\n[pt_booking id=\"REPLACE_WITH_EXPERIENCE_POST_ID\"]\n<!-- /wp:shortcode -->",
		)
	);
}
add_action( 'init', 'primetours_register_block_patterns' );
