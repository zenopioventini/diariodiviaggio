<?php
/**
 * Template per il singolo Viaggio Principale.
 * Layout a 2 colonne: Contenuto a sinistra, Sidebar Sticky a destra.
 */
get_header();

$current_token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$token_query   = $current_token ? '?token=' . urlencode($current_token) : '';


// Funzione Haversine (spostata qui provvisoriamente oppure gia' disponibile se chiamata dal partial, 
// ma raddoppiandola o spostandola e' più sicuro, usiamo class-travel-diary.php? No, definiamo una lambda).
if (!function_exists('td_haversine')) {
	function td_haversine($lat1, $lng1, $lat2, $lng2) {
		$R = 6371;
		$dLat = deg2rad($lat2 - $lat1);
		$dLng = deg2rad($lng2 - $lng1);
		$a = sin($dLat/2) * sin($dLat/2) +
			 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
			 sin($dLng/2) * sin($dLng/2);
		return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
	}
}

// Aggregazione dati prima di stampare layout
$trip_id   = get_the_ID();
$entries   = get_field(Travel_Diary_Cpt_Trip::FIELD_PREFIX . 'entry_of_trip', $trip_id);
$map_data  = array();

$km_totali_reali   = 0;
$km_totali_stimati = 0;
$costo_totale      = 0;
$costi_per_cat     = array();
$prev_lat = null; $prev_lng = null;

if ($entries && is_array($entries)) {
	foreach ($entries as $entry_id) {
		$entry = get_post($entry_id);
		if (!$entry || $entry->post_status !== 'publish') continue;

		$map        = get_field('field_entry_posizione', $entry_id);
		$km_reali   = get_field('field_entry_km_reali', $entry_id);
		$costi      = get_field('field_entry_costi', $entry_id);

		// Coordinate per la mappa
		if ($map && isset($map['lat']) && isset($map['lng'])) {
			$lat = floatval($map['lat']);
			$lng = floatval($map['lng']);
			$map_data[] = array('lat' => $lat, 'lng' => $lng, 'title' => $entry->post_title, 'url' => get_permalink($entry_id));

			if ($prev_lat !== null) {
				$km_totali_stimati += td_haversine($prev_lat, $prev_lng, $lat, $lng);
			}
			$prev_lat = $lat; $prev_lng = $lng;
		}

		if (!empty($km_reali)) {
			$km_totali_reali += floatval($km_reali);
		}

		if ($costi && is_array($costi)) {
			foreach ($costi as $spesa) {
				$importo = floatval($spesa['importo'] ?? 0);
				$cat     = $spesa['categoria'] ?? 'varie';
				$costo_totale += $importo;
				$costi_per_cat[$cat] = ($costi_per_cat[$cat] ?? 0) + $importo;
			}
		}
	}
}

