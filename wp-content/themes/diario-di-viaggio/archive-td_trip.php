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

<?php
get_footer();
