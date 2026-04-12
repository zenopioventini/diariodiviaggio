<?php
/**
 * Template per la singola Tappa.
 * Layout a 2 colonne: Contenuto a sinistra, Sidebar Sticky a destra.
 */
get_header(); ?>

	<!-- Hero: Video o Immagine in evidenza -->
	<?php 
	$video_url = get_post_meta( get_the_ID(), '_td_featured_video', true );
	if ( ! empty( $video_url ) ) : 
	?>
		<div class="site-hero td-responsive-video-wrap" style="height: clamp(300px, 50vh, 600px);">
			<?php echo wp_oembed_get( $video_url ); ?>
		</div>
	<?php elseif ( has_post_thumbnail() ) : ?>
		<div class="site-hero" style="height: clamp(300px, 50vh, 600px);">
			<?php the_post_thumbnail( 'full', array( 'class' => 'site-hero__image' ) ); ?>
			<div class="site-hero__overlay" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(26,26,26,1) 100%);"></div>
		</div>
	<?php else : ?>
		<div style="height: 80px; background:#1a1a1a;"></div>
	<?php endif; ?>

	<main id="main" class="site-main">
		<div class="container container--wide">

			<?php while ( have_posts() ) : the_post(); 
				$entry_id   = get_the_ID();
				$arrival    = get_field('field_entry_arrivo', $entry_id);
				$departure  = get_field('field_entry_partenza', $entry_id);
				$map        = get_field('field_entry_posizione', $entry_id);
				$km_reali   = get_field('field_entry_km_reali', $entry_id);
				$mezzo      = get_field('field_entry_mezzo_trasporto', $entry_id);
				$meteo      = get_field('field_entry_meteo', $entry_id);
				$valutaz    = get_field('field_entry_valutazione', $entry_id);

				// Recupero il viaggio genitore
				$terms = get_the_terms($entry_id, 'td_trip_cat');
				$parent_trip = false;
				if ($terms && !is_wp_error($terms)) {
					$trip_posts = get_posts(array(
						'name'           => $terms[0]->slug,
						'post_type'      => Travel_Diary_Cpt_Trip::POST_TYPE,
						'post_status'    => 'publish',
						'posts_per_page' => 1,
					));
					if (!empty($trip_posts)) {
						$parent_trip = $trip_posts[0];
					}
				}
			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'td-entry-single' ); ?>>

					<!-- Header della Tappa -->
					<header class="entry-header" style="text-align: center; max-width: 800px; margin: 0 auto 48px;">
						<?php 
						if ( $parent_trip ) : 
							$current_token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
							$token_query   = $current_token ? '?token=' . urlencode($current_token) : '';
						?>
							<a href="<?php echo esc_url(get_permalink($parent_trip->ID) . $token_query); ?>" class="td-breadcrumb">
								<span class="dashicons dashicons-arrow-left-alt" style="vertical-align:middle;"></span> 
								<?php echo esc_html($parent_trip->post_title); ?>
							</a>
						<?php endif; ?>
						
						<?php the_title( '<h1 class="entry-title" style="font-size:clamp(2.5rem, 5vw, 4rem); margin: 16px 0;">', '</h1>' ); ?>
						
						<div class="entry-meta" style="justify-content: center; font-size: 0.95rem;">
							<span><span class="dashicons dashicons-calendar-alt"></span> <?php echo get_the_date(); ?></span>
							<span><span class="dashicons dashicons-edit"></span> <?php the_author(); ?></span>
						</div>
					</header>

					<!-- Layout a 2 Colonne -->
					<div class="td-entry-layout">
						
						<!-- Colonna Testo (Left) -->
						<div class="td-entry-main-col">
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
						</div>

						<!-- Sidebar Sticky (Right) -->
						<aside class="td-entry-sidebar-col">
							<div class="td-sticky-box">
								<h3 class="td-sidebar-title">Board di Tappa</h3>
								
								<ul class="td-metrics-list">
									<?php if ($arrival): ?>
										<li><span class="td-metrics-icon">📥</span> <div class="td-metrics-val"><strong>Arrivo:</strong> <?php echo esc_html($arrival); ?></div></li>
									<?php endif; ?>
									<?php if ($departure): ?>
										<li><span class="td-metrics-icon">📤</span> <div class="td-metrics-val"><strong>Partenza:</strong> <?php echo esc_html($departure); ?></div></li>
									<?php endif; ?>
									<?php if ($mezzo): ?>
										<li><span class="td-metrics-icon">🚗</span> <div class="td-metrics-val"><strong>Mezzo:</strong> <?php echo esc_html($mezzo); ?></div></li>
									<?php endif; ?>
									<?php if ($meteo): ?>
										<li><span class="td-metrics-icon">☀️</span> <div class="td-metrics-val"><strong>Meteo:</strong> <?php echo esc_html($meteo); ?></div></li>
									<?php endif; ?>
									<?php if ($valutaz): ?>
										<li><span class="td-metrics-icon">⭐</span> <div class="td-metrics-val"><strong>Voto:</strong> <?php echo esc_html($valutaz); ?> su 5</div></li>
									<?php endif; ?>
									<?php if ($km_reali): ?>
										<li><span class="td-metrics-icon">🗺️</span> <div class="td-metrics-val"><strong>Percorsi:</strong> <?php echo esc_html($km_reali); ?> km</div></li>
									<?php endif; ?>
								</ul>

								<!-- Minimappa (In futuro Leaflet MiniMap qui) -->
								<?php 
								$map_data = array();
								if ($map && isset($map['lat']) && isset($map['lng'])) {
									$map_data[] = array(
										'lat' => floatval($map['lat']),
										'lng' => floatval($map['lng']),
										'title' => get_the_title(),
										'url'   => '',
										'type'  => 'entry'
									);
								}

								// Aggiunta EXIF della tappa
								if (class_exists('Travel_Diary_Exif') && class_exists('Travel_Diary_Gallery')) {
									if (!Travel_Diary_Exif::is_disabled($entry_id)) {
										$gallery = Travel_Diary_Gallery::get_gallery_ids($entry_id);
										foreach ($gallery as $attachment_id) {
											$coords = Travel_Diary_Exif::get_coords($attachment_id);
											if ($coords) {
												$thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');
												$map_data[] = array(
													'lat'   => $coords['lat'],
													'lng'   => $coords['lng'],
													'title' => 'Foto #' . $attachment_id,
													'url'   => '',
													'type'  => 'photo',
													'thumb' => $thumb
												);
											}
										}
									}
								}

								// Aggiunta POI (Punti di Interesse) della Tappa
								if (function_exists('get_field')) {
									$poi_list = get_field('field_entry_poi_list', $entry_id);
									if (!empty($poi_list)) {
										foreach ($poi_list as $poi) {
											$pos = $poi['posizione'] ?? null;
											if (!empty($pos) && isset($pos['lat']) && isset($pos['lng'])) {
												$map_data[] = array(
													'lat'   => floatval($pos['lat']),
													'lng'   => floatval($pos['lng']),
													'title' => esc_html($poi['titolo'] ?? 'POI'),
													'url'   => '',
													'type'  => 'poi',
													'thumb' => ''
												);
											}
										}
									}
								}

								if (!empty($map_data)) : ?>
									<div id="td-entry-map" class="td-sidebar-minimap-placeholder" style="margin-top:20px; height:250px; background:#111;">
										<!-- Mappa Leaflet -->
									</div>
									<script>var tdTripMapData = <?php echo json_encode($map_data); ?>;</script>
								<?php endif; ?>

							</div>
						</aside>

					</div> <!-- Fine Layout a 2 Colonne -->

					<!-- Sezioni Larghe (Full Width) -->
					<div class="td-entry-footer-sections">
						<?php 
						// Punti di Interesse (POI) della Tappa
						$poi_path = WP_PLUGIN_DIR . '/trave-diary/public/partials/travel-diary-poi.php';
						if ( file_exists( $poi_path ) ) {
							include $poi_path;
						}

						// Spese della Tappa
						$expenses_path = WP_PLUGIN_DIR . '/trave-diary/public/partials/travel-diary-expenses.php';
						if ( file_exists( $expenses_path ) ) {
							include $expenses_path;
						}

						// Galleria fotografica della Tappa
						$gallery_path = WP_PLUGIN_DIR . '/trave-diary/public/partials/travel-diary-gallery.php';
						if ( file_exists( $gallery_path ) ) {
							include $gallery_path;
						}
						?>
					</div>

				</article>

			<?php endwhile; ?>

		</div>
	</main>

<?php get_footer(); ?>
