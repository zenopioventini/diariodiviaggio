<?php get_header(); ?>

	<!-- Hero: immagine in evidenza della tappa -->
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'ddv-hero', array( 'class' => 'single-hero' ) ); ?>
	<?php endif; ?>

	<main id="main" class="site-main">
		<div class="container container--narrow">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'td-entry-single' ); ?>>

					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<div class="entry-meta">
							<span><?php echo get_the_date(); ?></span>
							<span><?php _e( 'di', 'diario-di-viaggio' ); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>
						</div>
					</header>

					<div class="entry-content">
						<?php the_content(); ?>
						<!-- Il plugin inietta qui: dati tappa (date, mezzo, meteo, valutaz, mappa, spese) -->
					</div>

				</article>

			<?php endwhile; ?>

		</div>
	</main>

<?php get_footer(); ?>
