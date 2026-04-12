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
				$arrival    = get_field('field_entry_data_principale', $entry_id);
				$departure  = get_field('field_entry_data_fine', $entry_id);
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
							<span><?php echo Travel_Diary_Icons::get('calendar', ['width'=>16,'height'=>16,'class'=>'td-inline-icon']); ?> <?php echo get_the_date(); ?></span>
							<span><?php echo Travel_Diary_Icons::get('user', ['width'=>16,'height'=>16,'class'=>'td-inline-icon']); ?> <?php the_author(); ?></span>
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
									<?php if ($arrival): 
										// Calcolo la stringa di visualizzazione
										$date_str = date_i18n('d/m/Y', strtotime($arrival));
										if (strpos($arrival, ':') !== false && !$departure) {
											$date_str = date_i18n('d/m/Y H:i', strtotime($arrival));
										}
										if ($departure) {
											$date_str .= ' - ' . date_i18n('d/m/Y', strtotime($departure));
										}
									?>
										<li><span class="td-metrics-icon"><?php echo Travel_Diary_Icons::get('calendar'); ?></span> <div class="td-metrics-val"><strong>Data/e:</strong> <?php echo esc_html($date_str); ?></div></li>
									<?php endif; ?>
									<?php if ($mezzo): ?>
										<li><span class="td-metrics-icon"><?php echo Travel_Diary_Icons::get($mezzo); ?></span> <div class="td-metrics-val"><strong>Mezzo:</strong> <?php echo esc_html(ucfirst($mezzo)); ?></div></li>
									<?php endif; ?>
									<?php if ($meteo): ?>
										<li><span class="td-metrics-icon"><?php echo Travel_Diary_Icons::get($meteo); ?></span> <div class="td-metrics-val"><strong>Meteo:</strong> <?php echo esc_html(ucfirst(str_replace('_', ' ', $meteo))); ?></div></li>
									<?php endif; ?>
									<?php if ($valutaz): ?>
										<li><span class="td-metrics-icon"><?php echo Travel_Diary_Icons::get('star'); ?></span> <div class="td-metrics-val"><strong>Voto:</strong> <?php echo esc_html($valutaz); ?> su 5</div></li>
									<?php endif; ?>
									<?php if ($km_reali): ?>
										<li><span class="td-metrics-icon"><?php echo Travel_Diary_Icons::get('map-pin'); ?></span> <div class="td-metrics-val"><strong>Percorsi:</strong> <?php echo esc_html($km_reali); ?> km</div></li>
									<?php endif; ?>
								</ul>

								<!-- Minimappa (In futuro Leaflet MiniMap qui) -->
								<?php 
								$map_data = array();

								// 1. Posizione principale della tappa (ACF google_map)
								if ($map && isset($map['lat']) && isset($map['lng'])) {
									$map_data[] = array(
										'lat'   => floatval($map['lat']),
										'lng'   => floatval($map['lng']),
										'title' => get_the_title(),
										'url'   => '',
										'type'  => 'entry'
									);
								}

								// 2. POI della Tappa: coordinate a cascata
								//    1) Campo google_map 'posizione' (manuale)
								//    2) EXIF dell'immagine del POI (automatico)
								//    3) Nessuna coordinata → POI escluso dalla mappa
								if (function_exists('get_field')) {
									$poi_list = get_field('field_entry_poi_list', $entry_id);
									if (!empty($poi_list)) {
										foreach ($poi_list as $poi) {
											$poi_lat   = null;
											$poi_lng   = null;
											$poi_thumb = '';

											// Thumbnail dell'immagine POI (comune a entrambi i percorsi)
											$poi_img_id = $poi['immagine'] ?? 0;
											if ($poi_img_id) {
												$poi_thumb = wp_get_attachment_image_url($poi_img_id, 'thumbnail') ?: '';
											}

											// Priorità 1: coordinate manuali dal campo google_map
											$pos = $poi['posizione'] ?? null;
											if (!empty($pos) && isset($pos['lat']) && isset($pos['lng'])) {
												$poi_lat = floatval($pos['lat']);
												$poi_lng = floatval($pos['lng']);
											}
											// Priorità 2: EXIF GPS dall'immagine del POI
											elseif ($poi_img_id && class_exists('Travel_Diary_Exif')) {
												$exif = Travel_Diary_Exif::get_coords($poi_img_id);
												if ($exif) {
													$poi_lat = $exif['lat'];
													$poi_lng = $exif['lng'];
												}
											}

											// Nessuna coordinata → salta questo POI
											if ($poi_lat === null || $poi_lng === null) continue;

											$map_data[] = array(
												'lat'   => $poi_lat,
												'lng'   => $poi_lng,
												'title' => esc_html($poi['titolo'] ?? 'POI'),
												'url'   => '',
												'type'  => 'poi',
												'thumb' => $poi_thumb,
												'cat'   => esc_html($poi['categoria'] ?? ''),
											);
										}
									}
								}

								// Nota: le foto EXIF della gallery NON vengono aggiunte alla mappa
								// della singola tappa (già visibili in galleria più sotto).
								// Vengono invece usate nella mappa globale del Viaggio.

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
