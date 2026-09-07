<?php
/**
 * Funkcje pomocnicze szablonów.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ikony SVG używane w motywie.
 *
 * @param string $name Nazwa ikony.
 * @param int    $size Rozmiar.
 * @param string $color Kolor obrysu.
 * @return string
 */
function trrol_icon( $name, $size = 26, $color = '#2C5AA0' ) {
	$paths = array(
		'dom'        => '<path d="M4 20V9.5L12 4l8 5.5V20"></path><rect x="10" y="13" width="4" height="7"></rect>',
		'dom-pusty'  => '<path d="M4 20V9.5L12 4l8 5.5V20"></path><path d="M4 20h16"></path>',
		'lupa'       => '<circle cx="11" cy="11" r="6"></circle><path d="M15.5 15.5L21 21"></path>',
		'narzedzia'  => '<rect x="3" y="8" width="18" height="12" rx="1"></rect><path d="M9 8V5h6v3"></path><path d="M3 13h18"></path>',
		'dokument'   => '<rect x="5" y="3" width="14" height="18" rx="1"></rect><path d="M9 9h6M9 13h6M9 17h3"></path>',
		'osoby'      => '<circle cx="9" cy="8" r="3"></circle><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"></path><path d="M16 9h5M16 13h5"></path>',
		'nadzor'     => '<path d="M4 20V9.5L12 4l8 5.5V20"></path><circle cx="12" cy="13" r="2.5"></circle>',
		'remont'     => '<rect x="3" y="8" width="18" height="12" rx="1"></rect><path d="M9 8V5h6v3"></path>',
		'rozliczenia'=> '<rect x="4" y="4" width="16" height="16" rx="1"></rect><path d="M8 9h8M8 13h5M8 17h3"></path>',
		'wspolpraca' => '<circle cx="7" cy="7" r="3"></circle><circle cx="17" cy="17" r="3"></circle><path d="M10 7h7v7"></path>',
		'pobierz'    => '<path d="M12 4v11"></path><path d="M8 11l4 4 4-4"></path><path d="M5 19h14"></path>',
		'wyslij'     => '<path d="M12 17V5"></path><path d="M7 10l5-5 5 5"></path><path d="M4 19h16"></path>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.5" aria-hidden="true" focusable="false">%3$s</svg>',
		(int) $size,
		esc_attr( $color ),
		$paths[ $name ]
	);
}

/**
 * Etykieta kategorii wpisu (tekst po dacie).
 *
 * @param int|WP_Post|null $post Wpis.
 * @return string
 */
function trrol_entry_label( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	switch ( $post->post_type ) {
		case 'trrol_lokal':
			return 'Wolne lokale';
		case 'trrol_oferta':
			$numer = get_post_meta( $post->ID, '_trrol_numer', true );
			return $numer ? sprintf( 'Zlecenie %s · Oferty dla firm', $numer ) : 'Oferty dla firm';
		case 'trrol_ogloszenie':
			$etykieta = get_post_meta( $post->ID, '_trrol_kategoria', true );
			return $etykieta ? $etykieta : 'Ogłoszenia';
		default:
			$cats = get_the_category( $post->ID );
			return $cats ? $cats[0]->name : 'Aktualności';
	}
}

/**
 * Wiersz „data · kategoria”.
 *
 * @param int|WP_Post|null $post Wpis.
 * @return string
 */
function trrol_entry_meta( $post = null ) {
	$post  = get_post( $post );
	$label = trrol_entry_label( $post );
	$date  = get_the_date( 'd.m.Y', $post );

	if ( 'trrol_oferta' === $post->post_type ) {
		return $label;
	}
	return $label ? $date . ' · ' . $label : $date;
}

/**
 * Czy nabór ofert jest otwarty.
 *
 * @param int|WP_Post|null $post Wpis.
 * @return bool
 */
