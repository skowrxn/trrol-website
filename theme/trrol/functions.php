<?php
/**
 * TRROL Nieruchomości — funkcje motywu.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRROL_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/settings.php' );
require_once get_theme_file_path( 'inc/cpt.php' );
require_once get_theme_file_path( 'inc/template-tags.php' );
require_once get_theme_file_path( 'inc/forms.php' );
require_once get_theme_file_path( 'inc/seo.php' );

/**
 * Konfiguracja motywu.
 */
function trrol_setup() {
	load_theme_textdomain( 'trrol', get_theme_file_path( 'languages' ) );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 84,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	register_nav_menus( array(
		'primary'      => __( 'Menu główne', 'trrol' ),
		'footer_firma' => __( 'Stopka — Firma', 'trrol' ),
		'footer_mieszkancy' => __( 'Stopka — Dla mieszkańców', 'trrol' ),
		'footer_firmy' => __( 'Stopka — Dla firm', 'trrol' ),
	) );

	add_image_size( 'trrol-hero', 2000, 1100, true );
	add_image_size( 'trrol-wide', 1240, 720, true );
}
add_action( 'after_setup_theme', 'trrol_setup' );

/**
 * Style i skrypty.
 */
function trrol_assets() {
	wp_enqueue_style( 'trrol-fonts', get_theme_file_uri( 'assets/fonts/manrope.css' ), array(), TRROL_VERSION );
	wp_enqueue_style( 'trrol-main', get_theme_file_uri( 'assets/css/main.css' ), array( 'trrol-fonts' ), TRROL_VERSION );
	wp_enqueue_script( 'trrol-main', get_theme_file_uri( 'assets/js/main.js' ), array(), TRROL_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'trrol_assets' );

/**
 * Krój pisma hostujemy u siebie, więc ładujemy go możliwie wcześnie.
 */
function trrol_preload_font() {
	printf(
		'<link rel="preload" as="font" type="font/woff2" href="%s" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/manrope-latin.woff2' ) )
	);
	printf(
		'<link rel="preload" as="font" type="font/woff2" href="%s" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/manrope-latin-ext.woff2' ) )
	);
}
add_action( 'wp_head', 'trrol_preload_font', 1 );

/**
 * Długość skrótu wpisu.
 */
add_filter( 'excerpt_length', function () { return 32; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/**
 * Liczba wpisów na stronach archiwum.
 */
function trrol_archive_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'trrol_lokal', 'trrol_oferta', 'trrol_ogloszenie' ) ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'trrol_archive_per_page' );

/**
 * Oferty dla firm posortowane tak, aby otwarte nabory były na górze listy.
 *
 * Sortujemy po pobraniu wpisów, bo część zleceń może nie mieć ustawionego
 * pola statusu — zapytanie po meta_key pomijałoby takie wpisy.
 *
 * @param WP_Post[] $posts Wpisy.
 * @param WP_Query  $query Zapytanie.
 * @return WP_Post[]
 */
function trrol_sort_oferty( $posts, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'trrol_oferta' ) ) {
		return $posts;
	}

	usort( $posts, function ( $a, $b ) {
		$open_a = trrol_oferta_is_open( $a ) ? 0 : 1;
		$open_b = trrol_oferta_is_open( $b ) ? 0 : 1;
		if ( $open_a !== $open_b ) {
			return $open_a <=> $open_b;
		}
		return strcmp( $b->post_date, $a->post_date );
	} );

	return $posts;
}
add_filter( 'the_posts', 'trrol_sort_oferty', 10, 2 );

/**
 * Zlecenia do pokazania na stronie głównej — najpierw otwarte nabory.
 *
 * @param int $limit Liczba zleceń.
 * @return WP_Post[]
 */
function trrol_featured_oferty( $limit = 2 ) {
	$all = get_posts( array( 'post_type' => 'trrol_oferta', 'posts_per_page' => 12 ) );

	$open   = array_filter( $all, 'trrol_oferta_is_open' );
	$closed = array_filter( $all, function ( $post ) { return ! trrol_oferta_is_open( $post ); } );

	return array_slice( array_merge( array_values( $open ), array_values( $closed ) ), 0, $limit );
}

/**
 * Wyłącz komentarze w całym serwisie — to serwis informacyjny.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

function trrol_remove_comments_admin() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'trrol_remove_comments_admin' );

/**
 * Nadawca wiadomości z serwisu.
 */
add_filter( 'wp_mail_from', function ( $email ) {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$host = preg_replace( '/^www\./i', '', (string) $host );
	return 'no-reply@' . $host;
} );
add_filter( 'wp_mail_from_name', function () {
	return get_bloginfo( 'name' );
} );

/**
 * Drobne porządki w <head>.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

/**
 * Panel administratora — komunikat powitalny z instrukcją.
 */
function trrol_dashboard_widget() {
	wp_add_dashboard_widget( 'trrol_help', 'TRROL — jak zarządzać stroną', function () {
		$items = array(
			'Wolne lokale'    => array( 'edit.php?post_type=trrol_lokal', 'Wykaz wolnych lokali — adres, powierzchnia, układ. Bez cen; zainteresowani piszą przez formularz.' ),
			'Oferty dla firm' => array( 'edit.php?post_type=trrol_oferta', 'Zapytania ofertowe na prace remontowe. Ustaw numer zlecenia i termin składania ofert.' ),
			'Ogłoszenia'      => array( 'edit.php?post_type=trrol_ogloszenie', 'Bieżące komunikaty dla mieszkańców — deratyzacja, odczyty liczników, prace w budynku.' ),
			'Aktualności'     => array( 'edit.php', 'Blog firmowy — co się dzieje w firmie i na budynkach.' ),
			'Ustawienia TRROL'=> array( 'admin.php?page=trrol-settings', 'Telefony, e-mail, adres, godziny otwarcia, mapa i pliki regulaminów.' ),
		);
		echo '<p style="margin-top:0">Treści na stronie dodajesz w tych miejscach:</p><ul style="margin:0">';
		foreach ( $items as $label => $data ) {
			printf(
				'<li style="margin-bottom:10px"><a href="%s"><strong>%s</strong></a><br><span style="color:#666">%s</span></li>',
				esc_url( admin_url( $data[0] ) ),
				esc_html( $label ),
				esc_html( $data[1] )
			);
		}
		echo '</ul>';
	} );
}
add_action( 'wp_dashboard_setup', 'trrol_dashboard_widget' );

/**
 * Kolejność menu w kokpicie.
 */
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', function ( $menu ) {
	return array(
		'index.php',
		'edit.php?post_type=trrol_lokal',
		'edit.php?post_type=trrol_oferta',
		'edit.php?post_type=trrol_ogloszenie',
		'edit.php',
		'edit.php?post_type=page',
		'upload.php',
	);
} );
