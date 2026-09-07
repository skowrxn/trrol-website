<?php
/**
 * SEO: tytuły, opisy, canonical, Open Graph, dane strukturalne, przekierowania.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * 1. Tytuły stron
 * ====================================================================== */

/**
 * Tytuł zakładki i wyniku wyszukiwania.
 *
 * @param array $parts Części tytułu.
 * @return array
 */
function trrol_document_title( $parts ) {
	$custom = trrol_entry_seo( 'title' );
	if ( $custom ) {
		return array( 'title' => $custom );
	}

	$brand = 'TRROL Nieruchomości';
	$city  = 'Siemianowice Śląskie';

	if ( is_front_page() ) {
		return array( 'title' => sprintf( 'Zarządzanie nieruchomościami %s — administracja kamienic | %s', $city, $brand ) );
	}

	if ( is_post_type_archive( 'trrol_lokal' ) ) {
		return array( 'title' => sprintf( 'Wolne lokale do wynajęcia — %s | %s', $city, $brand ) );
	}
	if ( is_post_type_archive( 'trrol_oferta' ) ) {
		return array( 'title' => sprintf( 'Oferty dla firm remontowych — zapytania ofertowe | %s', $brand ) );
	}
	if ( is_post_type_archive( 'trrol_ogloszenie' ) ) {
		return array( 'title' => sprintf( 'Ogłoszenia dla mieszkańców | %s', $brand ) );
	}
	if ( is_home() ) {
		return array( 'title' => sprintf( 'Aktualności — remonty i inwestycje | %s', $brand ) );
	}
	if ( is_page( 'kontakt' ) ) {
		return array( 'title' => sprintf( 'Kontakt — administracja nieruchomości %s | %s', $city, $brand ) );
	}
	if ( is_page( 'o-nas' ) ) {
		return array( 'title' => sprintf( 'O nas — zarządca nieruchomości prywatnych od 2017 r. | %s', $brand ) );
	}

	if ( isset( $parts['title'] ) ) {
		$parts['title'] = $parts['title'] . ' | ' . $brand;
	}
	unset( $parts['tagline'], $parts['site'] );

	return $parts;
}
add_filter( 'document_title_parts', 'trrol_document_title', 20 );

add_filter( 'document_title_separator', function () { return '|'; } );

/* =========================================================================
 * 2. Opisy
 * ====================================================================== */

/**
 * Opis strony (meta description).
 *
 * @return string
 */
function trrol_meta_description() {
	$custom = trrol_entry_seo( 'description' );
	if ( $custom ) {
		return $custom;
	}

	$city = 'Siemianowicach Śląskich';

	if ( is_front_page() ) {
		$default = sprintf(
			'Kompleksowa administracja i zarządzanie nieruchomościami prywatnymi w %s — kamienice, budynki mieszkalne, nadzór techniczny i obsługa lokatorów. Od %s roku. Tel. %s.',
			$city,
			trrol_opt( 'rok_zalozenia' ),
			trrol_opt( 'tel_sekretariat' )
		);
		return trrol_opt( 'seo_opis_home', $default );
	}

	if ( is_post_type_archive( 'trrol_lokal' ) ) {
		return sprintf( 'Aktualny wykaz wolnych lokali mieszkalnych i użytkowych w %s. Warunki najmu ustalamy indywidualnie — zadzwoń %s lub napisz przez formularz.', $city, trrol_opt( 'tel_administracja' ) );
	}
	if ( is_post_type_archive( 'trrol_oferta' ) ) {
		return 'Zapytania ofertowe na prace remontowe i konserwacyjne w budynkach, którymi zarządzamy. Złóż ofertę przez formularz przy wybranym zleceniu.';
	}
	if ( is_post_type_archive( 'trrol_ogloszenie' ) ) {
		return 'Bieżące komunikaty administracji dla mieszkańców — terminy odczytów liczników, planowane prace w budynkach i sprawy porządkowe.';
	}
	if ( is_home() ) {
		return 'Co się dzieje w firmie i na budynkach, którymi zarządzamy — remonty, inwestycje i sprawy administracyjne.';
	}
	if ( is_page( 'kontakt' ) ) {
		return sprintf(
			'Administracja, czynsze, windykacja i zgłoszenia awarii. %s, %s. Tel. %s, %s.',
			trrol_opt( 'ulica' ),
			trrol_opt( 'miasto' ),
			trrol_opt( 'tel_sekretariat' ),
			trrol_opt( 'email' )
		);
	}
	if ( is_page( 'o-nas' ) ) {
		return sprintf( 'Od %s r. zarządzamy kamienicami i budynkami mieszkalnymi stanowiącymi własność prywatną. Odciążamy właścicieli z codziennych obowiązków związanych z utrzymaniem nieruchomości.', trrol_opt( 'rok_zalozenia' ) );
	}

	if ( is_singular() ) {
		$post = get_post();
		$text = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		$text = trrol_trim_description( $text );

		if ( '' === $text ) {
			/* Strony bez własnej treści (np. rozdzielacz regulaminów) opisujemy tytułem. */
			$text = trrol_trim_description( sprintf(
				'%s — %s, %s. Tel. %s.',
				get_the_title(),
				trrol_opt( 'firma' ),
				trrol_opt( 'miasto' ),
				trrol_opt( 'tel_sekretariat' )
			) );
		}

		return $text;
	}

	if ( is_category() ) {
		$desc = term_description();
		if ( $desc ) {
			return trrol_trim_description( wp_strip_all_tags( $desc ) );
		}
		return sprintf( 'Wpisy w kategorii %s — TRROL Nieruchomości.', single_cat_title( '', false ) );
	}

	return trrol_trim_description( get_bloginfo( 'description' ) );
}

