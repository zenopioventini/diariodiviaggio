<?php get_header(); ?>

	<!-- Immagine hero -->
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'ddv-hero', array( 'class' => 'single-hero' ) ); ?>
	<?php endif; ?>

	<main id="main" class="site-main">
		<div class="container container--narrow">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<div class="entry-meta">
							<span><?php echo get_the_date(); ?></span>
							<?php if ( get_the_author() ) : ?>
								<span><?php _e( 'di', 'diario-di-viaggio' ); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>
							<?php endif; ?>
						</div>
					</header>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<footer class="entry-footer" style="padding-top: 32px; margin-top: 32px; border-top: 1px solid #2d2d2d;">
						<?php the_tags( '<div class="entry-tags text-muted" style="font-size:.82rem">', ', ', '</div>' ); ?>
					</footer>

				</article>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>

			<?php endwhile; ?>

		</div>
	</main>

<?php get_footer(); ?>
