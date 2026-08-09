<?php
/**
 * Single template for `post` — planning guides and articles.
 *
 * Unlike single-experience.php, article content is free-form: the author
 * places quick-answer, disclosure, verdict and comparison-table components
 * where they belong in the piece using the block patterns registered in
 * inc/block-patterns.php (and [pt_booking id="…"] to embed a live booking
 * module for a specific experience). Only the byline is guaranteed by the
 * template itself — named authorship on every substantial page is not
 * optional (identity.md §3).
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
				<article <?php post_class( 'pt-article' ); ?> id="post-<?php the_ID(); ?>">
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<?php echo primetours_byline(); // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped in helper. ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>
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
