<?php
/**
 * Archive template for the `experience` post type — /cape-town-tours/.
 *
 * A card grid, not a listicle: each card carries the verdict and price so
 * the archive itself is useful, not just a gateway to the full reviews.
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
			?>

			<header class="pt-archive-header">
				<h1><?php esc_html_e( 'Cape Town Tours', 'primetours' ); ?></h1>
				<p><?php esc_html_e( "The tours we recommend, and a few we don't. Prices, durations and cancellation terms below are checked and dated \u{2014} see each review for when.", 'primetours' ); ?></p>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="pt-experience-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						$worth_it = (string) get_field( 'worth_it' );
						$price    = get_field( 'price_from_zar' );
						$duration = get_field( 'duration_hours' );
						?>
						<article <?php post_class( 'pt-experience-card' ); ?>>
							<a href="<?php the_permalink(); ?>" class="pt-experience-card__link">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'pt-card', array( 'class' => 'pt-experience-card__image' ) ); ?>
								<?php endif; ?>
								<h2 class="pt-experience-card__title"><?php the_title(); ?></h2>
							</a>

							<?php if ( $worth_it ) : ?>
								<span class="pt-experience-card__badge pt-experience-card__badge--<?php echo esc_attr( $worth_it ); ?>">
									<?php
									echo esc_html(
										array(
											'yes'     => __( 'Worth it', 'primetours' ),
											'no'      => __( 'Not worth it', 'primetours' ),
											'depends' => __( 'Depends', 'primetours' ),
										)[ $worth_it ] ?? ''
									);
									?>
								</span>
							<?php endif; ?>

							<p class="pt-experience-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

							<p class="pt-experience-card__meta">
								<?php if ( $price ) : ?>
									<?php echo esc_html( sprintf( 'From R%s pp', number_format_i18n( (float) $price ) ) ); ?>
								<?php endif; ?>
								<?php if ( $duration ) : ?>
									&middot; <?php echo esc_html( sprintf( '%s hours', rtrim( rtrim( (string) $duration, '0' ), '.' ) ) ); ?>
								<?php endif; ?>
							</p>
						</article>
						<?php
					endwhile;
					?>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'No reviews published yet.', 'primetours' ); ?></p>
			<?php endif; ?>

			<?php
			do_action( 'generate_after_main_content' );
			?>
		</main>
	</div>

	<?php
	do_action( 'generate_after_primary_content_area' );

	generate_construct_sidebars();

	get_footer();