/**
 * Skrócenie tekstu do długości opisu meta, bez ucinania w połowie słowa.
 *
 * @param string $text  Tekst.
 * @param int    $limit Maksymalna długość.
 * @return string
 */
function trrol_trim_description( $text, $limit = 158 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );

	if ( mb_strlen( $text ) <= $limit ) {
		return $text;
	}

	$cut  = mb_substr( $text, 0, $limit );
	$stop = mb_strrpos( $cut, ' ' );

	if ( false !== $stop && $stop > $limit * 0.6 ) {
		$cut = mb_substr( $cut, 0, $stop );
	}

	return rtrim( $cut, " ,.;:–-" ) . '…';
}

/* =========================================================================
 * 3. Pola SEO przy wpisach i stronach
 * ====================================================================== */

/**
 * Własny tytuł lub opis ustawiony przy bieżącym wpisie.
 *
 * @param string $which title|description.
 * @return string
 */
function trrol_entry_seo( $which ) {
	if ( ! is_singular() ) {
		return '';
	}
	$post = get_post();
	if ( ! $post ) {
		return '';
	}
	return (string) get_post_meta( $post->ID, '_trrol_seo_' . $which, true );
}

/**
 * Metaboks SEO.
 */
function trrol_seo_meta_box() {
	$types = array( 'post', 'page', 'trrol_lokal', 'trrol_oferta', 'trrol_ogloszenie' );
	foreach ( $types as $type ) {
		add_meta_box( 'trrol_seo', 'SEO — jak wpis wygląda w Google', 'trrol_render_seo_box', $type, 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'trrol_seo_meta_box' );

/**
 * Widok metaboksu SEO.
 *
 * @param WP_Post $post Wpis.
 */
function trrol_render_seo_box( $post ) {
	wp_nonce_field( 'trrol_save_seo', 'trrol_seo_nonce' );

	$title = (string) get_post_meta( $post->ID, '_trrol_seo_title', true );
	$desc  = (string) get_post_meta( $post->ID, '_trrol_seo_description', true );
	?>
	<p class="description" style="margin-top:0">
		Zostaw puste, a tytuł i opis wygenerują się same z tytułu i zajawki wpisu.
		Wypełnij, jeśli chcesz mieć kontrolę nad tym, co widać w wynikach wyszukiwania.
	</p>
	<table class="form-table"><tbody>
		<tr>
			<th scope="row"><label for="trrol_seo_title">Tytuł w Google</label></th>
			<td>
				<input type="text" class="large-text" id="trrol_seo_title" name="trrol_seo_title"
					value="<?php echo esc_attr( $title ); ?>" maxlength="70">
				<p class="description">Najlepiej do 60 znaków.</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="trrol_seo_description">Opis w Google</label></th>
			<td>
				<textarea class="large-text" rows="3" id="trrol_seo_description" name="trrol_seo_description"
					maxlength="200"><?php echo esc_textarea( $desc ); ?></textarea>
				<p class="description">Najlepiej do 155 znaków — dłuższe Google i tak przytnie.</p>
			</td>
		</tr>
	</tbody></table>
	<?php
}

/**
 * Zapis pól SEO.
 *
 * @param int $post_id ID wpisu.
 */
function trrol_save_seo( $post_id ) {
	if ( ! isset( $_POST['trrol_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trrol_seo_nonce'] ) ), 'trrol_save_seo' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'title', 'description' ) as $field ) {
		$key = 'trrol_seo_' . $field;
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_' . $key );
		} else {
			update_post_meta( $post_id, '_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'trrol_save_seo' );

/* =========================================================================
 * 4. Znaczniki w <head>
 * ====================================================================== */

/**
 * Adres kanoniczny bieżącego widoku.
 *
 * @return string
 */
function trrol_canonical_url() {
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( is_home() ) {
		$url = trrol_archive_url( 'post' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$url = get_term_link( get_queried_object() );
	} else {
		$url = home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	}

	if ( is_wp_error( $url ) || ! $url ) {
		return '';
	}

	$page = (int) get_query_var( 'paged' );
	if ( $page > 1 ) {
		$url = trailingslashit( $url ) . 'page/' . $page . '/';
	}

	return $url;
}

/**
 * Obrazek do udostępniania w mediach społecznościowych.
 *
 * @return string
 */
function trrol_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $src ) {
			return $src[0];
		}
	}
	return trrol_img( 'og-trrol.png' );
}

/**
 * Znaczniki SEO w sekcji head.
 */
function trrol_seo_head() {
	$description = trrol_meta_description();
	$canonical   = trrol_canonical_url();
	$image       = trrol_share_image();
	$title       = wp_get_document_title();

	echo "\n<!-- TRROL SEO -->\n";

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}

	/* Strony bez wartości dla wyszukiwarki. */
	if ( is_search() || is_404() || is_author() || is_attachment() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	} else {
		echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
	}

	/* Open Graph */
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="pl_PL">' . "\n" );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( array( 'post', 'trrol_ogloszenie' ) ) ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( $canonical ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	}
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		printf( '<meta property="og:image:width" content="1200">' . "\n" );
		printf( '<meta property="og:image:height" content="630">' . "\n" );
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) . ' — zarządzanie nieruchomościami' ) );
	}

	if ( is_singular( array( 'post', 'trrol_ogloszenie' ) ) ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	/* Twitter */
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}

	/* Kod weryfikacyjny Google Search Console */
	$verification = trrol_opt( 'seo_google_verification' );
	if ( $verification ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $verification ) );
	}

	/* Kontakt dla wyszukiwarek lokalnych */
	printf( '<meta name="geo.placename" content="%s">' . "\n", esc_attr( 'Siemianowice Śląskie' ) );
	printf( '<meta name="geo.region" content="PL-SL">' . "\n" );

	trrol_jsonld();

	echo "<!-- /TRROL SEO -->\n\n";
}
add_action( 'wp_head', 'trrol_seo_head', 2 );

