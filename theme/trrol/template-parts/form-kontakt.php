<?php
/**
 * Główny formularz kontaktowy.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tematy = array(
	'Wynajem lokalu',
	'Sprawa administracyjna',
	'Czynsze i rozliczenia',
	'Zgłoszenie awarii',
	'Współpraca — firmy remontowe',
	'Inne',
);
?>
<div class="form-card" id="formularz">
	<h2 class="h-32">Napisz do nas</h2>

	<div style="margin-top:28px">
		<?php trrol_form_messages(); ?>

		<form class="form-grid" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php trrol_form_hidden_fields( 'kontakt' ); ?>

			<div class="form-row">
				<div class="field">
					<label class="field__label" for="k-imie">Imię i nazwisko <span class="field__req">*</span></label>
					<input type="text" id="k-imie" name="imie" required autocomplete="name" value="<?php echo esc_attr( trrol_field_value( 'imie' ) ); ?>">
				</div>
				<div class="field">
					<label class="field__label" for="k-telefon">Telefon</label>
					<input type="tel" id="k-telefon" name="telefon" autocomplete="tel" value="<?php echo esc_attr( trrol_field_value( 'telefon' ) ); ?>">
				</div>
			</div>

			<div class="field">
				<label class="field__label" for="k-email">E-mail <span class="field__req">*</span></label>
				<input type="email" id="k-email" name="email" required autocomplete="email" value="<?php echo esc_attr( trrol_field_value( 'email' ) ); ?>">
			</div>

			<div class="field">
				<label class="field__label" for="k-temat">Temat <span class="field__req">*</span></label>
				<select id="k-temat" name="temat" required>
					<?php foreach ( $tematy as $temat ) : ?>
						<option value="<?php echo esc_attr( $temat ); ?>" <?php selected( trrol_field_value( 'temat' ), $temat ); ?>><?php echo esc_html( $temat ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="field">
				<label class="field__label" for="k-wiadomosc">Treść wiadomości <span class="field__req">*</span></label>
				<textarea id="k-wiadomosc" name="wiadomosc" rows="5" required><?php echo esc_textarea( trrol_field_value( 'wiadomosc' ) ); ?></textarea>
			</div>

			<label class="consent">
				<input type="checkbox" name="zgoda" value="1" required>
				<span><?php echo wp_kses_post( trrol_consent_text() ); ?></span>
			</label>

			<button class="btn btn--primary btn--block" type="submit">Wyślij wiadomość</button>
		</form>
	</div>
</div>
