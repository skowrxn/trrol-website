<?php
/**
 * Obsługa formularzy: kontakt, zapytanie o lokal, oferta firmy.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Definicje formularzy.
 *
 * @return array<string,array<string,array>>
 */
function trrol_form_definitions() {
	return array(
		'kontakt' => array(
			'title'  => 'Wiadomość ze strony — formularz kontaktowy',
			'fields' => array(
				'imie'      => array( 'label' => 'Imię i nazwisko', 'required' => true, 'max' => 120 ),
				'telefon'   => array( 'label' => 'Telefon', 'required' => false, 'max' => 40 ),
				'email'     => array( 'label' => 'E-mail', 'required' => true, 'type' => 'email', 'max' => 160 ),
				'temat'     => array( 'label' => 'Temat', 'required' => true, 'max' => 120 ),
				'wiadomosc' => array( 'label' => 'Treść wiadomości', 'required' => true, 'type' => 'textarea', 'max' => 6000 ),
			),
		),
		'lokal' => array(
			'title'  => 'Zapytanie o wolny lokal',
			'fields' => array(
				'imie'      => array( 'label' => 'Imię i nazwisko', 'required' => true, 'max' => 120 ),
				'telefon'   => array( 'label' => 'Telefon', 'required' => false, 'max' => 40 ),
				'email'     => array( 'label' => 'E-mail', 'required' => true, 'type' => 'email', 'max' => 160 ),
				'lokal'     => array( 'label' => 'Dotyczy lokalu', 'required' => false, 'max' => 200 ),
				'wiadomosc' => array( 'label' => 'Treść wiadomości', 'required' => true, 'type' => 'textarea', 'max' => 6000 ),
			),
		),
		'oferta' => array(
			'title'  => 'Oferta na zlecenie remontowe',
			'fields' => array(
				'firma'    => array( 'label' => 'Nazwa firmy', 'required' => true, 'max' => 160 ),
				'nip'      => array( 'label' => 'NIP', 'required' => false, 'max' => 20 ),
				'osoba'    => array( 'label' => 'Osoba kontaktowa', 'required' => true, 'max' => 120 ),
				'telefon'  => array( 'label' => 'Telefon', 'required' => true, 'max' => 40 ),
				'email'    => array( 'label' => 'E-mail', 'required' => true, 'type' => 'email', 'max' => 160 ),
				'zlecenie' => array( 'label' => 'Zlecenie', 'required' => false, 'max' => 200 ),
				'kwota'    => array( 'label' => 'Proponowana kwota netto', 'required' => false, 'max' => 60 ),
				'termin'   => array( 'label' => 'Termin realizacji', 'required' => false, 'max' => 160 ),
				'uwagi'    => array( 'label' => 'Uwagi', 'required' => false, 'type' => 'textarea', 'max' => 6000 ),
			),
		),
	);
}

/**
 * Obsługa wysyłki formularza.
 */