/* =========================================================================
 * 5. Dane strukturalne (JSON-LD)
 * ====================================================================== */

/**
 * Graf danych strukturalnych.
 */
function trrol_jsonld() {
	$home      = home_url( '/' );
	$org_id    = $home . '#organizacja';
	$site_id   = $home . '#serwis';
	$canonical = trrol_canonical_url();

	$dni = array(
		'Monday'    => trrol_opt( 'godziny_pn' ),
		'Tuesday'   => trrol_opt( 'godziny_wt' ),
		'Wednesday' => trrol_opt( 'godziny_sr' ),
		'Thursday'  => trrol_opt( 'godziny_cz' ),
		'Friday'    => trrol_opt( 'godziny_pt' ),
	);

	$hours = array();
	foreach ( $dni as $day => $range ) {
		$parsed = trrol_parse_hours( $range );
		if ( $parsed ) {
			$hours[] = array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => 'https://schema.org/' . $day,
				'opens'     => $parsed[0],
				'closes'    => $parsed[1],
			);
		}
	}

	$organization = array(
		'@type'       => 'RealEstateAgent',
		'@id'         => $org_id,
		'name'        => trrol_opt( 'firma' ),
		'alternateName' => 'TRROL Nieruchomości',
		'url'         => $home,
		'description' => sprintf(
			'Kompleksowa administracja i zarządzanie nieruchomościami prywatnymi — kamienice i budynki mieszkalne w Siemianowicach Śląskich i okolicach. Działamy od %s roku.',
			trrol_opt( 'rok_zalozenia' )
		),
		'telephone'   => trrol_opt( 'tel_sekretariat' ),
		'faxNumber'   => trrol_opt( 'faks' ),
		'email'       => trrol_opt( 'email' ),
		'vatID'       => 'PL' . preg_replace( '/\D/', '', trrol_opt( 'nip' ) ),
		'taxID'       => trrol_opt( 'nip' ),
		'foundingDate' => trrol_opt( 'rok_zalozenia' ),
		'logo'        => array(
			'@type' => 'ImageObject',
			'url'   => trrol_img( 'logo-full.png' ),
		),
		'image'       => trrol_share_image(),
		'address'     => trrol_postal_address(),
		'areaServed'  => array(
			array( '@type' => 'City', 'name' => 'Siemianowice Śląskie' ),
			array( '@type' => 'AdministrativeArea', 'name' => 'województwo śląskie' ),
		),
		'knowsLanguage' => 'pl',
	);

	if ( $hours ) {
		$organization['openingHoursSpecification'] = $hours;
	}

	$lat = trrol_opt( 'geo_lat' );
	$lng = trrol_opt( 'geo_lng' );
	if ( $lat && $lng ) {
		$organization['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => $lat,
			'longitude' => $lng,
		);
	}

	$maps = trrol_opt( 'seo_mapa_link' );
	if ( $maps ) {
		$organization['hasMap'] = $maps;
	}

	$profiles = array_filter( array( trrol_opt( 'seo_profil_google' ), trrol_opt( 'seo_profil_facebook' ) ) );
	if ( $profiles ) {
		$organization['sameAs'] = array_values( $profiles );
	}

	$organization['contactPoint'] = array(
		array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'Administracja i zgłoszenia awarii',
			'telephone'         => trrol_opt( 'tel_administracja' ),
			'email'             => trrol_opt( 'email' ),
			'availableLanguage' => 'pl',
		),
		array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'Czynsze i rozliczenia',
			'telephone'         => trrol_opt( 'tel_ksiegowosc' ),
			'availableLanguage' => 'pl',
		),
	);

	$graph = array(
		$organization,
		array(
			'@type'      => 'WebSite',
			'@id'        => $site_id,
			'url'        => $home,
			'name'       => get_bloginfo( 'name' ),
			'inLanguage' => 'pl-PL',
			'publisher'  => array( '@id' => $org_id ),
		),
	);

	/* Strona bieżąca */
	if ( $canonical ) {
		$webpage = array(
			'@type'      => 'WebPage',
			'@id'        => $canonical . '#strona',
			'url'        => $canonical,
			'name'       => wp_get_document_title(),
			'description' => trrol_meta_description(),
			'inLanguage' => 'pl-PL',
			'isPartOf'   => array( '@id' => $site_id ),
			'about'      => array( '@id' => $org_id ),
		);

		if ( is_singular() ) {
			$webpage['datePublished'] = get_the_date( 'c' );
			$webpage['dateModified']  = get_the_modified_date( 'c' );
		}

		$graph[] = $webpage;
	}

	/* Okruszki */
	$crumbs = trrol_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$items = array();
		foreach ( $crumbs as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb['label'],
			);
			if ( ! empty( $crumb['url'] ) ) {
				$item['item'] = $crumb['url'];
			}
			$items[] = $item;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $canonical . '#okruszki',
			'itemListElement' => $items,
		);
	}

	/* Najczęstsze pytania na stronie głównej */
	if ( is_front_page() ) {
		$faq = array();
		foreach ( trrol_faq_items() as $item ) {
			$faq[] = array(
				'@type'          => 'Question',
				'name'           => $item['q'],
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $item['a'] ),
			);
		}
		if ( $faq ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $home . '#faq',
				'mainEntity' => $faq,
			);
		}
	}

	/* Wpisy */
	if ( is_singular( array( 'post', 'trrol_ogloszenie' ) ) ) {
		$graph[] = array(
			'@type'            => 'Article',
			'@id'              => $canonical . '#artykul',
			'headline'         => trrol_trim_description( get_the_title(), 110 ),
			'description'      => trrol_meta_description(),
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'inLanguage'       => 'pl-PL',
			'mainEntityOfPage' => array( '@id' => $canonical . '#strona' ),
			'author'           => array( '@id' => $org_id ),
			'publisher'        => array( '@id' => $org_id ),
		);
	}

	/* Wolny lokal */
	if ( is_singular( 'trrol_lokal' ) ) {
		$post   = get_post();
		$rodzaj = get_post_meta( $post->ID, '_trrol_rodzaj', true );
		$powierzchnia = get_post_meta( $post->ID, '_trrol_powierzchnia', true );

		$accommodation = array(
			'@type'       => 'uzytkowy' === $rodzaj ? 'Place' : 'Apartment',
			'@id'         => $canonical . '#lokal',
			'name'        => get_the_title(),
			'description' => trrol_meta_description(),
			'address'     => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => 'Siemianowice Śląskie',
				'addressRegion'   => 'śląskie',
				'addressCountry'  => 'PL',
			),
		);

		$metry = trrol_parse_area( $powierzchnia );
		if ( $metry ) {
			$accommodation['floorSize'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => $metry,
				'unitCode' => 'MTK',
			);
		}

		$graph[] = $accommodation;
		$graph[] = array(
			'@type'         => 'Offer',
			'@id'           => $canonical . '#oferta',
			'itemOffered'   => array( '@id' => $canonical . '#lokal' ),
			'offeredBy'     => array( '@id' => $org_id ),
			'businessFunction' => 'https://purl.org/goodrelations/v1#LeaseOut',
			'availability'  => 'https://schema.org/InStock',
			'url'           => $canonical,
			'areaServed'    => 'Siemianowice Śląskie',
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array( '@context' => 'https://schema.org', '@graph' => $graph ),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}

