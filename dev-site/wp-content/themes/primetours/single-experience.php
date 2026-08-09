<?php
/**
 * Single `experience` template — the tour review pages.
 *
 * Structure: quick answer (GEO) -> byline (E-E-A-T) -> full write-up ->
 * verdict -> disclosure + booking handoff -> verified stamp. The booking
 * module, verdict and verified stamp render straight from ACF fields, so
 * they can never be forgotten on a published review — identity.md §4c
 * calls the handoff the highest-stakes component on the site.
 *
 * Mirrors GeneratePress's own single.php hook structure (generate_header/
 * footer, generate_before/after_main_content) so sidebar layout, spacing
 * and breadcrumbs stay consistent with the rest of the site.
 *
 * @package PrimeTours
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

	<div <?php generate_do_attr( 'content' ); ?>>
		<main <?php generate_do_attr( 'main' ); ?>>
			<?php
			do_action( 'generate_before_main_content' );

			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'pt-experience' ); ?> id="post-<?php the_ID(); ?>">
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<?php echo primetours_quick_answer( get_the_excerpt() ); // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped in helper. ?>

					<?php echo primetours_byline(); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<?php echo primetours_the_verdict(); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<?php echo primetours_disclosure(); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<?php echo primetours_booking_module(); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<?php echo primetours_verified_stamp(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</article>
				<?php
			endwhile;

			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