function trrol_oferta_is_open( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	if ( 'zamkniete' === get_post_meta( $post->ID, '_trrol_status', true ) ) {
		return false;
	}
	$termin = get_post_meta( $post->ID, '_trrol_termin', true );
	if ( $termin ) {
		$deadline = strtotime( $termin . ' 23:59:59' );
		if ( $deadline && $deadline < current_time( 'timestamp' ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Krótki opis wpisu na kartę.
 *
 * @param int|WP_Post|null $post  Wpis.
 * @param int              $words Liczba słów.
 * @return string
 */
function trrol_card_excerpt( $post = null, $words = 30 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( $post->post_excerpt ) {
		return wp_strip_all_tags( $post->post_excerpt );
	}
	return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), $words, '…' );
}

/**
 * Karta wpisu na listach.
 *
 * @param int|WP_Post|null $post Wpis.
 */
function trrol_render_card( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$is_oferta = 'trrol_oferta' === $post->post_type;
	$open      = $is_oferta ? trrol_oferta_is_open( $post ) : true;
	$classes   = 'card' . ( $is_oferta && ! $open ? ' card--closed' : '' );

	$foot_note = '';
	if ( $is_oferta ) {
		$termin = get_post_meta( $post->ID, '_trrol_termin', true );
		if ( $open && $termin ) {
			$foot_note = 'Oferty do ' . date_i18n( 'd.m.Y', strtotime( $termin ) );
		} elseif ( ! $open ) {
			$foot_note = $termin ? 'Nabór zakończony ' . date_i18n( 'd.m.Y', strtotime( $termin ) ) : 'Nabór zakończony';
		}
	}
	?>
	<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<span class="card__meta"><?php echo esc_html( trrol_entry_meta( $post ) ); ?></span>
		<span class="card__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
		<span class="card__excerpt"><?php echo esc_html( trrol_card_excerpt( $post ) ); ?></span>
		<span class="card__foot">
			<span>Czytaj całość →</span>
			<?php if ( $foot_note ) : ?>
				<span class="card__foot-note"><?php echo esc_html( $foot_note ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
}

/**
 * Mała karta powiązanego wpisu.
 *
 * @param int|WP_Post|null $post Wpis.
 */
function trrol_render_related_card( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}
	?>
	<a class="related-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
		<span class="related-card__meta"><?php echo esc_html( trrol_entry_meta( $post ) ); ?></span>
		<span class="related-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
		<span class="related-card__cta">Czytaj całość →</span>
	</a>
	<?php
}

/**
 * Okruszki nawigacyjne.
 *
 * Bez argumentu buduje ścieżkę z bieżącego widoku — tę samą, którą motyw
 * zgłasza wyszukiwarkom jako BreadcrumbList, więc obie wersje się nie rozjeżdżają.
 *
 * @param array<int,array{label:string,url?:string}>|null $items Własne elementy.
 */
function trrol_breadcrumbs( $items = null ) {
	if ( null === $items ) {
		$items = trrol_breadcrumb_items();
		array_shift( $items ); // „Strona główna" dodajemy poniżej.
		if ( $items ) {
			$items[ count( $items ) - 1 ]['url'] = '';
		}
	}

	echo '<nav class="breadcrumbs" aria-label="Ścieżka nawigacji">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">Strona główna</a>';
	foreach ( $items as $item ) {
		echo '<span aria-hidden="true">›</span>';
		if ( ! empty( $item['url'] ) ) {
			printf( '<a href="%s">%s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
		} else {
			printf( '<span aria-current="page">%s</span>', esc_html( $item['label'] ) );
		}
	}
	echo '</nav>';
}

/**
 * Tabela godzin otwarcia.
 *
 * @param bool $large Większy wariant.
 */
function trrol_hours_table( $large = false ) {
	$days = array(
		'Poniedziałek' => trrol_opt( 'godziny_pn' ),
		'Wtorek'       => trrol_opt( 'godziny_wt' ),
		'Środa'        => trrol_opt( 'godziny_sr' ),
		'Czwartek'     => trrol_opt( 'godziny_cz' ),
		'Piątek'       => trrol_opt( 'godziny_pt' ),
	);
	?>
	<div class="hours<?php echo $large ? ' hours--lg' : ''; ?>">
		<?php foreach ( $days as $day => $hours ) : ?>
			<div class="hours__row"><span><?php echo esc_html( $day ); ?></span><span><?php echo esc_html( $hours ); ?></span></div>
		<?php endforeach; ?>
		<div class="hours__row hours__row--off"><span>Sobota, niedziela</span><span><?php echo esc_html( trrol_opt( 'godziny_weekend' ) ); ?></span></div>
	</div>
	<?php
}

/**
 * Ciemna karta z regulaminami do pobrania.
 */
function trrol_docs_card() {
	$reg_page = get_page_by_path( 'regulaminy' );
	$reg_url  = $reg_page ? get_permalink( $reg_page ) : home_url( '/regulaminy/' );

	$docs = array(
		array(
			'name' => 'Regulamin użytkowania lokali',
			'url'  => trrol_opt( 'pdf_regulamin' ) ? trrol_opt( 'pdf_regulamin' ) : $reg_url . '#regulamin-porzadkowy',
			'pdf'  => (bool) trrol_opt( 'pdf_regulamin' ),
		),
		array(
			'name' => 'Rozliczanie wody i ścieków',
			'url'  => trrol_opt( 'pdf_woda' ) ? trrol_opt( 'pdf_woda' ) : $reg_url . '#regulamin-wody',
			'pdf'  => (bool) trrol_opt( 'pdf_woda' ),
		),
	);
	?>
	<div class="docs-card">
		<h2 class="docs-card__title">Regulamin używania lokali</h2>
		<p class="docs-card__text">Obowiązki najemcy, zasady korzystania z części wspólnych i zgłaszania awarii.</p>
		<div class="docs-card__list">
			<?php foreach ( $docs as $doc ) : ?>
				<a class="doc-link" href="<?php echo esc_url( $doc['url'] ); ?>"<?php echo $doc['pdf'] ? ' download' : ''; ?>>
					<?php echo trrol_icon( 'pobierz', 20, '#7FA5DC' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span>
						<span class="doc-link__name"><?php echo esc_html( $doc['name'] ); ?></span>
						<span class="doc-link__type"><?php echo $doc['pdf'] ? 'PDF do pobrania' : 'Czytaj na stronie'; ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Paginacja list.
 */
function trrol_pagination() {
	$links = paginate_links( array(
		'prev_text' => '←',
		'next_text' => '→',
		'type'      => 'array',
	) );

	if ( ! $links ) {
		return;
	}

	echo '<nav class="pagination" aria-label="Strony wyników">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}

/**
 * Adres archiwum wg typu.
 *
 * @param string $type Typ treści.
 * @return string
 */
function trrol_archive_url( $type ) {
	if ( 'post' === $type ) {
		$page = get_option( 'page_for_posts' );
		return $page ? get_permalink( $page ) : home_url( '/aktualnosci/' );
	}
	$url = get_post_type_archive_link( $type );
	return $url ? $url : home_url( '/' );
}

/**
 * Nazwa archiwum wg typu.
 *
 * @param string $type Typ treści.
 * @return string
 */
function trrol_archive_label( $type ) {
	$map = array(
		'trrol_lokal'      => 'Wolne lokale',
		'trrol_oferta'     => 'Oferty dla firm',
		'trrol_ogloszenie' => 'Ogłoszenia',
		'post'             => 'Aktualności',
	);
	return isset( $map[ $type ] ) ? $map[ $type ] : 'Wpisy';
}

/**
 * Powiązane wpisy — najpierw ten sam typ, potem pozostałe.
 *
 * @param WP_Post $post  Bieżący wpis.
 * @param int     $limit Liczba wpisów.
 * @return WP_Post[]
 */
function trrol_related_posts( $post, $limit = 2 ) {
	$same = get_posts( array(
		'post_type'      => $post->post_type,
		'posts_per_page' => $limit,
		'post__not_in'   => array( $post->ID ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( count( $same ) >= $limit ) {
		return $same;
	}

	$others = get_posts( array(
		'post_type'      => array( 'post', 'trrol_lokal', 'trrol_oferta', 'trrol_ogloszenie' ),
		'posts_per_page' => $limit - count( $same ),
		'post__not_in'   => array_merge( array( $post->ID ), wp_list_pluck( $same, 'ID' ) ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	return array_merge( $same, $others );
}

/**
 * Zestaw najczęstszych pytań na stronie głównej.
 *
 * @return array<int,array{q:string,a:string}>
 */
function trrol_faq_items() {
	$mail = trrol_opt( 'email' );
	$tel  = trrol_opt( 'tel_administracja' );

	return array(
		array(
			'q' => 'Jak zgłosić awarię w lokalu lub w częściach wspólnych?',
			'a' => sprintf( 'Awarie zgłaszaj telefonicznie do administracji w godzinach pracy biura, tel. %s, lub mailowo na %s. W przypadku awarii zagrażających bezpieczeństwu, np. zalania lub braku prądu, kontaktuj się z nami niezwłocznie.', $tel, $mail ),
		),
		array(
			'q' => 'Gdzie sprawdzę wysokość czynszu i saldo płatności?',
			'a' => sprintf( 'Informacje o naliczeniach i saldzie uzyskasz w dziale czynszów — telefonicznie pod numerem %s lub osobiście w naszym biurze (%s).', trrol_opt( 'tel_ksiegowosc' ), trrol_opt( 'ulica' ) ),
		),
		array(
			'q' => 'Jak przekazać stan licznika wody?',
			'a' => sprintf( 'Na koniec każdego miesiąca prześlij zdjęcie wodomierza na adres %s. Zdjęcie musi obejmować numer licznika i czytelny stan.', $mail ),
		),
		array(
			'q' => 'Jak wynająć lokal z Państwa oferty?',
			'a' => 'Wypełnij formularz przy wybranym lokalu lub zadzwoń do administracji. Skontaktujemy się i umówimy oględziny oraz omówimy warunki najmu.',
		),
		array(
			'q' => 'Dlaczego przy lokalach nie ma podanych cen?',
			'a' => 'Warunki najmu ustalamy indywidualnie, w zależności od stanu lokalu i długości umowy. Cenę podajemy przy bezpośrednim kontakcie.',
		),
		array(
			'q' => 'Jesteśmy firmą remontową — jak nawiązać współpracę?',
			'a' => 'Aktualne zapytania ofertowe publikujemy w zakładce Oferty dla firm. Ofertę składasz przez formularz przy wybranym zleceniu.',
		),
	);
}

/**
 * Zakres usług na stronie głównej.
 *
 * @return array<int,array{icon:string,title:string,text:string}>
 */
function trrol_services() {
	return array(
		array( 'icon' => 'dokument', 'title' => 'Bieżąca administracja', 'text' => 'Dokumentacja budynku, korespondencja, sprawy formalne i kontakt z instytucjami.' ),
		array( 'icon' => 'osoby', 'title' => 'Obsługa lokatorów', 'text' => 'Kontakt z najemcami, zgłoszenia, umowy najmu i sprawy codzienne mieszkańców.' ),
		array( 'icon' => 'nadzor', 'title' => 'Nadzór techniczny', 'text' => 'Kontrola stanu budynków, przeglądy okresowe i reagowanie na usterki.' ),
		array( 'icon' => 'remont', 'title' => 'Remonty i konserwacja', 'text' => 'Organizacja i koordynacja prac remontowych oraz bieżącej konserwacji.' ),
		array( 'icon' => 'rozliczenia', 'title' => 'Czynsze i media', 'text' => 'Naliczenia, rozliczenia mediów, odczyty liczników i obsługa płatności.' ),
		array( 'icon' => 'wspolpraca', 'title' => 'Współpraca z wykonawcami', 'text' => 'Wybór firm, zapytania ofertowe i nadzór nad realizacją zleconych prac.' ),
	);
}

/**
 * Zastrzeżenie prawne przy lokalach.
 */
function trrol_legal_disclaimer() {
	echo '<p class="disclaimer">Prezentowane informacje mają charakter informacyjny i nie stanowią oferty w rozumieniu art. 66 § 1 Kodeksu cywilnego.</p>';
}

/**
 * Adres obrazka motywu z podmianą na własny (jeśli ustawiony w Multimediach).
 *
 * @param string $file Nazwa pliku w assets/img.
 * @return string
 */
function trrol_img( $file ) {
	return get_theme_file_uri( 'assets/img/' . $file );
}
