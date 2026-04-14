<?php
/**
 * The template for displaying archive pages for the custom post type "td_trip"
 *
 * @package Diario_Di_Viaggio
 */

get_header(); ?>

<section class="site-hero" style="min-height:300px; display:flex; align-items:center; justify-content:center; background:#1a1a1a; margin-bottom:40px;">
	<div class="site-hero__content container" style="text-align:center;">
		<h1 class="site-hero__title" style="margin-bottom:10px; font-size:clamp(2.5rem, 5wv, 4rem);">
			<?php _e( 'I Miei Viaggi', 'diario-di-viaggio' ); ?>
		</h1>
		<p class="site-hero__subtitle" style="color:#aaa;">
			Esplora tutti gli itinerari condivisi pubblicamente.
		</p>
	</div>
</section>

<main id="main" class="site-main">
	<div class="container container--wide">

		<!-- Barra Ricerca -->
		<div class="td-search-bar" style="background:#222; padding:20px; border-radius:8px; margin-bottom:30px; display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
			<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex; width:100%; gap:15px; margin:0; flex-wrap:wrap;">
				<input type="hidden" name="post_type" value="td_trip">
				
				<div style="flex-grow:1; min-width:200px;">
					<input type="search" name="s" placeholder="Cerca itinerari, nazioni, descrizioni..." value="<?php echo get_search_query(); ?>" style="width:100%; padding:10px 15px; border-radius:4px; border:1px solid #444; background:#111; color:#fff; font-size:1rem;">
				</div>
				
				<div style="min-width:200px;">
					<?php 
					wp_dropdown_categories( array(
						'show_option_all' => 'Tutte le tipologie',
						'taxonomy'        => 'td_trip_cat',
						'name'            => 'td_trip_cat',
						'value_field'     => 'slug',
						'selected'        => isset( $_GET['td_trip_cat'] ) ? sanitize_text_field( $_GET['td_trip_cat'] ) : '',
						'class'           => 'td-filter-select',
					) ); 
					?>
				</div>
				
				<button type="submit" style="padding:10px 25px; background:var(--td-accent,#d4943a); color:#1a1a1a; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Cerca</button>
			</form>
		</div>

		<?php if ( is_search() ) : ?>
			<h2 style="margin-bottom:20px; font-size:1.5rem; color:#fff;">Risultati di ricerca per: "<?php echo get_search_query(); ?>"</h2>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<div class="trips-grid">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();
					ddv_trip_card();
				endwhile;
				?>
			</div>

			<div class="td-pagination" style="margin-top:40px; display:flex; justify-content:center; gap:20px;">
				<?php
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span> Precedenti',
					'next_text' => 'Successivi <span class="dashicons dashicons-arrow-right-alt2"></span>',
				) );
				?>
			</div>

		<?php else : ?>
			<div style="text-align:center; padding: 100px 20px;">
				<h2 style="color:#666; font-size:4rem; margin-bottom:20px;">🏜️</h2>
				<h3>Nessun viaggio pubblico trovato.</h3>
				<p style="color:#888;">Sembra che non ci siano ancora viaggi salvati con visibilità "Pubblica".</p>
			</div>
		<?php endif; ?>
	</div>
</main>

<style>
.td-filter-select {
	width: 100%;
	padding: 10px 15px;
	border-radius: 4px;
	border: 1px solid #444;
	background: #111;
	color: #fff;
	font-size: 1rem;
	appearance: auto;
}
</style>

<?php
get_footer();
