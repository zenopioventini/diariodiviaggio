<?php get_header(); ?>

	<!-- Hero: immagine in evidenza del viaggio -->
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'ddv-hero', array( 'class' => 'single-hero' ) ); ?>
	<?php endif; ?>

	<main id="main" class="site-main">
		<div class="container container--narrow">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'td-trip-single' ); ?>>

					<header class="entry-header">
						<?php
						// Tassonomia td_trip_cat
						$terms = get_the_terms( get_the_ID(), 'td_trip_cat' );
						if ( $terms && ! is_wp_error( $terms ) ) :
						?>
							<div style="margin-bottom: 12px;">
								<?php foreach ( $terms as $term ) : ?>
									<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="text-accent" style="font-size: .78rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;">
										<?php echo esc_html( $term->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						<div class="entry-meta">
							<span><?php echo get_the_date(); ?></span>
							<span><?php _e( 'di', 'diario-di-viaggio' ); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>
						</div>
					</header>

					<div class="entry-content">
						<?php the_content(); ?>
						<!-- Il plugin inietta qui: mappa aggregata + statistiche + lista tappe -->
					</div>

				</article>

			<?php endwhile; ?>

		</div>
	</main>

<?php get_footer(); ?>
