<?php
/**
 * Archiwum: dokumenty do pobrania.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$docs     = trrol_documents();
?>

<main>
	<section class="container section--first section--last">
		<h1 class="h-page">Dokumenty do pobrania</h1>
		<p class="lead lead--mt">Druki, wnioski i informacje dla najemców lokali w budynkach zarządzanych przez <?php echo esc_html( rtrim( trrol_opt( 'firma' ), '.' ) ); ?>.</p>

		<?php if ( $docs ) : ?>
			<ul class="doc-list">
				<?php foreach ( $docs as $doc ) : ?>
					<li class="doc-item">
						<span class="doc-item__icon"><?php echo trrol_icon( 'dokument', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div class="doc-item__body">
							<h2 class="doc-item__name"><?php echo esc_html( $doc['name'] ); ?></h2>
							<?php if ( $doc['opis'] ) : ?>
								<p class="doc-item__opis"><?php echo esc_html( $doc['opis'] ); ?></p>
							<?php endif; ?>
							<p class="doc-item__meta"><?php echo esc_html( trrol_document_meta( $doc ) ); ?></p>
						</div>
						<a class="btn btn--primary btn--sm doc-item__btn" href="<?php echo esc_url( $doc['url'] ); ?>" download>
							<?php echo trrol_icon( 'pobierz', 18, '#FFFFFF' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span>Pobierz</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="empty">
				<span class="empty__icon"><?php echo trrol_icon( 'dokument' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Brak dokumentów do pobrania</p>
				<p class="empty__text">Dokumenty zostaną udostępnione wkrótce. W sprawie druków i wniosków prosimy o kontakt z biurem.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Kontakt</a>
				</div>
			</div>
		<?php endif; ?>

	</section>
</main>

<?php
get_footer();
