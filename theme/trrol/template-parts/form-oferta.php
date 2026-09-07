<?php
/**
 * Formularz składania oferty na zlecenie remontowe.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$open_offers = get_posts( array(
	'post_type'      => 'trrol_oferta',
	'posts_per_page' => 20,
	'meta_query'     => array(
		'relation' => 'OR',
		array( 'key' => '_trrol_status', 'value' => 'zamkniete', 'compare' => '!=' ),
		array( 'key' => '_trrol_status', 'compare' => 'NOT EXISTS' ),
	),
) );

$open_offers = array_values( array_filter( $open_offers, 'trrol_oferta_is_open' ) );

$current       = is_singular( 'trrol_oferta' ) ? get_post() : null;
$current_numer = $current ? get_post_meta( $current->ID, '_trrol_numer', true ) : '';
$current_label = '';
if ( $current ) {
	$current_label = $current_numer ? $current_numer . ' — ' . get_the_title( $current ) : get_the_title( $current );
}
$deadline = $current ? get_post_meta( $current->ID, '_trrol_termin', true ) : '';

$heading = $current_numer ? sprintf( 'Złóż ofertę na zlecenie %s', $current_numer ) : 'Złóż ofertę';
?>
<h2 class="h-sub" id="formularz"><?php echo esc_html( $heading ); ?></h2>

<div class="form-card form-card--wide">
	<?php trrol_form_messages(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
		<?php trrol_form_hidden_fields( 'oferta' ); ?>

		<div class="form-row" style="gap:20px">
			<div class="field">
				<label class="field__label" for="of-firma">Nazwa firmy <span class="field__req">*</span></label>
				<input type="text" id="of-firma" name="firma" required autocomplete="organization" value="<?php echo esc_attr( trrol_field_value( 'firma' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-nip">NIP</label>
				<input type="text" id="of-nip" name="nip" inputmode="numeric" value="<?php echo esc_attr( trrol_field_value( 'nip' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-osoba">Osoba kontaktowa <span class="field__req">*</span></label>
				<input type="text" id="of-osoba" name="osoba" required autocomplete="name" value="<?php echo esc_attr( trrol_field_value( 'osoba' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-telefon">Telefon <span class="field__req">*</span></label>
				<input type="tel" id="of-telefon" name="telefon" required autocomplete="tel" value="<?php echo esc_attr( trrol_field_value( 'telefon' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-email">E-mail <span class="field__req">*</span></label>
				<input type="email" id="of-email" name="email" required autocomplete="email" value="<?php echo esc_attr( trrol_field_value( 'email' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-zlecenie">Wybór zlecenia</label>
				<?php if ( $open_offers ) : ?>
					<select id="of-zlecenie" name="zlecenie">
						<?php foreach ( $open_offers as $offer ) : ?>
							<?php
							$numer = get_post_meta( $offer->ID, '_trrol_numer', true );
							$label = $numer ? $numer . ' — ' . get_the_title( $offer ) : get_the_title( $offer );
							?>
							<option value="<?php echo esc_attr( $label ); ?>" <?php selected( trrol_field_value( 'zlecenie', $current_label ), $label ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<input type="text" id="of-zlecenie" name="zlecenie" value="<?php echo esc_attr( trrol_field_value( 'zlecenie', $current_label ) ); ?>">
				<?php endif; ?>
			</div>
			<div class="field">
				<label class="field__label" for="of-kwota">Proponowana kwota netto</label>
				<input type="text" id="of-kwota" name="kwota" placeholder="zł" value="<?php echo esc_attr( trrol_field_value( 'kwota' ) ); ?>">
			</div>
			<div class="field">
				<label class="field__label" for="of-termin">Termin realizacji</label>
				<input type="text" id="of-termin" name="termin" placeholder="np. 3 tygodnie od podpisania umowy" value="<?php echo esc_attr( trrol_field_value( 'termin' ) ); ?>">
			</div>
		</div>

		<div class="field field--file" style="margin-top:20px">
			<label class="field__label" for="of-plik">Załącz ofertę</label>
			<input type="file" id="of-plik" name="zalacznik" accept="application/pdf,.pdf">
			<p class="field__hint">Plik PDF, maksymalnie 8 MB.</p>
		</div>

		<div class="field" style="margin-top:20px">
			<label class="field__label" for="of-uwagi">Uwagi</label>
			<textarea id="of-uwagi" name="uwagi" rows="4"><?php echo esc_textarea( trrol_field_value( 'uwagi' ) ); ?></textarea>
		</div>

		<label class="consent" style="margin-top:20px">
			<input type="checkbox" name="zgoda" value="1" required>
			<span><?php echo wp_kses_post( trrol_consent_text() ); ?></span>
		</label>

		<div class="form-foot">
			<span class="form-foot__note">
				<?php if ( $deadline ) : ?>
					Oferty przyjmujemy do <?php echo esc_html( date_i18n( 'd.m.Y', strtotime( $deadline ) ) ); ?>. Odpowiadamy na wszystkie zgłoszenia po zamknięciu naboru.
				<?php else : ?>
					Odpowiadamy na wszystkie zgłoszenia po zamknięciu naboru.
				<?php endif; ?>
			</span>
			<button class="btn btn--primary" type="submit">Wyślij ofertę</button>
		</div>
	</form>
</div>
