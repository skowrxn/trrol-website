<?php
/**
 * Migracja 2026-09-14:
 * - telefony: kierownik administracji zamiast głównej księgowej, drugie numery działów, dział techniczny,
 * - dyżur awaryjny poza godzinami pracy biura,
 * - zakładka „Dokumenty do pobrania” w menu (osobna od regulaminów),
 * - nowa nazwa regulaminu: „Regulamin użytkowania lokali i porządku domowego”.
 *
 * Uruchomienie: wp eval-file 2026-09-14-telefony-dokumenty.php [ścieżka-do-nowego-pdf]
 * Skrypt można uruchomić wielokrotnie — nie tworzy duplikatów.
 */

$nowa_nazwa = 'Regulamin użytkowania lokali i porządku domowego';
$nowy_pdf   = isset( $args[0] ) ? $args[0] : '';

/* 1. Ustawienia */
$o = get_option( 'trrol_options', array() );

if ( ! isset( $o['tel_kierownik'] ) ) {
	$o['tel_kierownik'] = ! empty( $o['tel_ksiegowosc'] ) ? $o['tel_ksiegowosc'] : '(32) 228-00-03';
}
unset( $o['tel_ksiegowosc'] );

foreach ( array( 'tel_administracja_2', 'tel_techniczny', 'tel_techniczny_2' ) as $k ) {
	if ( ! isset( $o[ $k ] ) ) {
		$o[ $k ] = '';
	}
}

$awaria = array(
	'awaria_tel'   => '510-141-114',
	'awaria_osoba' => 'Przemysław Hrabia',
	'awaria_kiedy' => 'od poniedziałku do piątku od godziny 15:00, w soboty, niedziele i święta całodobowo',
);
foreach ( $awaria as $k => $v ) {
	if ( empty( $o[ $k ] ) ) {
		$o[ $k ] = $v;
	}
}
update_option( 'trrol_options', $o );
WP_CLI::log( 'Ustawienia: kierownik=' . $o['tel_kierownik'] . ', dyżur=' . $o['awaria_tel'] );

/* 2. Pliki regulaminów */
$znajdz_zalacznik = function ( $plik ) {
	$ids = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array( array( 'key' => '_wp_attached_file', 'value' => $plik, 'compare' => 'LIKE' ) ),
	) );
	return $ids ? (int) $ids[0] : 0;
};

$att_reg  = $znajdz_zalacznik( 'regulamin-uzytkowania-lokali.pdf' );
$att_woda = $znajdz_zalacznik( 'rozliczanie-wody-i-sciekow.pdf' );

if ( $att_reg ) {
	wp_update_post( array( 'ID' => $att_reg, 'post_title' => $nowa_nazwa ) );
	if ( $nowy_pdf && file_exists( $nowy_pdf ) ) {
		$cel = get_attached_file( $att_reg );
		copy( $nowy_pdf, $cel );
		WP_CLI::log( 'Podmieniono plik PDF: ' . $cel );
	}
}
if ( $att_woda ) {
	wp_update_post( array( 'ID' => $att_woda, 'post_title' => 'Regulamin rozliczania zużycia wody i odprowadzania ścieków' ) );
}
WP_CLI::log( "Załączniki: regulamin=$att_reg, woda=$att_woda" );

/* 3. Strona regulaminu */
$strona = get_page_by_path( 'regulaminy/regulamin-uzytkowania-lokali' );
if ( $strona ) {
	wp_update_post( array( 'ID' => $strona->ID, 'post_title' => $nowa_nazwa ) );
	WP_CLI::log( 'Strona regulaminu: ' . $strona->ID );
}

/* 4. Dokumenty do pobrania — sekcja startuje pusta; regulaminy nie są dokumentami (ustawiane w Ustawieniach TRROL). */

/* 5. Menu główne */
$menu = wp_get_nav_menu_object( 'Menu główne' );
if ( $menu ) {
	$items = wp_get_nav_menu_items( $menu->term_id );
	$jest  = false;
	$poz   = 0;
	foreach ( $items as $item ) {
		if ( false !== strpos( $item->url, '/dokumenty/' ) ) {
			$jest = true;
		}
		if ( 'Ogłoszenia' === $item->title ) {
			$poz = (int) $item->menu_order;
		}
	}
	if ( ! $jest ) {
		/* Przesunięcie pozycji za „Ogłoszeniami”. */
		foreach ( $items as $item ) {
			if ( (int) $item->menu_order > $poz ) {
				wp_update_post( array( 'ID' => $item->ID, 'menu_order' => (int) $item->menu_order + 1 ) );
			}
		}
		wp_update_nav_menu_item( $menu->term_id, 0, array(
			'menu-item-title'    => 'Dokumenty do pobrania',
			'menu-item-url'      => home_url( '/dokumenty/' ),
			'menu-item-type'     => 'custom',
			'menu-item-status'   => 'publish',
			'menu-item-position' => $poz + 1,
		) );
		WP_CLI::log( 'Menu: dodano „Dokumenty do pobrania” na pozycji ' . ( $poz + 1 ) );
	}
}

flush_rewrite_rules( false );
WP_CLI::success( 'Migracja zakończona.' );
