<?php
/**
 * Stopka serwisu.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trrol_reg_page = get_page_by_path( 'regulaminy' );
$trrol_reg_url  = $trrol_reg_page ? get_permalink( $trrol_reg_page ) : home_url( '/regulaminy/' );
$trrol_pdf      = trrol_opt( 'pdf_regulamin' );

$trrol_footer_cols = array(
	'Firma' => array(
		array( 'label' => 'O nas', 'url' => home_url( '/o-nas/' ) ),
		array( 'label' => 'Aktualności', 'url' => trrol_archive_url( 'post' ) ),
		array( 'label' => 'Kontakt', 'url' => home_url( '/kontakt/' ) ),
	),
	'Dla mieszkańców' => array(
		array( 'label' => 'Ogłoszenia', 'url' => trrol_archive_url( 'trrol_ogloszenie' ) ),
		array( 'label' => $trrol_pdf ? 'Regulamin używania lokali (PDF)' : 'Regulamin używania lokali', 'url' => $trrol_pdf ? $trrol_pdf : $trrol_reg_url ),
		array( 'label' => 'Zgłoś awarię', 'url' => home_url( '/kontakt/' ) ),
	),
	'Dla firm' => array(
		array( 'label' => 'Wolne lokale', 'url' => trrol_archive_url( 'trrol_lokal' ) ),
		array( 'label' => 'Oferty dla firm', 'url' => trrol_archive_url( 'trrol_oferta' ) ),
	),
);
?>
</div><!-- /#tresc -->

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div>
				<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( trrol_img( 'logo-white.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</a>
				<p class="site-footer__addr"><?php echo esc_html( trrol_opt( 'ulica' ) . ', ' . trrol_opt( 'miasto' ) ); ?></p>
			</div>

			<div class="footer-cols">
				<?php foreach ( $trrol_footer_cols as $title => $links ) : ?>
					<div class="footer-col">
						<h2 class="footer-col__title"><?php echo esc_html( $title ); ?></h2>
						<ul class="footer-col__links">
							<?php foreach ( $links as $link ) : ?>
								<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="site-footer__rule"></div>

		<div class="site-footer__bottom">
			<p class="site-footer__copy">
				<?php
				printf(
					'© %s %s · %s, %s · NIP %s · REGON %s',
					esc_html( date_i18n( 'Y' ) ),
					esc_html( trrol_opt( 'firma' ) ),
					esc_html( trrol_opt( 'ulica' ) ),
					esc_html( trrol_opt( 'miasto' ) ),
					esc_html( trrol_opt( 'nip' ) ),
					esc_html( trrol_opt( 'regon' ) )
				);
				?>
			</p>
			<p class="site-footer__legal">
				<?php
				$trrol_privacy = get_privacy_policy_url();
				if ( $trrol_privacy ) {
					printf( '<a href="%s">Polityka prywatności</a>', esc_url( $trrol_privacy ) );
				}
				?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
