<?php
/**
 * Wspólny szablon wpisu — aktualności, ogłoszenia, wolne lokale, oferty dla firm.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_obj = get_post();
$type     = $post_obj->post_type;

$archive_url   = trrol_archive_url( $type );
$archive_label = trrol_archive_label( $type );

$powierzchnia = get_post_meta( $post_obj->ID, '_trrol_powierzchnia', true );
$uklad        = get_post_meta( $post_obj->ID, '_trrol_uklad', true );
$budynek      = get_post_meta( $post_obj->ID, '_trrol_budynek', true );
$rodzaj       = get_post_meta( $post_obj->ID, '_trrol_rodzaj', true );
$dostepnosc   = get_post_meta( $post_obj->ID, '_trrol_dostepnosc', true );
$numer        = get_post_meta( $post_obj->ID, '_trrol_numer', true );
$termin       = get_post_meta( $post_obj->ID, '_trrol_termin', true );
$wazne_do     = get_post_meta( $post_obj->ID, '_trrol_wazne_do', true );

$rodzaj_label = array( 'mieszkalny' => 'Lokal mieszkalny', 'uzytkowy' => 'Lokal użytkowy' );

/* Dane wpisu w kolumnie bocznej. */
$rows = array();
if ( 'trrol_lokal' === $type ) {
	$rows['Kategoria'] = isset( $rodzaj_label[ $rodzaj ] ) ? $rodzaj_label[ $rodzaj ] : 'Wolne lokale';
	if ( $powierzchnia ) {
		$rows['Powierzchnia'] = $powierzchnia;
	}
	if ( $uklad ) {
		$rows['Układ'] = $uklad;
	}
	if ( $dostepnosc ) {
		$rows['Dostępność'] = $dostepnosc;
	}
} elseif ( 'trrol_oferta' === $type ) {
	$rows['Kategoria'] = 'Oferty dla firm';
	if ( $numer ) {
		$rows['Nr zlecenia'] = $numer;
	}
	if ( $termin ) {
		$rows['Termin ofert'] = date_i18n( 'd.m.Y', strtotime( $termin ) );
	}
	$rows['Status'] = trrol_oferta_is_open( $post_obj ) ? 'Otwarty nabór' : 'Nabór zakończony';
} else {
	$rows['Kategoria'] = trrol_entry_label( $post_obj );
	if ( $wazne_do ) {
		$rows['Ważne do'] = date_i18n( 'd.m.Y', strtotime( $wazne_do ) );
	}
}
$rows['Publikacja'] = get_the_date( 'd.m.Y' );
if ( $budynek ) {
	$rows['Dotyczy'] = $budynek;
}

$related = trrol_related_posts( $post_obj, 2 );
?>

<main>
	<section class="container" style="padding-top:40px">
		<?php trrol_breadcrumbs(); ?>

		<div class="entry-layout">
			<article>
				<p class="entry__meta"><?php echo esc_html( trrol_entry_meta( $post_obj ) ); ?></p>
				<h1 class="entry__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="entry__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="entry__body">
					<?php the_content(); ?>

					<?php if ( 'trrol_lokal' === $type ) : ?>
						<div class="callout">
							<p class="callout__title">Pytania w tej sprawie</p>
							<p>Dział administracji, tel. <?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?>, <?php echo esc_html( trrol_opt( 'email' ) ); ?> — w godzinach pracy biura.</p>
						</div>
					<?php endif; ?>

					<?php if ( 'trrol_oferta' === $type && trrol_oferta_is_open( $post_obj ) ) : ?>
						<div class="callout">
							<p class="callout__title">Jak złożyć ofertę</p>
							<p>Wypełnij formularz pod treścią zlecenia<?php echo $termin ? ' — oferty przyjmujemy do ' . esc_html( date_i18n( 'd.m.Y', strtotime( $termin ) ) ) : ''; ?>. W razie pytań dzwoń: <?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?>.</p>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( 'trrol_lokal' === $type ) : ?>
					<h2 class="entry__section-title">Lokalizacja</h2>
					<div class="map-embed map-embed--tall">
						<iframe
							src="<?php echo esc_url( trrol_opt( 'mapa_embed' ) ); ?>"
							title="Mapa okolicy lokalu"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"
							allowfullscreen></iframe>
					</div>

					<div class="entry__note">
						<p>Prezentowane informacje mają charakter informacyjny i nie stanowią oferty w rozumieniu art. 66 § 1 Kodeksu cywilnego.</p>
					</div>
				<?php endif; ?>

				<div class="entry__foot">
					<a class="link-arrow" href="<?php echo esc_url( $archive_url ); ?>">← <?php echo esc_html( $archive_label ); ?></a>
					<span class="entry__updated">Ostatnia aktualizacja <?php echo esc_html( get_the_modified_date( 'd.m.Y' ) ); ?></span>
				</div>
			</article>

			<aside class="sidebar">
				<div class="panel">
					<h2 class="panel__title">Dane wpisu</h2>
					<div class="datalist">
						<?php foreach ( $rows as $key => $value ) : ?>
							<div class="datalist__row">
								<span class="datalist__k"><?php echo esc_html( $key ); ?></span>
								<span class="datalist__v"><?php echo esc_html( $value ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( in_array( $type, array( 'trrol_lokal', 'trrol_ogloszenie' ), true ) ) : ?>
					<div class="panel">
						<h2 class="panel__title"><?php echo 'trrol_lokal' === $type ? 'Zainteresowany?' : 'Masz pytanie?'; ?></h2>
						<p style="margin-top:10px;font-size:15px;line-height:1.6;color:var(--muted)">
							<?php echo 'trrol_lokal' === $type ? 'Napisz przez formularz poniżej albo zadzwoń do administracji.' : 'Skontaktuj się z administracją w godzinach pracy biura.'; ?>
						</p>
						<p style="margin-top:16px">
							<a class="office__value" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_administracja' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?></a>
							<a class="office__value" href="mailto:<?php echo esc_attr( trrol_opt( 'email' ) ); ?>"><?php echo esc_html( trrol_opt( 'email' ) ); ?></a>
						</p>
					</div>
				<?php endif; ?>

				<?php if ( $related ) : ?>
					<div>
						<h2 class="related-title">Powiązane wpisy</h2>
						<div class="related-list">
							<?php foreach ( $related as $rel ) : ?>
								<?php trrol_render_related_card( $rel ); ?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</section>

	<?php if ( 'trrol_lokal' === $type ) : ?>
		<section class="container" style="padding-top:88px">
			<?php get_template_part( 'template-parts/form', 'lokal' ); ?>
		</section>
	<?php endif; ?>

	<?php if ( 'trrol_oferta' === $type && trrol_oferta_is_open( $post_obj ) ) : ?>
		<section class="container" style="padding-top:88px">
			<?php get_template_part( 'template-parts/form', 'oferta' ); ?>
		</section>
	<?php endif; ?>

	<?php
	$more = get_posts( array(
		'post_type'      => $type,
		'posts_per_page' => 2,
		'post__not_in'   => array( $post_obj->ID ),
	) );
	?>
	<?php if ( $more ) : ?>
		<section class="container section section--last">
			<div class="section-head">
				<h2 class="h-32">Inne wpisy</h2>
				<a class="link-arrow" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( $archive_label ); ?> →</a>
			</div>
			<div class="grid grid--auto mt-32">
				<?php foreach ( $more as $m ) : ?>
					<?php trrol_render_card( $m ); ?>
				<?php endforeach; ?>
			</div>
		</section>
	<?php else : ?>
		<div class="section--last"></div>
	<?php endif; ?>
</main>
