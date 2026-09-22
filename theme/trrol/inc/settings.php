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
		'tel_kierownik'    => '(32) 228-00-03',
		'tel_administracja'=> '(32) 228-00-03',
		'tel_administracja_2' => '',
		'tel_techniczny'   => '',
		'tel_techniczny_2' => '',
		'tel_windykacja'   => '(32) 228-00-03',
		'opis_sekretariat' => 'Korespondencja, umowy, sprawy ogólne i przekierowanie do właściwego działu.',
		'opis_kierownik' => 'Sprawy dotyczące zarządzania budynkami i najmu lokali.',
		'opis_administracja' => 'Bieżące sprawy najemców, czynsze, rozliczenia mediów i wynajem wolnych lokali.',
		'opis_techniczny' => 'Zgłoszenia awarii i usterek w godzinach pracy biura, sprawy techniczne budynków.',
		'opis_windykacja' => 'Zaległości czynszowe, wezwania do zapłaty i ustalanie warunków spłaty.',
		'awaria_tel'       => '510-141-114',
		'awaria_osoba'     => 'Przemysław Hrabia',
		'awaria_kiedy'     => 'od poniedziałku do piątku od godziny 15:00, w soboty, niedziele i święta całodobowo',
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
		'regulamin_nazwa_1' => '',
		'regulamin_pdf_1'   => '',
		'regulamin_nazwa_2' => '',
		'regulamin_pdf_2'   => '',
		'regulamin_nazwa_3' => '',
		'regulamin_pdf_3'   => '',
		'regulamin_nazwa_4' => '',
		'regulamin_pdf_4'   => '',
		'regulamin_nazwa_5' => '',
		'regulamin_pdf_5'   => '',
		'odpady_opis'      => 'Terminy wywozu odpadów komunalnych i segregowanych dla budynków w zarządzie.',
		'odpady_nazwa_1'   => '',
		'odpady_pdf_1'     => '',
		'odpady_nazwa_2'   => '',
		'odpady_pdf_2'     => '',
		'odpady_nazwa_3'   => '',
		'odpady_pdf_3'     => '',
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
 * Ustawienie w postaci zapisanej w kokpicie.
 *
 * W odróżnieniu od trrol_opt() nie podstawia wartości domyślnej, gdy pole
 * zostało celowo wyczyszczone — np. usunięty opis działu ma zniknąć ze strony.
 *
 * @param string $key Klucz.
 * @return string
 */
