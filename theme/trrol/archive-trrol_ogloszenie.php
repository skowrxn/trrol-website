<?php
/**
 * Archiwum: ogłoszenia dla mieszkańców.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main>
	<section class="container section--first section--last">
		<h1 class="h-page">Ogłoszenia</h1>
		<p class="lead lead--mt">Bieżące komunikaty administracji dla mieszkańców — terminy odczytów, planowane prace w budynkach i sprawy porządkowe.</p>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid--auto-lg mt-44">
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
				<span class="empty__icon"><?php echo trrol_icon( 'dokument' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Brak bieżących ogłoszeń</p>
				<p class="empty__text">Nie mamy teraz komunikatów dla mieszkańców. W pilnych sprawach dzwoń do administracji: <?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?>.</p>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
