<?php
/**
 * Archiwum: wolne lokale.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$total = (int) $GLOBALS['wp_query']->found_posts;
?>

<main>
	<section class="container section--first">
		<h1 class="h-page">Wolne lokale</h1>
		<p class="lead lead--mt">Lokale zwalniają się nieregularnie, dlatego wykaz aktualizujemy na bieżąco. Warunki najmu ustalamy indywidualnie — w sprawie każdego lokalu prosimy o kontakt z administracją.</p>

		<div class="list-bar">
			<div class="list-bar__count">
				<span class="list-bar__title">Aktualnie w wykazie</span>
				<span class="list-bar__num">
					<?php
					if ( 0 === $total ) {
						echo 'brak pozycji';
					} elseif ( 1 === $total ) {
						echo '1 pozycja';
					} elseif ( $total < 5 ) {
						echo esc_html( $total ) . ' pozycje';
					} else {
						echo esc_html( $total ) . ' pozycji';
					}
					?>
				</span>
			</div>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid--auto-lg mt-32">
				<?php
				while ( have_posts() ) {
					the_post();
					trrol_render_card( get_post() );
				}
				?>
			</div>
			<?php trrol_pagination(); ?>
		<?php else : ?>
			<div class="empty">
				<span class="empty__icon"><?php echo trrol_icon( 'dom-pusty' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Obecnie nie dysponujemy wolnymi lokalami</p>
				<p class="empty__text">Zostaw swój kontakt, a poinformujemy Cię, gdy pojawi się nowa oferta.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="#formularz">Zostaw kontakt</a>
					<a class="btn btn--outline btn--sm" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_administracja' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?></a>
				</div>
			</div>
		<?php endif; ?>

		<?php trrol_legal_disclaimer(); ?>
	</section>

	<section class="container section--tight section--last">
		<?php get_template_part( 'template-parts/form', 'lokal' ); ?>
	</section>
</main>

<?php
get_footer();
