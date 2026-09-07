<?php
/**
 * Formularz zapytania o wolny lokal.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lokal_title = is_singular( 'trrol_lokal' ) ? get_the_title() : '';
?>
<div class="form-split" id="formularz">
	<div class="form-split__aside">
		<h2 class="h-sub">Zapytanie o lokal</h2>
		<p>Wypełnij formularz albo zadzwoń do administracji. Skontaktujemy się, umówimy oględziny i omówimy warunki najmu.</p>
		<div class="form-split__contacts">
			<div>
				<span class="form-split__k">Dział administracji</span>
				<a class="form-split__v" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_administracja' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_administracja' ) ); ?></a>
			</div>
			<div>
				<span class="form-split__k">E-mail</span>
				<a class="form-split__v" href="mailto:<?php echo esc_attr( trrol_opt( 'email' ) ); ?>"><?php echo esc_html( trrol_opt( 'email' ) ); ?></a>
			</div>
		</div>
	</div>

	<div>
		<?php trrol_form_messages(); ?>

		<form class="form-grid" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php trrol_form_hidden_fields( 'lokal' ); ?>

			<div class="form-row">
				<div class="field">
					<label class="field__label" for="lokal-imie">Imię i nazwisko <span class="field__req">*</span></label>
					<input type="text" id="lokal-imie" name="imie" required autocomplete="name" value="<?php echo esc_attr( trrol_field_value( 'imie' ) ); ?>">
				</div>
				<div class="field">
					<label class="field__label" for="lokal-telefon">Telefon</label>
					<input type="tel" id="lokal-telefon" name="telefon" autocomplete="tel" value="<?php echo esc_attr( trrol_field_value( 'telefon' ) ); ?>">
				</div>
			</div>

			<div class="field">
				<label class="field__label" for="lokal-email">E-mail <span class="field__req">*</span></label>
				<input type="email" id="lokal-email" name="email" required autocomplete="email" value="<?php echo esc_attr( trrol_field_value( 'email' ) ); ?>">
			</div>

			<div class="field">
				<label class="field__label" for="lokal-lokal">Dotyczy lokalu</label>
				<input type="text" id="lokal-lokal" name="lokal" value="<?php echo esc_attr( trrol_field_value( 'lokal', $lokal_title ) ); ?>">
			</div>

			<div class="field">
				<label class="field__label" for="lokal-wiadomosc">Treść wiadomości <span class="field__req">*</span></label>
				<textarea id="lokal-wiadomosc" name="wiadomosc" rows="4" required><?php echo esc_textarea( trrol_field_value( 'wiadomosc' ) ); ?></textarea>
			</div>

			<label class="consent">
				<input type="checkbox" name="zgoda" value="1" required>
				<span><?php echo wp_kses_post( trrol_consent_text() ); ?></span>
			</label>

			<button class="btn btn--primary btn--block" type="submit">Wyślij zapytanie</button>
		</form>
	</div>
</div>