/**
 * Adres siedziby w formacie PostalAddress.
 *
 * Kod pocztowy i miejscowość rozdzielamy z jednego pola ustawień,
 * a skrót „Śl." rozwijamy do pełnej nazwy — wyszukiwarki dopasowują
 * wizytówkę po pełnej nazwie miasta.
 *
 * @return array<string,string>
 */
function trrol_postal_address() {
	$miasto = trim( trrol_opt( 'miasto' ) );

	$kod  = '';
	$town = $miasto;
	if ( preg_match( '/^(\d{2}-\d{3})\s+(.*)$/u', $miasto, $m ) ) {
		$kod  = $m[1];
		$town = trim( $m[2] );
	}

	$town = preg_replace( '/\bŚl\.?$/u', 'Śląskie', $town );

	$address = array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => trrol_opt( 'ulica' ),
		'addressLocality' => $town,
		'addressRegion'   => 'śląskie',
		'addressCountry'  => 'PL',
	);

	if ( $kod ) {
		$address['postalCode'] = $kod;
	}

	return $address;
}

/**
 * Rozbicie zapisu godzin „7:00–17:00" na godzinę otwarcia i zamknięcia.
 *
 * @param string $range Zakres godzin.
 * @return array{0:string,1:string}|null
 */
function trrol_parse_hours( $range ) {
	if ( ! preg_match( '/(\d{1,2}):(\d{2})\s*[–-]\s*(\d{1,2}):(\d{2})/u', (string) $range, $m ) ) {
		return null;
	}
	return array(
		sprintf( '%02d:%s', $m[1], $m[2] ),
		sprintf( '%02d:%s', $m[3], $m[4] ),
	);
}

