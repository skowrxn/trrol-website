<?php
/**
 * Nagłówek serwisu.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#tresc">Przejdź do treści</a>

<header class="site-header">
	<div class="site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — strona główna">
			<?php
			if ( has_custom_logo() ) {
				$logo_id  = get_theme_mod( 'custom_logo' );
				$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
				printf(
					'<img src="%s" alt="%s" width="%d" height="%d">',
					esc_url( $logo_src[0] ),
					esc_attr( get_bloginfo( 'name' ) ),
					(int) $logo_src[1],
					(int) $logo_src[2]
				);
			} else {
				printf(
					'<img src="%s" alt="%s">',
					esc_url( trrol_img( 'logo-nav.png' ) ),
					esc_attr( get_bloginfo( 'name' ) )
				);
			}
			?>
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu-glowne" aria-label="Menu">
			<span></span><span></span><span></span>
		</button>

		<nav class="main-nav" id="menu-glowne" aria-label="Menu główne">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
			} else {
				$links = array(
					array( 'url' => home_url( '/' ), 'label' => 'Strona główna', 'current' => is_front_page() ),
					array( 'url' => trrol_archive_url( 'trrol_lokal' ), 'label' => 'Wolne lokale', 'current' => is_singular( 'trrol_lokal' ) || is_post_type_archive( 'trrol_lokal' ) ),
					array( 'url' => trrol_archive_url( 'trrol_oferta' ), 'label' => 'Oferty dla firm', 'current' => is_singular( 'trrol_oferta' ) || is_post_type_archive( 'trrol_oferta' ) ),
					array( 'url' => trrol_archive_url( 'trrol_ogloszenie' ), 'label' => 'Ogłoszenia', 'current' => is_singular( 'trrol_ogloszenie' ) || is_post_type_archive( 'trrol_ogloszenie' ) ),
					array( 'url' => trrol_archive_url( 'post' ), 'label' => 'Aktualności', 'current' => is_home() || is_singular( 'post' ) || is_category() ),
					array( 'url' => home_url( '/o-nas/' ), 'label' => 'O nas', 'current' => is_page( 'o-nas' ) ),
					array( 'url' => home_url( '/kontakt/' ), 'label' => 'Kontakt', 'current' => is_page( 'kontakt' ) ),
				);
				echo '<ul>';
				foreach ( $links as $link ) {
					printf(
						'<li class="%s"><a href="%s">%s</a></li>',
						$link['current'] ? 'is-current' : '',
						esc_url( $link['url'] ),
						esc_html( $link['label'] )
					);
				}
				echo '</ul>';
			}
			?>
		</nav>

		<div class="header-contact">
			<div>
				<span class="header-contact__label">Sekretariat</span>
				<a class="header-contact__phone" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_sekretariat' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_sekretariat' ) ); ?></a>
			</div>
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Skontaktuj się</a>
		</div>
	</div>
</header>

<div id="tresc">
