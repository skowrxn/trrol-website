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
		'label'  => 'Sekretariat',
		'phones' => trrol_phones( array( 'tel_sekretariat' ) ),
		'email'  => trrol_opt( 'email' ),
		'text'   => trrol_opt_saved( 'opis_sekretariat' ),
	),
	array(
		'label'  => 'Kierownik administracji',
		'phones' => trrol_phones( array( 'tel_kierownik' ) ),
		'text'   => trrol_opt_saved( 'opis_kierownik' ),
	),
	array(
		'label'  => 'Dział administracji',
		'phones' => trrol_phones( array( 'tel_administracja', 'tel_administracja_2' ) ),
		'text'   => trrol_opt_saved( 'opis_administracja' ),
	),
	array(
		'label'  => 'Dział techniczny',
		'phones' => trrol_phones( array( 'tel_techniczny', 'tel_techniczny_2' ) ),
		'text'   => trrol_opt_saved( 'opis_techniczny' ),
	),
	array(
		'label'  => 'Dział windykacji',
		'phones' => trrol_phones( array( 'tel_windykacja' ) ),
		'text'   => trrol_opt_saved( 'opis_windykacja' ),
	),
);

$dzialy = array_values( array_filter( $dzialy, function ( $d ) { return ! empty( $d['phones'] ); } ) );
$awaria = trrol_awaria();
?>

<main>
	<section class="container section--first">
		<h1 class="h-page">Kontakt</h1>

		<div class="contact-cards">
			<?php foreach ( $dzialy as $dzial ) : ?>
				<div class="contact-card">
					<p class="contact-card__label"><?php echo esc_html( $dzial['label'] ); ?></p>
					<p class="contact-card__phones"><?php echo trrol_phone_links( $dzial['phones'], 'contact-card__phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php if ( ! empty( $dzial['faks'] ) ) : ?>
						<p class="contact-card__alt">faks <?php echo esc_html( $dzial['faks'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $dzial['email'] ) ) : ?>
						<a class="contact-card__mail" href="mailto:<?php echo esc_attr( $dzial['email'] ); ?>"><?php echo esc_html( $dzial['email'] ); ?></a>
					<?php endif; ?>
					<?php if ( '' !== trim( $dzial['text'] ) ) : ?>
						<p class="contact-card__text"><?php echo esc_html( $dzial['text'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<?php if ( $awaria ) : ?>
				<div class="contact-card contact-card--awaria" id="awarie">
					<p class="contact-card__label">Zgłaszanie awarii poza godzinami pracy biura</p>
					<p class="contact-card__phones"><?php echo trrol_phone_links( array( $awaria['tel'] ), 'contact-card__phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php if ( $awaria['osoba'] ) : ?>
						<p class="contact-card__alt">Dyżur pełni <?php echo esc_html( $awaria['osoba'] ); ?></p>
					<?php endif; ?>
					<?php if ( $awaria['kiedy'] ) : ?>
						<p class="contact-card__text"><?php echo esc_html( trrol_ucfirst( $awaria['kiedy'] ) ); ?>.</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
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
				<p class="emergency__text">Zalanie, brak prądu, uszkodzenie instalacji gazowej — awarię należy zgłosić telefonicznie, bez oczekiwania na odpowiedź mailową.</p>
			</div>
			<div class="emergency__nums">
				<div class="emergency__side">
					<p class="emergency__label">W godzinach pracy biura</p>
					<?php echo trrol_phone_links( trrol_phones( array( 'tel_techniczny', 'tel_administracja' ) ), 'emergency__phone', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<?php if ( $awaria ) : ?>
					<div class="emergency__side">
						<p class="emergency__label">Poza godzinami pracy biura</p>
						<a class="emergency__phone" href="<?php echo esc_attr( trrol_tel_href( $awaria['tel'] ) ); ?>"><?php echo esc_html( $awaria['tel'] ); ?></a>
						<?php if ( $awaria['osoba'] ) : ?>
							<p class="emergency__who">dyżur: <?php echo esc_html( $awaria['osoba'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
