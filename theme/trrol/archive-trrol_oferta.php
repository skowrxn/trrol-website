<?php
/**
 * Archiwum: oferty dla firm.
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
		<h1 class="h-page">Oferty dla firm</h1>
		<p class="lead lead--mt">Publikujemy tu zapytania ofertowe na prace remontowe i konserwacyjne w budynkach, którymi zarządzamy. Ofertę składasz przez formularz przy wybranym zleceniu.</p>

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
			<p class="disclaimer">Pełny zakres prac, wymagania i formularz składania ofert znajdują się na stronie każdego zlecenia.</p>
		<?php else : ?>
			<div class="empty">
				<span class="empty__icon"><?php echo trrol_icon( 'narzedzia' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Nie prowadzimy teraz naboru ofert</p>
				<p class="empty__text">Nowe zapytania ofertowe pojawiają się tutaj na bieżąco. Możesz też napisać do nas i przedstawić zakres swoich usług.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Napisz do nas</a>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
