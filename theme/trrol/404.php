<?php
/**
 * Strona 404.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main>
	<section class="container">
		<div class="notfound">
			<p class="notfound__code">404</p>
			<h1 class="h-section" style="margin-top:12px">Nie znaleźliśmy tej strony</h1>
			<p class="notfound__text">Adres mógł się zmienić albo wpis został usunięty. Zajrzyj na stronę główną lub skorzystaj z odnośników poniżej.</p>
			<div class="notfound__actions">
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/' ) ); ?>">Strona główna</a>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( trrol_archive_url( 'trrol_lokal' ) ); ?>">Wolne lokale</a>
				<a class="btn btn--outline btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Kontakt</a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