/**
 * Powierzchnia w metrach jako liczba.
 *
 * @param string $value Zapis powierzchni, np. „41,80 m²".
 * @return float|null
 */
function trrol_parse_area( $value ) {
	if ( ! preg_match( '/([\d]+[.,]?[\d]*)/u', (string) $value, $m ) ) {
		return null;
	}
	return (float) str_replace( ',', '.', $m[1] );
}

/**
 * Okruszki bieżącego widoku jako dane (używane też w JSON-LD).
 *
 * @return array<int,array{label:string,url:string}>
 */
function trrol_breadcrumb_items() {
	$items = array( array( 'label' => 'Strona główna', 'url' => home_url( '/' ) ) );

	if ( is_front_page() ) {
		return $items;
	}

	if ( is_singular( array( 'trrol_lokal', 'trrol_oferta', 'trrol_ogloszenie', 'post' ) ) ) {
		$type = get_post_type();
		$items[] = array( 'label' => trrol_archive_label( $type ), 'url' => trrol_archive_url( $type ) );
		$items[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
		return $items;
	}

	if ( is_page() ) {
		$post = get_post();
		if ( $post && $post->post_parent ) {
			$parent  = get_post( $post->post_parent );
			$items[] = array( 'label' => $parent->post_title, 'url' => get_permalink( $parent ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => get_permalink() );
		return $items;
	}

	if ( is_post_type_archive() ) {
		$type    = get_query_var( 'post_type' );
		$items[] = array( 'label' => trrol_archive_label( $type ), 'url' => trrol_archive_url( $type ) );
		return $items;
	}

	if ( is_home() ) {
		$items[] = array( 'label' => 'Aktualności', 'url' => trrol_archive_url( 'post' ) );
		return $items;
	}

	if ( is_category() ) {
		$items[] = array( 'label' => 'Aktualności', 'url' => trrol_archive_url( 'post' ) );
		$items[] = array( 'label' => single_cat_title( '', false ), 'url' => get_term_link( get_queried_object() ) );
	}

	return $items;
}

/**
 * Czytelne adresy wpisów.
 *
 * Tytuły lokali zawierają „m²", a WordPress zostawia w adresie zakodowane %c2%b2.
 * Zamieniamy takie znaki na zwykłe odpowiedniki jeszcze przed utworzeniem odnośnika.
 *
 * @param string $title    Tytuł po oczyszczeniu.
 * @param string $raw      Tytuł źródłowy.
 * @param string $context  Kontekst wywołania.
 * @return string
 */
function trrol_clean_slug( $title, $raw = '', $context = 'display' ) {
	if ( 'save' !== $context ) {
		return $title;
	}

	$map = array( '²' => '2', '³' => '3', '°' => '', '„' => '', '”' => '', '–' => '-', '—' => '-' );
	$title = strtr( $title, $map );
	$title = strtr( urldecode( $title ), $map );

	$title = preg_replace( '/[^a-z0-9\-]/', '-', $title );
	$title = preg_replace( '/-+/', '-', $title );

	return trim( $title, '-' );
}
add_filter( 'sanitize_title', 'trrol_clean_slug', 20, 3 );

/* =========================================================================
 * 6. Mapa strony i robots.txt
 * ====================================================================== */

/**
 * Mapa strony nie potrzebuje listy autorów.
 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return ( 'users' === $name ) ? false : $provider;
}, 10, 2 );

/**
 * Ze strony wykluczamy techniczne strony bez wartości w wyszukiwarce.
 */
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'page' === $post_type ) {
		$skip = array_filter( array( get_page_by_path( 'strona-glowna' ) ) );
		if ( $skip ) {
			$args['post__not_in'] = wp_list_pluck( $skip, 'ID' );
		}
	}
	return $args;
}, 10, 2 );

