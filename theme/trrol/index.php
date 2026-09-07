<?php
/**
 * Aktualności — lista wpisów bloga, a także szablon zapasowy.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( is_category() ) {
	$title = single_cat_title( '', false );
	$lead  = 'Wpisy w kategorii ' . $title . '.';
} elseif ( is_search() ) {
	$title = 'Wyniki wyszukiwania';
	$lead  = sprintf( 'Szukana fraza: „%s”.', get_search_query() );
} elseif ( is_archive() ) {
	$title = get_the_archive_title();
	$lead  = '';
} else {
	$title = 'Aktualności';
	$lead  = 'Co się dzieje w firmie i na budynkach, którymi zarządzamy — remonty, inwestycje i sprawy administracyjne.';
}
?>

<main>
	<section class="container section--first section--last">
		<h1 class="h-page"><?php echo esc_html( wp_strip_all_tags( $title ) ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="lead lead--mt"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>

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
				<p class="empty__title">Brak wpisów</p>
				<p class="empty__text">Nie znaleźliśmy nic w tym miejscu. Wróć na stronę główną albo napisz do nas.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/' ) ); ?>">Strona główna</a>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