function trrol_handle_form() {
	$type  = isset( $_POST['trrol_form'] ) ? sanitize_key( wp_unslash( $_POST['trrol_form'] ) ) : '';
	$forms = trrol_form_definitions();

	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! isset( $forms[ $type ] ) ) {
		trrol_form_redirect( $redirect, 'error', array( 'Nieznany formularz.' ), array() );
	}

	// Weryfikacja tokenu.
	if ( ! isset( $_POST['trrol_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trrol_nonce'] ) ), 'trrol_form_' . $type ) ) {
		trrol_form_redirect( $redirect, 'error', array( 'Formularz wygasł. Odśwież stronę i spróbuj ponownie.' ), array() );
	}

	// Pułapka na roboty.
	if ( ! empty( $_POST['trrol_hp'] ) ) {
		trrol_form_redirect( $redirect, 'ok', array(), array() );
	}

	// Formularz wypełniony zbyt szybko.
	$started = isset( $_POST['trrol_ts'] ) ? absint( $_POST['trrol_ts'] ) : 0;
	if ( $started && ( time() - $started ) < 3 ) {
		trrol_form_redirect( $redirect, 'error', array( 'Formularz został wysłany zbyt szybko. Spróbuj ponownie.' ), array() );
	}

	// Limit zgłoszeń z jednego adresu IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'brak';
	$key = 'trrol_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 6 ) {
		trrol_form_redirect( $redirect, 'error', array( 'Wysłano już kilka wiadomości z tego urządzenia. Spróbuj ponownie za godzinę lub zadzwoń do biura.' ), array() );
	}

	$errors = array();
	$values = array();

	foreach ( $forms[ $type ]['fields'] as $name => $field ) {
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';

		if ( isset( $field['type'] ) && 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( isset( $field['type'] ) && 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		$value = mb_substr( trim( $value ), 0, (int) $field['max'] );
		$values[ $name ] = $value;

		if ( ! empty( $field['required'] ) && '' === $value ) {
			$errors[] = sprintf( 'Uzupełnij pole „%s”.', $field['label'] );
		}
		if ( isset( $field['type'] ) && 'email' === $field['type'] && '' !== $value && ! is_email( $value ) ) {
			$errors[] = 'Podaj poprawny adres e-mail.';
		}
	}

	if ( empty( $_POST['zgoda'] ) ) {
		$errors[] = 'Zaznacz zgodę na przetwarzanie danych — bez niej nie możemy odpowiedzieć.';
	}

	// Załącznik (tylko formularz ofert).
	$attachment = '';
	if ( 'oferta' === $type && ! empty( $_FILES['zalacznik']['name'] ) ) {
		$upload = trrol_handle_offer_upload();
		if ( is_wp_error( $upload ) ) {
			$errors[] = $upload->get_error_message();
		} else {
			$attachment = $upload;
		}
	}

	if ( $errors ) {
		trrol_form_redirect( $redirect, 'error', $errors, $values );
	}

	// Wysyłka.
	$to      = trrol_opt( 'email_formularze', trrol_opt( 'email' ) );
	$subject = sprintf( '[%s] %s', get_bloginfo( 'name' ), $forms[ $type ]['title'] );

	$lines = array();
	foreach ( $forms[ $type ]['fields'] as $name => $field ) {
		if ( '' === $values[ $name ] ) {
			continue;
		}
		$lines[] = sprintf( '%s: %s', $field['label'], $values[ $name ] );
	}
	$source = $redirect;
	if ( '' === $source || '/' === substr( $source, 0, 1 ) ) {
		$source = home_url( $source ? $source : '/' );
	}

	$lines[] = '';
	$lines[] = '---';
	$lines[] = 'Wysłano ze strony: ' . esc_url_raw( $source );
	$lines[] = 'Data: ' . date_i18n( 'd.m.Y H:i' );
	if ( $attachment ) {
		$lines[] = 'Załącznik: ' . basename( $attachment );
	}

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $values['email'] ) && is_email( $values['email'] ) ) {
		$name_for_reply = ! empty( $values['imie'] ) ? $values['imie'] : ( ! empty( $values['osoba'] ) ? $values['osoba'] : $values['email'] );
		$headers[]      = sprintf( 'Reply-To: %s <%s>', $name_for_reply, $values['email'] );
	}

	$attachments = $attachment ? array( $attachment ) : array();
	$sent        = wp_mail( $to, $subject, implode( "\n", $lines ), $headers, $attachments );

	set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

	if ( ! $sent ) {
		trrol_form_redirect(
			$redirect,
			'error',
			array( 'Nie udało się wysłać wiadomości. Zadzwoń do nas: ' . trrol_opt( 'tel_sekretariat' ) . '.' ),
			$values
		);
	}

	trrol_form_redirect( $redirect, 'ok', array(), array() );
}
add_action( 'admin_post_nopriv_trrol_form', 'trrol_handle_form' );
add_action( 'admin_post_trrol_form', 'trrol_handle_form' );

/**
 * Przyjęcie załącznika PDF do oferty.
 *
 * @return string|WP_Error Ścieżka do pliku lub błąd.
 */
function trrol_handle_offer_upload() {
	$file = $_FILES['zalacznik'];

	if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== $file['error'] ) {
		return new WP_Error( 'upload', 'Nie udało się wczytać załącznika. Spróbuj ponownie lub wyślij ofertę mailem.' );
	}
	if ( $file['size'] > 8 * MB_IN_BYTES ) {
		return new WP_Error( 'upload', 'Załącznik jest za duży — maksymalny rozmiar to 8 MB.' );
	}

	$check = wp_check_filetype( $file['name'], array( 'pdf' => 'application/pdf' ) );
	if ( 'pdf' !== $check['ext'] ) {
		return new WP_Error( 'upload', 'Załącznik musi być plikiem PDF.' );
	}

	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'trrol-oferty';

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
		file_put_contents( $dir . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" );
		file_put_contents( $dir . '/index.php', "<?php // Cisza.\n" );
	}

	$target = trailingslashit( $dir ) . gmdate( 'Ymd-His' ) . '-' . wp_generate_password( 6, false ) . '.pdf';

	if ( ! move_uploaded_file( $file['tmp_name'], $target ) ) {
		return new WP_Error( 'upload', 'Nie udało się zapisać załącznika.' );
	}

	return $target;
}

