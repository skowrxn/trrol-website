<?php
/**
 * Ustawienia serwisu (dane kontaktowe, godziny, mapa, regulaminy).
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wartości domyślne ustawień.
 *
 * @return array<string,string>
 */
function trrol_default_options() {
	return array(
		'firma'            => '„TRROL” Nieruchomości Sp. z o.o.',
		'ulica'            => 'ul. Śląska 80',
		'miasto'           => '41-100 Siemianowice Śl.',
		'nip'              => '6431746635',
		'regon'            => '241504484',
		'krs'              => '0000349526',
		'tel_sekretariat'  => '(32) 228-00-03',
		'tel_administracja'=> '(32) 228-00-03',
		'tel_ksiegowosc'   => '(32) 228-00-03',
		'tel_windykacja'   => '(32) 228-00-03',
		'faks'             => '(32) 220-45-69',
		'email'            => 'biuro@trrol.pl',
		'email_formularze' => 'biuro@trrol.pl',
		'rok_zalozenia'    => '2017',
		'godziny_pn'       => '7:00–17:00',
		'godziny_wt'       => '7:00–15:00',
		'godziny_sr'       => '7:00–15:00',
		'godziny_cz'       => '7:00–15:00',
		'godziny_pt'       => '7:00–13:00',
		'godziny_weekend'  => 'nieczynne',
		'mapa_embed'       => 'https://www.google.com/maps?q=' . rawurlencode( 'ul. Śląska 80, 41-100 Siemianowice Śląskie' ) . '&output=embed',
		'pdf_regulamin'    => '',
		'pdf_woda'         => '',
	);
}

/**
 * Pobierz ustawienie.
 *
 * @param string $key     Klucz.
 * @param string $default Wartość zastępcza.
 * @return string
 */