/**
 * WordPress potrafi oddać mapę strony ze statusem 404 — prostujemy nagłówek.
 */
function trrol_fix_sitemap_status() {
	if ( '' !== (string) get_query_var( 'sitemap' ) || '' !== (string) get_query_var( 'sitemap-stylesheet' ) ) {
		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );
	}
}
add_action( 'template_redirect', 'trrol_fix_sitemap_status', 0 );

/**
 * robots.txt.
 *
 * @param string $output Domyślna treść.
 * @return string
 */
function trrol_robots_txt( $output ) {
	$lines = array(
		'User-agent: *',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
		'Disallow: /wp-content/uploads/trrol-oferty/',
		'Disallow: /*?trrol_status=',
		'Disallow: /*?trrol_token=',
		'Disallow: /?s=',
		'Disallow: /search/',
		'',
		'Sitemap: ' . home_url( '/wp-sitemap.xml' ),
		'',
	);
	return implode( "\n", $lines );
}
add_filter( 'robots_txt', 'trrol_robots_txt', 20 );

/* =========================================================================
 * 7. Przekierowania ze starej strony
 * ====================================================================== */

/**
 * Mapa starych adresów na nowe.
 *
 * @return array<string,string>
 */
function trrol_legacy_redirects() {
	return array(
		'/index.html'                    => '/',
		'/administracja.html'            => '/',
		'/administracja_kadra.html'      => '/o-nas/',
		'/administracja_oferta.html'     => '/o-nas/',
		'/administracja_kontakt.html'    => '/kontakt/',
		'/administracja_lokale.html'     => '/wolne-lokale/',
		'/administracja_konkurs.html'    => '/oferty-dla-firm/',
		'/administracja_pobieranie.html' => '/regulaminy/',
		'/budownictwo.html'              => '/o-nas/',
		'/budownictwo_kadra.html'        => '/o-nas/',
		'/budownictwo_oferta.html'       => '/o-nas/',
		'/budownictwo_galeria.html'      => '/o-nas/',
		'/budownictwo_inwestycje.html'   => '/o-nas/',
		'/budownictwo_partnerzy.html'    => '/o-nas/',
		'/budownictwo_referencje.html'   => '/o-nas/',
		'/budownictwo_kontakt.html'      => '/kontakt/',
		'/regulamin.pdf'                 => '/regulaminy/',
		'/konkurs_ofert.pdf'             => '/oferty-dla-firm/',
		'/wniosek_na_mieszkanie.pdf'     => '/wolne-lokale/',
		'/wniosek_na_mieszkanie%20.pdf'  => '/wolne-lokale/',
		'/oswiadczenie.pdf'              => '/kontakt/',
		'/oswiadczenie_smieci.pdf'       => '/kontakt/',
		'/zawiadomienie_klatki.pdf'      => '/ogloszenia/',
		'/wentylacja_nawiewniki.pdf'     => '/ogloszenia/',
	);
}

