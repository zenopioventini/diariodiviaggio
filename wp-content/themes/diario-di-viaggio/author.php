<?php
/**
 * The template for displaying the author profile and their public trips.
 *
 * @package Diario_Di_Viaggio
 */

get_header(); 

// Dati dell'autore corrente
$author_id = get_queried_object_id();
$author_name = get_the_author_meta('display_name', $author_id);
$author_bio  = get_the_author_meta('description', $author_id);
$author_url  = get_the_author_meta('user_url', $author_id);
$author_avatar = get_avatar($author_id, 150, '', '', array('class' => 'td-author-avatar'));
?>

<section class="site-hero td-author-hero" style="min-height:350px; display:flex; align-items:center; justify-content:center; background:#1a1a1a; margin-bottom:40px; padding: 40px 20px;">
	<div class="site-hero__content container" style="text-align:center; max-width:800px;">
		<div class="td-author-hero__avatar" style="margin-bottom:20px;">
			<?php echo $author_avatar; ?>
		</div>
		<h1 class="site-hero__title" style="margin-bottom:10px; font-size:clamp(2rem, 4vw, 3rem); display:flex; align-items:center; justify-content:center; gap:10px;">
			<!-- Usiamo l'icona user dal nostro set SVG -->
			<?php if(class_exists('Travel_Diary_Icons')) echo Travel_Diary_Icons::get('user', ['width'=>36,'height'=>36]); ?>
			<?php echo esc_html($author_name); ?>
		</h1>
		
		<?php if ($author_bio) : ?>
			<div class="site-hero__subtitle" style="color:#bbb; font-size:1.1rem; line-height:1.6; margin-top:20px;">
				<?php echo wpautop(esc_html($author_bio)); ?>
			</div>
		<?php endif; ?>

		<?php if ($author_url) : ?>
			<div style="margin-top:20px;">
				<a href="<?php echo esc_url($author_url); ?>" target="_blank" rel="noopener noreferrer" style="color:var(--td-accent,#d4943a); text-decoration:none; font-weight:bold;">
					🌐 Visita il Sito Web
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="site-main">
	<div class="container container--wide">
		
		<div class="section-header" style="margin-bottom: 30px; text-align:center;">
			<h2>I Viaggi di <?php echo esc_html($author_name); ?></h2>
			<p style="color:#888;">Elenco degli itinerari pubblici condivisi da questo viaggiatore.</p>
		</div>

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
			<div style="text-align:center; padding: 60px 20px; background:#111; border-radius:8px; border:1px solid #333;">
				<h2 style="color:#666; font-size:3rem; margin-bottom:15px;">✈️</h2>
				<h3>Nessun viaggio pubblico</h3>
				<p style="color:#888;">Questo autore non ha ancora condiviso nessun viaggio pubblicamente.</p>
			</div>
		<?php endif; ?>
	</div>
</main>

<style>
.td-author-avatar img {
	border-radius: 50%;
	border: 4px solid #333;
	box-shadow: 0 5px 15px rgba(0,0,0,0.5);
	transition: border-color 0.3s;
}
.td-author-avatar img:hover {
	border-color: var(--td-accent, #d4943a);
}
</style>

<?php
get_footer();
