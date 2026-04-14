<?php
/**
 * Template Name: I Viaggiatori
 *
 * @package Diario_Di_Viaggio
 */

get_header(); ?>

<section class="site-hero" style="min-height:300px; display:flex; align-items:center; justify-content:center; background:#1a1a1a; margin-bottom:40px;">
	<div class="site-hero__content container" style="text-align:center;">
		<h1 class="site-hero__title" style="margin-bottom:10px; font-size:clamp(2.5rem, 5wv, 4rem);">
			👥 La Community
		</h1>
		<p class="site-hero__subtitle" style="color:#aaa;">
			Esplora i diari di tutti i viaggiatori che hanno condiviso le loro avventure.
		</p>
	</div>
</section>

<main id="main" class="site-main">
	<div class="container container--wide">
		
		<?php
		// Recupera tutti gli utenti che hanno pubblicato almeno un viaggio (td_trip)
		$users = get_users( array(
			'has_published_posts' => array( 'td_trip' ),
			'orderby'             => 'post_count',
			'order'               => 'DESC',
		) );
		?>

		<?php if ( ! empty( $users ) ) : ?>
			<div class="td-authors-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:30px;">
				
				<?php foreach ( $users as $user ) : 
					$author_url = get_author_posts_url( $user->ID );
					$author_bio = get_the_author_meta( 'description', $user->ID );
					$trip_count = count_user_posts( $user->ID, 'td_trip', true ); 
				?>
					<article class="td-author-card" style="background:#111; border:1px solid #333; border-radius:8px; overflow:hidden; text-align:center; padding:30px 20px; transition:transform 0.3s, border-color 0.3s;">
						<a href="<?php echo esc_url( $author_url ); ?>" style="display:block; text-decoration:none; color:inherit;">
							
							<div class="td-author-card__avatar" style="margin-bottom:20px;">
								<?php echo get_avatar( $user->ID, 120, '', '', array( 'style' => 'border-radius:50%; border:3px solid #d4943a;' ) ); ?>
							</div>
							
							<h3 class="td-author-card__name" style="margin:0 0 10px 0; color:#fff; font-size:1.4rem;">
								<?php echo esc_html( $user->display_name ); ?>
							</h3>
							
							<div class="td-author-card__stats" style="margin-bottom:15px; font-size:0.9rem; font-weight:bold; color:var(--td-accent,#d4943a); text-transform:uppercase; letter-spacing:0.05em;">
								✈️ <?php printf( _n( '%d Viaggio', '%d Viaggi', $trip_count, 'diario-di-viaggio' ), $trip_count ); ?>
							</div>
							
							<?php if ( $author_bio ) : ?>
								<p class="td-author-card__bio" style="font-size:0.9rem; color:#888; line-height:1.5; margin:0 auto; max-width:90%;">
									<?php echo wp_trim_words( esc_html( $author_bio ), 15, '...' ); ?>
								</p>
							<?php endif; ?>

						</a>
					</article>
				<?php endforeach; ?>
				
			</div>
		<?php else : ?>
			
			<div style="text-align:center; padding: 100px 20px;">
				<h2 style="color:#666; font-size:4rem; margin-bottom:20px;">🏜️</h2>
				<h3>Nessun Viaggiatore trovato.</h3>
				<p style="color:#888;">Nessun utente ha ancora pubblicato un viaggio pubblico.</p>
			</div>
			
		<?php endif; ?>
	</div>
</main>

<style>
.td-author-card:hover { transform: translateY(-5px); border-color: var(--td-accent,#d4943a); }
</style>

<?php
get_footer();
