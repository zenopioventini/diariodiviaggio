<?php get_header(); ?>

	<!-- Hero: Video o Immagine in evidenza -->
	<?php 
	$video_url = get_post_meta( get_the_ID(), '_td_featured_video', true );
	if ( ! empty( $video_url ) ) : 
	?>
		<div class="single-hero td-responsive-video-wrap">
			<?php echo wp_oembed_get( $video_url ); ?>
		</div>
	<?php elseif ( has_post_thumbnail() ) : ?>
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

					<?php 
					// Galleria fotografica della Tappa
					$gallery_path = WP_PLUGIN_DIR . '/trave-diary/public/partials/travel-diary-gallery.php';
					if ( file_exists( $gallery_path ) ) {
						include $gallery_path;
					}
					?>

				</article>

			<?php endwhile; ?>

		</div>
	</main>

<?php get_footer(); ?>
