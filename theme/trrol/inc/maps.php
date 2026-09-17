<?php
/**
 * Wizytówka Google: rozwinięcie linku, mapa z pinezką wizytówki, dane dla wyszukiwarek.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rozwinięcie linku do wizytówki (np. maps.app.goo.gl/…) i odczyt danych miejsca.
 *
 * Skrócony link nie zawiera identyfikatora miejsca — trzeba podążyć za przekierowaniem.
 * Z pełnego adresu odczytujemy identyfikator wizytówki (CID), nazwę i współrzędne.
 *
 * @param string $link Link wklejony w ustawieniach.
 * @return array{src:string,url:string,cid:string,nazwa:string,lat:string,lng:string}|null
 */
function trrol_gmb_resolve( $link ) {
	$link = trim( (string) $link );
	if ( '' === $link ) {
		return null;
	}

	$url = $link;
	for ( $i = 0; $i < 5 && ! preg_match( '#/maps/place/#', $url ); $i++ ) {
		$res = wp_remote_head( $url, array( 'redirection' => 0, 'timeout' => 8 ) );
		if ( is_wp_error( $res ) ) {
			break;
		}
		$next = wp_remote_retrieve_header( $res, 'location' );
		if ( ! $next ) {
			break;
		}
		$url = is_array( $next ) ? end( $next ) : $next;
	}

	$dane = array(
		'src'   => $link,
		'url'   => strtok( $url, '?' ),
		'cid'   => '',
		'nazwa' => '',
		'lat'   => '',
		'lng'   => '',
	);

	/* Identyfikator miejsca: …!1s0x4716d16eaaca17d1:0x402845d8e0e0d7cc… — CID to druga część. */
	if ( preg_match( '/!1s0x[0-9a-f]+:0x([0-9a-f]+)/i', $url, $m ) ) {
		$dane['cid'] = trrol_hex_to_dec( $m[1] );
	} elseif ( preg_match( '/[?&]cid=(\d+)/', $link, $m ) ) {
		$dane['cid'] = $m[1];
	}

	if ( preg_match( '#/maps/place/([^/@]+)#', $url, $m ) ) {
		$dane['nazwa'] = trim( str_replace( '+', ' ', rawurldecode( $m[1] ) ), " \"" );
	}

	/* Współrzędne pinezki (!3d / !4d) są dokładniejsze niż środek widoku (@lat,lng). */
	if ( preg_match( '/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $m ) ) {
		$dane['lat'] = $m[1];
		$dane['lng'] = $m[2];
	} elseif ( preg_match( '/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m ) ) {
		$dane['lat'] = $m[1];
		$dane['lng'] = $m[2];
	}

	return $dane;
}

/**
 * Zamiana liczby szesnastkowej na dziesiętną bez utraty precyzji (CID przekracza zakres int).
 *
 * @param string $hex Liczba szesnastkowa.
 * @return string
 */
function trrol_hex_to_dec( $hex ) {
	$dec = '0';
	foreach ( str_split( strtolower( $hex ) ) as $ch ) {
		$dec = trrol_bc_add( trrol_bc_mul16( $dec ), (string) hexdec( $ch ) );
	}
	return $dec;
}

/**
 * Mnożenie dużej liczby (zapis dziesiętny) przez 16.
 *
 * @param string $n Liczba.
 * @return string
 */
function trrol_bc_mul16( $n ) {
	$wynik = '';
	$carry = 0;
	for ( $i = strlen( $n ) - 1; $i >= 0; $i-- ) {
		$v      = (int) $n[ $i ] * 16 + $carry;
		$wynik  = ( $v % 10 ) . $wynik;
		$carry  = intdiv( $v, 10 );
	}
	while ( $carry > 0 ) {
		$wynik = ( $carry % 10 ) . $wynik;
		$carry = intdiv( $carry, 10 );
	}
	return ltrim( $wynik, '0' ) ?: '0';
}

/**
 * Dodawanie dużej liczby (zapis dziesiętny) i małej.
 *
 * @param string $a Liczba.
 * @param string $b Liczba.
 * @return string
 */
function trrol_bc_add( $a, $b ) {
	$a     = str_pad( $a, max( strlen( $a ), strlen( $b ) ), '0', STR_PAD_LEFT );
	$b     = str_pad( $b, strlen( $a ), '0', STR_PAD_LEFT );
	$wynik = '';
	$carry = 0;
	for ( $i = strlen( $a ) - 1; $i >= 0; $i-- ) {
		$v     = (int) $a[ $i ] + (int) $b[ $i ] + $carry;
		$wynik = ( $v % 10 ) . $wynik;
		$carry = intdiv( $v, 10 );
	}
	return ( $carry ? $carry : '' ) . $wynik;
}

/**
 * Po zapisaniu ustawień — odświeżenie danych wizytówki, jeśli link się zmienił.
 *
 * @param mixed $old Poprzednie ustawienia.
 * @param mixed $new Nowe ustawienia.
 */
function trrol_gmb_on_save( $old, $new ) {
	$link = is_array( $new ) && isset( $new['seo_profil_google'] ) ? trim( $new['seo_profil_google'] ) : '';
	$gmb  = get_option( 'trrol_gmb', array() );

	if ( '' === $link ) {
		delete_option( 'trrol_gmb' );
		return;
	}
	if ( is_array( $gmb ) && isset( $gmb['src'] ) && $gmb['src'] === $link && ! empty( $gmb['cid'] ) ) {
		return;
	}

	$dane = trrol_gmb_resolve( $link );
	if ( $dane ) {
		update_option( 'trrol_gmb', $dane, false );
	}
}
add_action( 'update_option_trrol_options', 'trrol_gmb_on_save', 10, 2 );

/**
 * Dane wizytówki aktualne względem linku w ustawieniach.
 *
 * @return array|null
 */
function trrol_gmb() {
	$link = trim( trrol_opt( 'seo_profil_google' ) );
	$gmb  = get_option( 'trrol_gmb', array() );
	if ( '' === $link || ! is_array( $gmb ) || empty( $gmb['src'] ) || $gmb['src'] !== $link ) {
		return null;
	}
	return $gmb;
}

/**
 * Adres mapy do osadzenia na stronie.
 *
 * Gdy podany jest link do wizytówki, mapa pokazuje pinezkę tej wizytówki (nazwa, adres,
 * link „Wyświetl większą mapę”). W przeciwnym razie — adres z pola „Adres osadzenia mapy”.
 *
 * @return string
 */
function trrol_map_embed() {
	$gmb = trrol_gmb();
	if ( $gmb && ! empty( $gmb['cid'] ) ) {
		return 'https://maps.google.com/maps?cid=' . rawurlencode( $gmb['cid'] ) . '&output=embed';
	}
	return trrol_opt( 'mapa_embed' );
}

/**
 * Link „Zobacz w Mapach Google” — do wizytówki albo do adresu biura.
 *
 * @return string
 */
function trrol_map_link() {
	$link = trim( trrol_opt( 'seo_profil_google' ) );
	if ( $link ) {
		return $link;
	}
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( trrol_opt( 'ulica' ) . ', ' . trrol_opt( 'miasto' ) );
}

/**
 * Współrzędne biura: z ustawień, a gdy ich brak — z wizytówki.
 *
 * @return array{0:string,1:string}|null
 */
function trrol_geo() {
	$lat = trim( trrol_opt( 'geo_lat' ) );
	$lng = trim( trrol_opt( 'geo_lng' ) );
	if ( $lat && $lng ) {
		return array( $lat, $lng );
	}
	$gmb = trrol_gmb();
	if ( $gmb && $gmb['lat'] && $gmb['lng'] ) {
		return array( $gmb['lat'], $gmb['lng'] );
	}
	return null;
}
