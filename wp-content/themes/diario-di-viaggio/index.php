<?php get_header(); ?>

	<!-- Hero: usa immagine del post in evidenza più recente -->
	<?php
	$hero_args = array(
		'post_type'      => 'td_trip',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_key'       => '_thumbnail_id',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	$hero_query = new WP_Query( $hero_args );
	if ( $hero_query->have_posts() ) :
		$hero_query->the_post();
	?>
	<section class="site-hero">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'ddv-hero', array( 'class' => 'site-hero__image', 'alt' => get_the_title() ) ); ?>
		<?php endif; ?>
		<div class="site-hero__overlay"></div>
		<div class="site-hero__content container">
			<h1 class="site-hero__title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
			<p class="site-hero__subtitle"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
		</div>
	</section>
	<?php
	wp_reset_postdata();
	endif;
	?>

	<!-- Griglia ultimi viaggi -->
	<main id="main" class="site-main">
		<section class="site-section container">
			<div class="section-header">
				<h2><?php _e( 'Ultimi Viaggi', 'diario-di-viaggio' ); ?></h2>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'td_trip' ) ); ?>" class="section-link">
					<?php _e( 'Vedi tutti →', 'diario-di-viaggio' ); ?>
				</a>
			</div>

			<?php
			$trips = new WP_Query( array(
				'post_type'      => 'td_trip',
				'posts_per_page' => 9,
				'post_status'    => 'publish',
			) );
			?>

			<?php if ( $trips->have_posts() ) : ?>
				<div class="trips-grid">
					<?php while ( $trips->have_posts() ) : $trips->the_post(); ?>
						<?php ddv_trip_card(); ?>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<p class="text-muted"><?php _e( 'Nessun viaggio trovato.', 'diario-di-viaggio' ); ?></p>
			<?php endif; ?>
		</section>
	</main>

<?php get_footer(); ?>