function trrol_opt_saved( $key ) {
	$saved = get_option( 'trrol_options', array() );
	if ( is_array( $saved ) && array_key_exists( $key, $saved ) ) {
		return (string) $saved[ $key ];
	}
	$defaults = trrol_default_options();
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
			'tel_sekretariat'     => array( 'label' => 'Telefon — sekretariat' ),
			'opis_sekretariat'    => array( 'label' => 'Opis — sekretariat', 'type' => 'textarea', 'hint' => 'Tekst wyświetlany na stronie Kontakt pod numerem telefonu.' ),
			'tel_kierownik'       => array( 'label' => 'Telefon — kierownik administracji' ),
			'opis_kierownik'      => array( 'label' => 'Opis — kierownik administracji', 'type' => 'textarea', 'hint' => 'Tekst wyświetlany na stronie Kontakt pod numerem telefonu.' ),
			'tel_administracja'   => array( 'label' => 'Telefon — dział administracji' ),
			'tel_administracja_2' => array( 'label' => 'Telefon — dział administracji (drugi numer)', 'hint' => 'Opcjonalnie. Puste pole nie jest wyświetlane na stronie.' ),
			'opis_administracja'  => array( 'label' => 'Opis — dział administracji', 'type' => 'textarea', 'hint' => 'Tekst wyświetlany na stronie Kontakt pod numerem telefonu.' ),
			'tel_techniczny'      => array( 'label' => 'Telefon — dział techniczny' ),
			'tel_techniczny_2'    => array( 'label' => 'Telefon — dział techniczny (drugi numer)', 'hint' => 'Opcjonalnie. Puste pole nie jest wyświetlane na stronie.' ),
			'opis_techniczny'     => array( 'label' => 'Opis — dział techniczny', 'type' => 'textarea', 'hint' => 'Tekst wyświetlany na stronie Kontakt pod numerem telefonu.' ),
			'tel_windykacja'      => array( 'label' => 'Telefon — dział windykacji' ),
			'opis_windykacja'     => array( 'label' => 'Opis — dział windykacji', 'type' => 'textarea', 'hint' => 'Tekst wyświetlany na stronie Kontakt pod numerem telefonu.' ),
			'email'               => array( 'label' => 'E-mail publiczny' ),
			'email_formularze'    => array( 'label' => 'E-mail dla formularzy', 'hint' => 'Na ten adres trafiają wiadomości z formularzy na stronie.' ),
		),
		'Zgłaszanie awarii poza godzinami pracy biura' => array(
			'awaria_tel'   => array( 'label' => 'Telefon dyżurny', 'hint' => 'Numer wyświetlany na stronie kontaktowej, przy godzinach otwarcia i w stopce.' ),
			'awaria_osoba' => array( 'label' => 'Osoba pełniąca dyżur', 'hint' => 'Opcjonalnie.' ),
			'awaria_kiedy' => array( 'label' => 'Kiedy obowiązuje dyżur', 'hint' => 'Np. od poniedziałku do piątku od godziny 15:00, w soboty, niedziele i święta całodobowo.' ),
		),
		'Godziny otwarcia' => array(
			'godziny_pn'      => array( 'label' => 'Poniedziałek' ),
			'godziny_wt'      => array( 'label' => 'Wtorek' ),
			'godziny_sr'      => array( 'label' => 'Środa' ),
			'godziny_cz'      => array( 'label' => 'Czwartek' ),
			'godziny_pt'      => array( 'label' => 'Piątek' ),
			'godziny_weekend' => array( 'label' => 'Sobota, niedziela' ),
		),
		'Mapa i regulaminy' => array(
			'mapa_embed'    => array( 'label' => 'Adres osadzenia mapy', 'type' => 'url', 'hint' => 'Używane tylko wtedy, gdy pole „Wizytówka Google” jest puste. Gdy link do wizytówki jest podany, mapy na stronie pokazują pinezkę wizytówki.' ),
			'pdf_regulamin' => array( 'label' => 'PDF — Regulamin użytkowania lokali i porządku domowego', 'type' => 'file', 'hint' => 'Plik pobierany ze strony głównej i ze strony „Regulaminy”. Przycisk „Wybierz plik” otwiera bibliotekę mediów — można w niej też wgrać nowy plik.' ),
			'pdf_woda'      => array( 'label' => 'PDF — Rozliczanie wody i ścieków', 'type' => 'file' ),
			'regulamin_nazwa_1' => array( 'label' => 'Kolejny regulamin 1 — nazwa', 'hint' => 'Nazwa wyświetlana na stronie, np. Regulamin korzystania z placu zabaw. Regulamin pojawia się na stronie dopiero po wybraniu pliku PDF.' ),
			'regulamin_pdf_1'   => array( 'label' => 'Kolejny regulamin 1 — PDF', 'type' => 'file' ),
			'regulamin_nazwa_2' => array( 'label' => 'Kolejny regulamin 2 — nazwa' ),
			'regulamin_pdf_2'   => array( 'label' => 'Kolejny regulamin 2 — PDF', 'type' => 'file' ),
			'regulamin_nazwa_3' => array( 'label' => 'Kolejny regulamin 3 — nazwa' ),
			'regulamin_pdf_3'   => array( 'label' => 'Kolejny regulamin 3 — PDF', 'type' => 'file' ),
			'regulamin_nazwa_4' => array( 'label' => 'Kolejny regulamin 4 — nazwa' ),
			'regulamin_pdf_4'   => array( 'label' => 'Kolejny regulamin 4 — PDF', 'type' => 'file' ),
			'regulamin_nazwa_5' => array( 'label' => 'Kolejny regulamin 5 — nazwa' ),
			'regulamin_pdf_5'   => array( 'label' => 'Kolejny regulamin 5 — PDF', 'type' => 'file' ),
		),
		'Harmonogram odbioru odpadów' => array(
			'odpady_opis'    => array( 'label' => 'Opis', 'type' => 'textarea', 'hint' => 'Tekst w ramce „Harmonogram odbioru odpadów” na stronie głównej, pod ramką „Regulaminy”.' ),
			'odpady_nazwa_1' => array( 'label' => 'Plik 1 — nazwa', 'hint' => 'Np. Harmonogram odbioru odpadów 2026 lub nazwa ulicy/rejonu. Puste pole — wyświetlana jest nazwa „Harmonogram odbioru odpadów”.' ),
			'odpady_pdf_1'   => array( 'label' => 'Plik 1 — PDF', 'type' => 'file' ),
			'odpady_nazwa_2' => array( 'label' => 'Plik 2 — nazwa', 'hint' => 'Opcjonalnie, np. gdy harmonogramy różnią się dla poszczególnych budynków.' ),
			'odpady_pdf_2'   => array( 'label' => 'Plik 2 — PDF', 'type' => 'file' ),
			'odpady_nazwa_3' => array( 'label' => 'Plik 3 — nazwa', 'hint' => 'Opcjonalnie.' ),
			'odpady_pdf_3'   => array( 'label' => 'Plik 3 — PDF', 'type' => 'file' ),
		),
		'SEO i widoczność w Google' => array(
			'seo_opis_home'           => array( 'label' => 'Opis strony głównej', 'hint' => 'To zdanie widać w wynikach wyszukiwania pod tytułem. Najlepiej do 155 znaków.' ),
			'seo_google_verification' => array( 'label' => 'Kod weryfikacyjny Google', 'hint' => 'Sama wartość z atrybutu content znacznika google-site-verification (Search Console → Weryfikacja → znacznik HTML).' ),
			'seo_profil_google'       => array( 'label' => 'Wizytówka Google', 'type' => 'url', 'hint' => 'Link do wizytówki (Mapy Google → wizytówka firmy → Udostępnij → Kopiuj link). Mapy na stronie pokazują wtedy tę wizytówkę, a przy mapach pojawia się link „Zobacz w Mapach Google”.' ),
			'seo_profil_facebook'     => array( 'label' => 'Profil na Facebooku', 'type' => 'url' ),
			'seo_mapa_link'           => array( 'label' => 'Link do biura na mapie', 'type' => 'url', 'hint' => 'Zwykły link do lokalizacji biura w Mapach Google.' ),
			'geo_lat'                 => array( 'label' => 'Szerokość geograficzna biura', 'hint' => 'Opcjonalnie. Puste pole — współrzędne pobierane są z wizytówki Google.' ),
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
 * Wybór plików PDF z biblioteki mediów na stronie ustawień.
 *
 * @param string $hook Identyfikator ekranu.
 */
function trrol_settings_assets( $hook ) {
	if ( 'toplevel_page_trrol-settings' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script( 'media-editor', <<<'JS'
jQuery(function ($) {
	$(document).on('click', '.trrol-pick-file', function (e) {
		e.preventDefault();
		var input = $('#' + $(this).data('target'));
		var frame = wp.media({ title: 'Wybierz plik', button: { text: 'Użyj tego pliku' }, multiple: false });
		frame.on('select', function () {
			input.val(frame.state().get('selection').first().toJSON().url);
		});
		frame.open();
	});
	$(document).on('click', '.trrol-clear-file', function (e) {
		e.preventDefault();
		$('#' + $(this).data('target')).val('');
	});
});
JS
	);
}
add_action( 'admin_enqueue_scripts', 'trrol_settings_assets' );

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

			if ( 'textarea' === $type ) {
				$out[ $key ] = sanitize_textarea_field( $value );
			} elseif ( 'url' === $type || 'file' === $type ) {
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
							<?php if ( isset( $field['type'] ) && 'textarea' === $field['type'] ) : ?>
								<textarea
									id="trrol-<?php echo esc_attr( $key ); ?>"
									name="trrol_options[<?php echo esc_attr( $key ); ?>]"
									rows="2"
									class="large-text"><?php echo esc_textarea( trrol_opt_saved( $key ) ); ?></textarea>
							<?php else : ?>
								<input
									type="text"
									id="trrol-<?php echo esc_attr( $key ); ?>"
									name="trrol_options[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( trrol_opt_saved( $key ) ); ?>"
									class="regular-text<?php echo ( isset( $field['type'] ) && in_array( $field['type'], array( 'url', 'file' ), true ) ) ? ' large-text' : ''; ?>">
							<?php endif; ?>
							<?php if ( isset( $field['type'] ) && 'file' === $field['type'] ) : ?>
								<p style="margin-top:6px">
									<button type="button" class="button trrol-pick-file" data-target="trrol-<?php echo esc_attr( $key ); ?>">Wybierz plik</button>
									<button type="button" class="button-link trrol-clear-file" data-target="trrol-<?php echo esc_attr( $key ); ?>" style="margin-left:8px">Usuń</button>
								</p>
							<?php endif; ?>
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
