<?php
/**
 * Diario di Viaggio — functions.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DDV_VERSION', '1.0.0' );
define( 'DDV_DIR', get_template_directory() );
define( 'DDV_URI', get_template_directory_uri() );

// ─── Theme Setup ─────────────────────────────────────────────────────────────

function ddv_setup() {
	load_theme_textdomain( 'diario-di-viaggio', DDV_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	// Thumbnail sizes
	add_image_size( 'ddv-card',       600, 400, true );
	add_image_size( 'ddv-hero',      1920, 800, true );
	add_image_size( 'ddv-entry-map', 900,  500, true );

	register_nav_menus( array(
		'primary' => __( 'Menu Principale', 'diario-di-viaggio' ),
		'footer'  => __( 'Menu Footer', 'diario-di-viaggio' ),
	) );
}
add_action( 'after_setup_theme', 'ddv_setup' );

// ─── Content Width ────────────────────────────────────────────────────────────

function ddv_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ddv_content_width', 1200 );
}
add_action( 'after_setup_theme', 'ddv_content_width', 0 );

// ─── Enqueue Scripts & Styles ─────────────────────────────────────────────────

function ddv_enqueue_assets() {
	// Google Fonts: Playfair Display + Inter
	wp_enqueue_style(
		'ddv-google-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap',
		array(),
		null
	);

	// Main stylesheet
	wp_enqueue_style( 'ddv-style', DDV_URI . '/assets/css/main.css', array( 'ddv-google-fonts' ), DDV_VERSION );

	// Main JS
	wp_enqueue_script( 'ddv-main', DDV_URI . '/assets/js/main.js', array( 'jquery' ), DDV_VERSION, true );

	// Comments
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ddv_enqueue_assets' );

// ─── Widget Areas ─────────────────────────────────────────────────────────────

function ddv_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'diario-di-viaggio' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Aggiungi widget qui.', 'diario-di-viaggio' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'ddv_widgets_init' );

// ─── Template Functions ───────────────────────────────────────────────────────

require DDV_DIR . '/inc/template-functions.php';
