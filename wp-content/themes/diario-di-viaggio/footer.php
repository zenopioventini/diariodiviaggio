	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="container site-footer__inner">
			<span class="footer-site-title"><?php bloginfo( 'name' ); ?></span>
			<p class="footer-copyright">
				&copy; <?php echo date( 'Y' ); ?> &mdash; <?php bloginfo( 'description' ); ?>
			</p>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</div>
	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