// Integrazione EXIF Fotografico: aggiungiamo in coda all'array $map_data 
// i punti derivanti da TUTTE le foto del viaggio
if (class_exists('Travel_Diary_Exif')) {
	$exif_markers = Travel_Diary_Exif::get_trip_map_markers($trip_id); // rimosso il true
	if (!empty($exif_markers)) {
		foreach ($exif_markers as $em) {
			$map_data[] = array(
				'lat'   => $em['lat'],
				'lng'   => $em['lng'],
				'title' => 'Foto #'. $em['id'],
				'url'   => '',
				'type'  => 'photo',
				'thumb' => $em['thumb']
			);
		}
	}
}
?>

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

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'td-trip-single' ); ?>>

					<!-- Header del Viaggio -->
					<header class="entry-header" style="text-align: center; max-width: 800px; margin: 0 auto 48px;">
						<?php
						$terms = get_the_terms( get_the_ID(), 'td_trip_cat' );
						if ( $terms && ! is_wp_error( $terms ) ) :
						?>
							<div class="td-trip-categories">
								<?php foreach ( $terms as $term ) : ?>
									<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="td-breadcrumb">
										<?php echo esc_html( $term->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
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

								<!-- Lista Tappe -->
								<?php if ($entries && count($entries) > 0) : ?>
									<h2 style="margin-top: 3rem; margin-bottom:1.5rem;">L'Itinerario</h2>
									<div class="td-trip-entries-list" style="display:flex; flex-direction:column; gap:16px;">
									<?php
									$count = 1;
									foreach ($entries as $entry_id) :
										$entry    = get_post($entry_id);
										if (!$entry || $entry->post_status !== 'publish') continue;
										$date     = get_field('field_entry_arrivo', $entry_id);
										$mezzo    = get_field('field_entry_mezzo_trasporto', $entry_id);
										$km_r     = get_field('field_entry_km_reali', $entry_id);
										$excerpt  = has_excerpt($entry_id) ? get_the_excerpt($entry_id) : wp_trim_words(strip_tags(get_post_field('post_content', $entry_id)), 20);
									?>
										<a href="<?php echo esc_url(get_permalink($entry_id) . $token_query); ?>" style="display:block; padding:20px; background:#252525; border:1px solid #333; border-radius:8px; text-decoration:none; color:inherit; transition:transform 0.2s, box-shadow 0.2s;">
											<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
												<h4 style="margin:0; font-size:1.2rem; color:#f5f0e8;"><span style="color:#d4943a; margin-right:8px;"><?php echo $count; ?>.</span> <?php echo esc_html($entry->post_title); ?></h4>
												<div style="font-size:0.85rem; color:#888; display:flex; gap:15px;">
													<?php if ($date)  echo '<span>📅 ' . esc_html($date) . '</span>'; ?>
													<?php if ($mezzo) echo '<span>' . esc_html($mezzo) . '</span>'; ?>
													<?php if ($km_r)  echo '<span>📍 ' . esc_html($km_r) . ' km</span>'; ?>
												</div>
											</div>
											<?php if ($excerpt) : ?>
												<p style="margin:12px 0 0 0; font-size:0.95rem; color:#aaa;"><?php echo esc_html($excerpt); ?></p>
											<?php endif; ?>
										</a>
									<?php $count++; endforeach; ?>
									</div>
								<?php endif; ?>

							</div>
						</div>

						<!-- Sidebar Sticky (Right) -->
						<aside class="td-entry-sidebar-col">
							<div class="td-sticky-box">
								<h3 class="td-sidebar-title">Riepilogo Viaggio</h3>
								
								<ul class="td-metrics-list">
									<li>
										<span class="td-metrics-icon">📍</span>
										<div class="td-metrics-val"><strong>Tappe:</strong> <?php echo count($entries ?: []); ?></div>
									</li>
									<?php if ($km_totali_reali > 0) : ?>
										<li>
											<span class="td-metrics-icon">🛣️</span>
											<div class="td-metrics-val"><strong>Km percorsi:</strong> <?php echo number_format($km_totali_reali, 0, ',', '.'); ?></div>
										</li>
									<?php elseif ($km_totali_stimati > 0) : ?>
										<li>
											<span class="td-metrics-icon">📐</span>
											<div class="td-metrics-val"><strong>Km stimati:</strong> <?php echo number_format($km_totali_stimati, 0, ',', '.'); ?></div>
										</li>
									<?php endif; ?>
									<?php if ($costo_totale > 0) : ?>
										<li>
											<span class="td-metrics-icon">💶</span>
											<div class="td-metrics-val"><strong style="color:var(--td-accent,#d4943a);">Spesa Totale:</strong> € <?php echo number_format($costo_totale, 2, ',', '.'); ?></div>
										</li>
									<?php endif; ?>
								</ul>

								<!-- Minimappa global spostata in basso -->

							</div>
						</aside>

					</div> <!-- Fine Layout a 2 Colonne -->

					<!-- Sezioni Larghe (Full Width) -->
					<div class="td-entry-footer-sections">
						
						<?php if (!empty($map_data)) : ?>
							<div id="td-trip-map" class="td-sidebar-minimap-placeholder" style="margin-top:0; height: 500px; background:#111; border-radius: 8px; margin-bottom:48px;">
								<!-- Leaflet verrà renderizzato qui -->
							</div>
							<script>var tdTripMapData = <?php echo json_encode($map_data); ?>;</script>
						<?php endif; ?>

						<?php 
						// Galleria fotografica del Viaggio
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