/**
 * Przekierowanie po obsłudze formularza.
 *
 * @param string   $url    Adres powrotny.
 * @param string   $status ok|error.
 * @param string[] $errors Lista błędów.
 * @param array    $values Wprowadzone dane.
 */
function trrol_form_redirect( $url, $status, $errors = array(), $values = array() ) {
	$args = array( 'trrol_status' => $status );

	if ( 'error' === $status ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'trrol_form_' . $token, array( 'errors' => $errors, 'values' => $values ), 5 * MINUTE_IN_SECONDS );
		$args['trrol_token'] = $token;
	}

	$url = remove_query_arg( array( 'trrol_status', 'trrol_token' ), $url );
	wp_safe_redirect( add_query_arg( $args, $url ) . '#formularz' );
	exit;
}

/**
 * Dane zwrotne formularza (błędy i wpisane wartości).
 *
 * @return array{status:string,errors:string[],values:array}
 */
function trrol_form_feedback() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$cache = array( 'status' => '', 'errors' => array(), 'values' => array() );

	$status = isset( $_GET['trrol_status'] ) ? sanitize_key( wp_unslash( $_GET['trrol_status'] ) ) : '';
	if ( ! $status ) {
		return $cache;
	}
	$cache['status'] = $status;

	$token = isset( $_GET['trrol_token'] ) ? sanitize_text_field( wp_unslash( $_GET['trrol_token'] ) ) : '';
	if ( $token ) {
		$data = get_transient( 'trrol_form_' . $token );
		if ( is_array( $data ) ) {
			$cache['errors'] = isset( $data['errors'] ) ? (array) $data['errors'] : array();
			$cache['values'] = isset( $data['values'] ) ? (array) $data['values'] : array();
		}
	}

	return $cache;
}

/**
 * Zapamiętana wartość pola po nieudanej wysyłce.
 *
 * @param string $name    Nazwa pola.
 * @param string $default Wartość domyślna.
 * @return string
 */
function trrol_field_value( $name, $default = '' ) {
	$feedback = trrol_form_feedback();
	if ( isset( $feedback['values'][ $name ] ) ) {
		return (string) $feedback['values'][ $name ];
	}
	return $default;
}

/**
 * Komunikat nad formularzem.
 */
function trrol_form_messages() {
	$feedback = trrol_form_feedback();

	if ( 'ok' === $feedback['status'] ) {
		echo '<div class="form-msg form-msg--ok" role="status"><strong>Dziękujemy — wiadomość została wysłana.</strong><br>Odpowiemy w godzinach pracy biura. W pilnych sprawach zadzwoń: ' . esc_html( trrol_opt( 'tel_administracja' ) ) . '.</div>';
		return;
	}

	if ( 'error' === $feedback['status'] ) {
		echo '<div class="form-msg form-msg--err" role="alert"><strong>Wiadomość nie została wysłana.</strong>';
		if ( $feedback['errors'] ) {
			echo '<ul style="margin:8px 0 0;padding-left:18px">';
			foreach ( $feedback['errors'] as $error ) {
				echo '<li>' . esc_html( $error ) . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	}
}

/**
 * Pola ukryte wspólne dla wszystkich formularzy.
 *
 * @param string $type Typ formularza.
 */
function trrol_form_hidden_fields( $type ) {
	printf( '<input type="hidden" name="action" value="trrol_form">' );
	printf( '<input type="hidden" name="trrol_form" value="%s">', esc_attr( $type ) );
	printf( '<input type="hidden" name="trrol_ts" value="%d">', time() );
	wp_nonce_field( 'trrol_form_' . $type, 'trrol_nonce' );
	echo '<div class="hp-field" aria-hidden="true"><label>Nie wypełniaj tego pola<input type="text" name="trrol_hp" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Treść zgody RODO.
 *
 * @return string
 */
function trrol_consent_text() {
	$policy = get_privacy_policy_url();
	$link   = $policy ? sprintf( ' <a href="%s">polityką prywatności</a>', esc_url( $policy ) ) : ' polityką prywatności';
	return sprintf(
		'Wyrażam zgodę na przetwarzanie moich danych osobowych przez %s w celu obsługi zgłoszenia, zgodnie z%s.',
		esc_html( trrol_opt( 'firma' ) ),
		$link
	);
}
