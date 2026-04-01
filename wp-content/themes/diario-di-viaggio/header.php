<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

	<header id="masthead" class="site-header">
		<div class="container site-header__inner">

			<!-- Logo / Nome sito -->
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title" rel="home">
						<?php bloginfo( 'name' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<!-- Menu principale -->
			<nav class="main-nav" aria-label="<?php esc_attr_e( 'Menu Principale', 'diario-di-viaggio' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'menu_class'     => '',
					'fallback_cb'    => 'ddv_fallback_menu',
				) );
				?>
			</nav>

			<!-- Hamburger (mobile) -->
			<button class="nav-toggle" aria-label="<?php esc_attr_e( 'Apri menu', 'diario-di-viaggio' ); ?>" aria-expanded="false">
				<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
					<line x1="2" y1="6" x2="20" y2="6"/>
					<line x1="2" y1="12" x2="20" y2="12"/>
					<line x1="2" y1="18" x2="20" y2="18"/>
				</svg>
			</button>

		</div>
	</header><!-- #masthead -->

	<div id="content" class="site-content">