/**
 * Obsługa przekierowań 301 ze starych adresów.
 */
function trrol_handle_legacy_redirects() {
	if ( ! is_404() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = strtok( $request, '?' );
	$path    = '/' . ltrim( (string) $path, '/' );

	$map = trrol_legacy_redirects();

	foreach ( array( $path, rawurldecode( $path ) ) as $candidate ) {
		$key = strtolower( $candidate );
		foreach ( $map as $from => $to ) {
			if ( strtolower( $from ) === $key ) {
				wp_redirect( home_url( $to ), 301 );
				exit;
			}
		}
	}

	/* Stare materiały referencyjne firmy budowlanej — kierujemy na stronę o firmie. */
	if ( 0 === stripos( $path, '/referencje/' ) ) {
		wp_redirect( home_url( '/o-nas/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'trrol_handle_legacy_redirects', 1 );

/* =========================================================================
 * 8. Porządki wpływające na szybkość strony
 * ====================================================================== */

/**
 * Motyw nie używa bloków ani emoji — nie ładujemy tych zasobów.
 */
function trrol_dequeue_unused() {
	if ( is_admin() ) {
		return;
	}
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wp-emoji-styles' );
}
add_action( 'wp_enqueue_scripts', 'trrol_dequeue_unused', 100 );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

/**
 * Wcześniejsze wczytanie zdjęcia głównego — poprawia czas największego wyrenderowania.
 */
function trrol_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
		esc_url( trrol_img( 'budynek-1.jpg' ) )
	);
}
add_action( 'wp_head', 'trrol_preload_hero', 1 );

/**
 * Strony załączników nie mają wartości — kierujemy na plik.
 */
function trrol_redirect_attachments() {
	if ( is_attachment() ) {
		$url = wp_get_attachment_url( get_queried_object_id() );
		if ( $url ) {
			wp_redirect( $url, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'trrol_redirect_attachments', 2 );