function trrol_opt( $key, $default = '' ) {
	$saved    = get_option( 'trrol_options', array() );
	$defaults = trrol_default_options();

	if ( isset( $saved[ $key ] ) && '' !== $saved[ $key ] ) {
		return (string) $saved[ $key ];
	}
	if ( '' !== $default ) {
		return $default;
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Telefon w formacie do atrybutu href="tel:".
 *
 * @param string $phone Numer.
 * @return string
 */
function trrol_tel_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', $phone );
	if ( $digits && '+' !== substr( $digits, 0, 1 ) && 0 === strpos( $digits, '32' ) ) {
		$digits = '+48' . $digits;
	}
	return 'tel:' . $digits;
}

/**
 * Grupy pól ustawień.
 *
 * @return array<string,array<string,array{label:string,type?:string,hint?:string}>>
 */
function trrol_settings_groups() {
	return array(
		'Dane spółki' => array(
			'firma'         => array( 'label' => 'Nazwa spółki' ),
			'ulica'         => array( 'label' => 'Ulica' ),
			'miasto'        => array( 'label' => 'Kod i miasto' ),
			'nip'           => array( 'label' => 'NIP' ),
			'regon'         => array( 'label' => 'REGON' ),
			'krs'           => array( 'label' => 'KRS' ),
			'rok_zalozenia' => array( 'label' => 'Działamy od roku' ),
		),
		'Kontakt' => array(
			'tel_sekretariat'   => array( 'label' => 'Telefon — sekretariat' ),
			'tel_administracja' => array( 'label' => 'Telefon — dział administracji' ),
			'tel_ksiegowosc'    => array( 'label' => 'Telefon — główna księgowa' ),
			'tel_windykacja'    => array( 'label' => 'Telefon — dział windykacji' ),
			'faks'              => array( 'label' => 'Faks' ),
			'email'             => array( 'label' => 'E-mail publiczny' ),
			'email_formularze'  => array( 'label' => 'E-mail dla formularzy', 'hint' => 'Na ten adres trafiają wiadomości z formularzy na stronie.' ),
		),
		'Godziny otwarcia' => array(
			'godziny_pn'      => array( 'label' => 'Poniedziałek' ),
			'godziny_wt'      => array( 'label' => 'Wtorek' ),
			'godziny_sr'      => array( 'label' => 'Środa' ),
			'godziny_cz'      => array( 'label' => 'Czwartek' ),
			'godziny_pt'      => array( 'label' => 'Piątek' ),
			'godziny_weekend' => array( 'label' => 'Sobota, niedziela' ),
		),
		'Mapa i dokumenty' => array(
			'mapa_embed'    => array( 'label' => 'Adres osadzenia mapy', 'type' => 'url', 'hint' => 'Google Maps → Udostępnij → Umieść mapę → skopiuj adres z atrybutu src.' ),
			'pdf_regulamin' => array( 'label' => 'PDF — Regulamin użytkowania lokali', 'type' => 'file', 'hint' => 'Wgraj plik w Multimediach i wklej tutaj jego adres.' ),
			'pdf_woda'      => array( 'label' => 'PDF — Rozliczanie wody i ścieków', 'type' => 'file' ),
		),
		'SEO i widoczność w Google' => array(
			'seo_opis_home'           => array( 'label' => 'Opis strony głównej', 'hint' => 'To zdanie widać w wynikach wyszukiwania pod tytułem. Najlepiej do 155 znaków.' ),
			'seo_google_verification' => array( 'label' => 'Kod weryfikacyjny Google', 'hint' => 'Sama wartość z atrybutu content znacznika google-site-verification (Search Console → Weryfikacja → znacznik HTML).' ),
			'seo_profil_google'       => array( 'label' => 'Wizytówka Google', 'type' => 'url', 'hint' => 'Adres profilu firmy w Mapach Google — łączy stronę z wizytówką.' ),
			'seo_profil_facebook'     => array( 'label' => 'Profil na Facebooku', 'type' => 'url' ),
			'seo_mapa_link'           => array( 'label' => 'Link do biura na mapie', 'type' => 'url', 'hint' => 'Zwykły link do lokalizacji biura w Mapach Google.' ),
			'geo_lat'                 => array( 'label' => 'Szerokość geograficzna biura', 'hint' => 'Opcjonalnie, np. 50.3275. W Mapach Google: prawy przycisk na pinezce → współrzędne.' ),
			'geo_lng'                 => array( 'label' => 'Długość geograficzna biura', 'hint' => 'Opcjonalnie, np. 19.0294.' ),
		),
	);
}

/**
 * Strona ustawień w kokpicie.
 */
function trrol_settings_menu() {
	add_menu_page(
		'Ustawienia TRROL',
		'Ustawienia TRROL',
		'manage_options',
		'trrol-settings',
		'trrol_settings_page',
		'dashicons-admin-generic',
		59
	);
}
add_action( 'admin_menu', 'trrol_settings_menu' );

/**
 * Rejestracja ustawień.
 */
function trrol_register_settings() {
	register_setting( 'trrol_settings_group', 'trrol_options', array(
		'sanitize_callback' => 'trrol_sanitize_options',
		'default'           => array(),
	) );
}
add_action( 'admin_init', 'trrol_register_settings' );

/**
 * Czyszczenie danych ustawień.
 *
 * @param mixed $input Dane z formularza.
 * @return array<string,string>
 */
function trrol_sanitize_options( $input ) {
	$out = array();
	if ( ! is_array( $input ) ) {
		return $out;
	}

	foreach ( trrol_settings_groups() as $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}
			$value = wp_unslash( $input[ $key ] );
			$type  = isset( $field['type'] ) ? $field['type'] : 'text';

			if ( 'url' === $type || 'file' === $type ) {
				$out[ $key ] = esc_url_raw( trim( $value ) );
			} elseif ( false !== strpos( $key, 'email' ) ) {
				$out[ $key ] = sanitize_email( trim( $value ) );
			} else {
				$out[ $key ] = sanitize_text_field( $value );
			}
		}
	}
	return $out;
}

/**
 * Widok strony ustawień.
 */
function trrol_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Ustawienia TRROL</h1>
		<p>Dane wpisane tutaj pojawiają się na całej stronie — w nagłówku, stopce, na stronie kontaktu i przy lokalach.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'trrol_settings_group' ); ?>
			<?php foreach ( trrol_settings_groups() as $group => $fields ) : ?>
				<h2><?php echo esc_html( $group ); ?></h2>
				<table class="form-table"><tbody>
				<?php foreach ( $fields as $key => $field ) : ?>
					<tr>
						<th scope="row"><label for="trrol-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<input
								type="text"
								id="trrol-<?php echo esc_attr( $key ); ?>"
								name="trrol_options[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( trrol_opt( $key ) ); ?>"
								class="regular-text<?php echo ( isset( $field['type'] ) && in_array( $field['type'], array( 'url', 'file' ), true ) ) ? ' large-text' : ''; ?>">
							<?php if ( ! empty( $field['hint'] ) ) : ?>
								<p class="description"><?php echo esc_html( $field['hint'] ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody></table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
