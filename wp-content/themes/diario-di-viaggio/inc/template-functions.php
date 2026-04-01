<?php
/**
 * inc/template-functions.php
 * Helper usati nei template del tema
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fallback menu quando nessun menu è assegnato
 */
function ddv_fallback_menu() {
	echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Home', 'diario-di-viaggio' ) . '</a></li></ul>';
}

/**
 * Render di una card Viaggio
 * Chiamata dentro un loop WP_Query su td_trip
 */
function ddv_trip_card() {
	$post_id  = get_the_ID();
	$title    = get_the_title();
	$link     = get_permalink();
	$excerpt  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 );

	// Numero di tappe
	$entries = get_field( 'field_trip_' . 'entry_of_trip', $post_id );
	$n_tappe = is_array( $entries ) ? count( $entries ) : 0;

	// Categoria viaggio
	$terms    = get_the_terms( $post_id, 'td_trip_cat' );
	$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
	?>

	<article class="trip-card">
		<a href="<?php echo esc_url( $link ); ?>" tabindex="-1">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'ddv-card', array( 'class' => 'trip-card__thumb' ) ); ?>
			<?php else : ?>
				<div class="trip-card__thumb trip-card__thumb--placeholder">🗺️</div>
			<?php endif; ?>
		</a>

		<div class="trip-card__body">
			<h3 class="trip-card__title">
				<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>

			<div class="trip-card__meta">
				<?php if ( $cat_name ) : ?>
					<span><?php echo esc_html( $cat_name ); ?></span>
				<?php endif; ?>
				<?php if ( $n_tappe > 0 ) : ?>
					<span><?php printf( _n( '%d tappa', '%d tappe', $n_tappe, 'diario-di-viaggio' ), $n_tappe ); ?></span>
				<?php endif; ?>
				<span><?php echo get_the_date(); ?></span>
			</div>

			<?php if ( $excerpt ) : ?>
				<p class="trip-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
		</div>
	</article>

	<?php
}

/**
 * Corpo pagina: wrapper per contenuto principale
 */
function ddv_content_open() {
	echo '<div class="entry-content">';
}

function ddv_content_close() {
	echo '</div>';
}
