<?php
/**
 * Strona: Kontakt.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	the_post();
}

$dzialy = array(
	array(
		'label' => 'Sekretariat',
		'tel'   => trrol_opt( 'tel_sekretariat' ),
		'faks'  => trrol_opt( 'faks' ),
		'email' => trrol_opt( 'email' ),
		'text'  => 'Korespondencja, umowy, sprawy ogólne i przekierowanie do właściwego działu.',
	),
	array(
		'label' => 'Dział administracji',
		'tel'   => trrol_opt( 'tel_administracja' ),
		'text'  => 'Zgłoszenia awarii i usterek, sprawy techniczne, wynajem wolnych lokali.',
	),
	array(
		'label' => 'Główna księgowa',
		'tel'   => trrol_opt( 'tel_ksiegowosc' ),
		'text'  => 'Naliczenia, saldo płatności, rozliczenia mediów i odczyty liczników.',
	),
	array(
		'label' => 'Dział windykacji',
		'tel'   => trrol_opt( 'tel_windykacja' ),
		'text'  => 'Zaległości czynszowe, wezwania do zapłaty i ustalanie warunków spłaty.',
	),
);
?>

<main>
	<section class="container section--first">
		<h1 class="h-page">Kontakt</h1>

		<div class="contact-cards">
			<?php foreach ( $dzialy as $dzial ) : ?>
				<div class="contact-card">
					<p class="contact-card__label"><?php echo esc_html( $dzial['label'] ); ?></p>
					<a class="contact-card__phone" href="<?php echo esc_attr( trrol_tel_href( $dzial['tel'] ) ); ?>"><?php echo esc_html( $dzial['tel'] ); ?></a>
					<?php if ( ! empty( $dzial['faks'] ) ) : ?>
						<p class="contact-card__alt">faks <?php echo esc_html( $dzial['faks'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $dzial['email'] ) ) : ?>
						<a class="contact-card__mail" href="mailto:<?php echo esc_attr( $dzial['email'] ); ?>"><?php echo esc_html( $dzial['email'] ); ?></a>
					<?php endif; ?>
					<p class="contact-card__text"><?php echo esc_html( $dzial['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="container section--tight">
		<div class="grid grid--2 grid--top">
			<?php get_template_part( 'template-parts/form', 'kontakt' ); ?>

			<div style="display:grid;gap:24px;align-content:start">
				<div class="form-card">
					<h2 class="h-32">Godziny otwarcia</h2>
					<?php trrol_hours_table( true ); ?>
					<div style="margin-top:28px;padding-top:24px;border-top:1px solid var(--border)">
						<p class="office__label">Adres biura</p>
						<p style="font-size:19px;font-weight:600;margin-top:8px;line-height:1.5">
							<?php echo esc_html( trrol_opt( 'ulica' ) ); ?><br><?php echo esc_html( trrol_opt( 'miasto' ) ); ?>
						</p>
						<p style="font-size:15px;color:var(--muted);margin-top:10px">
							NIP <?php echo esc_html( trrol_opt( 'nip' ) ); ?> · REGON <?php echo esc_html( trrol_opt( 'regon' ) ); ?>
						</p>
					</div>
				</div>

				<div class="map-embed">
					<iframe
						src="<?php echo esc_url( trrol_opt( 'mapa_embed' ) ); ?>"
						title="Mapa — biuro TRROL, <?php echo esc_attr( trrol_opt( 'ulica' ) ); ?>"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</section>

	<?php
	$page_content = get_the_content();
	if ( trim( wp_strip_all_tags( $page_content ) ) ) :
		?>
		<section class="container section--tight">
			<div class="entry__body" style="border-top:0;padding-top:0;margin-top:0;max-width:900px">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="container section--tight section--last">
		<div class="emergency">
			<div>
				<p class="emergency__title">Awaria zagrażająca bezpieczeństwu?</p>
				<p class="emergency__text">Zalanie, brak prądu, uszkodzenie instalacji gazowej — dzwoń do administracji niezwłocznie, nie czekaj na odpowiedź mailową.</p>
			</div>
			<div class="emergency__side">
				<p class="emergency__label">Dział administracji</p>
				<a class="emergency__phone" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_administracja' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?></a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
