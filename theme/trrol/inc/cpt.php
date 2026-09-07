<?php
/**
 * Typy treści: wolne lokale, oferty dla firm, ogłoszenia.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja typów treści.
 */
function trrol_register_post_types() {

	register_post_type( 'trrol_lokal', array(
		'labels' => array(
			'name'               => 'Wolne lokale',
			'singular_name'      => 'Wolny lokal',
			'add_new'            => 'Dodaj lokal',
			'add_new_item'       => 'Dodaj wolny lokal',
			'edit_item'          => 'Edytuj lokal',
			'new_item'           => 'Nowy lokal',
			'view_item'          => 'Zobacz lokal',
			'search_items'       => 'Szukaj lokali',
			'not_found'          => 'Brak lokali w wykazie',
			'not_found_in_trash' => 'Brak lokali w koszu',
			'menu_name'          => 'Wolne lokale',
		),
		'public'        => true,
		'has_archive'   => 'wolne-lokale',
		'rewrite'       => array( 'slug' => 'wolne-lokale', 'with_front' => false ),
		'menu_icon'     => 'dashicons-admin-home',
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
	) );

	register_post_type( 'trrol_oferta', array(
		'labels' => array(
			'name'               => 'Oferty dla firm',
			'singular_name'      => 'Zlecenie',
			'add_new'            => 'Dodaj zlecenie',
			'add_new_item'       => 'Dodaj zlecenie',
			'edit_item'          => 'Edytuj zlecenie',
			'new_item'           => 'Nowe zlecenie',
			'view_item'          => 'Zobacz zlecenie',
			'search_items'       => 'Szukaj zleceń',
			'not_found'          => 'Brak zleceń',
			'not_found_in_trash' => 'Brak zleceń w koszu',
			'menu_name'          => 'Oferty dla firm',
		),
		'public'        => true,
		'has_archive'   => 'oferty-dla-firm',
		'rewrite'       => array( 'slug' => 'oferty-dla-firm', 'with_front' => false ),
		'menu_icon'     => 'dashicons-hammer',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
	) );

	register_post_type( 'trrol_ogloszenie', array(
		'labels' => array(
			'name'               => 'Ogłoszenia',
			'singular_name'      => 'Ogłoszenie',
			'add_new'            => 'Dodaj ogłoszenie',
			'add_new_item'       => 'Dodaj ogłoszenie',
			'edit_item'          => 'Edytuj ogłoszenie',
			'new_item'           => 'Nowe ogłoszenie',
			'view_item'          => 'Zobacz ogłoszenie',
			'search_items'       => 'Szukaj ogłoszeń',
			'not_found'          => 'Brak ogłoszeń',
			'not_found_in_trash' => 'Brak ogłoszeń w koszu',
			'menu_name'          => 'Ogłoszenia',
		),
		'public'        => true,
		'has_archive'   => 'ogloszenia',
		'rewrite'       => array( 'slug' => 'ogloszenia', 'with_front' => false ),
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'trrol_register_post_types' );

/**
 * Definicja pól dodatkowych dla każdego typu treści.
 *
 * @return array<string,array<string,array{label:string,type:string,options?:array,hint?:string}>>
 */
function trrol_meta_fields() {
	return array(
		'trrol_lokal' => array(
			'_trrol_powierzchnia' => array( 'label' => 'Powierzchnia', 'type' => 'text', 'hint' => 'np. 41,80 m²' ),
			'_trrol_uklad'        => array( 'label' => 'Układ', 'type' => 'text', 'hint' => 'np. 2 pokoje, II piętro' ),
			'_trrol_budynek'      => array( 'label' => 'Dotyczy budynku', 'type' => 'text', 'hint' => 'np. Michałkowicka 24' ),
			'_trrol_rodzaj'       => array(
				'label'   => 'Rodzaj lokalu',
				'type'    => 'select',
				'options' => array( 'mieszkalny' => 'Lokal mieszkalny', 'uzytkowy' => 'Lokal użytkowy' ),
			),
			'_trrol_dostepnosc'   => array( 'label' => 'Dostępność', 'type' => 'text', 'hint' => 'np. od zaraz / w rezerwacji' ),
		),
		'trrol_oferta' => array(
			'_trrol_numer'   => array( 'label' => 'Numer zlecenia', 'type' => 'text', 'hint' => 'np. 08/2026' ),
			'_trrol_termin'  => array( 'label' => 'Termin składania ofert', 'type' => 'date', 'hint' => 'Po tej dacie zlecenie oznaczymy jako zamknięte.' ),
			'_trrol_status'  => array(
				'label'   => 'Status naboru',
				'type'    => 'select',
				'options' => array( 'otwarte' => 'Otwarty nabór', 'zamkniete' => 'Nabór zakończony / rozstrzygnięte' ),
			),
			'_trrol_budynek' => array( 'label' => 'Dotyczy budynku', 'type' => 'text', 'hint' => 'np. Śląska 62' ),
		),
		'trrol_ogloszenie' => array(
			'_trrol_kategoria' => array( 'label' => 'Etykieta', 'type' => 'text', 'hint' => 'np. Liczniki, Prace, Parking — pokazuje się przy dacie' ),
			'_trrol_budynek'   => array( 'label' => 'Dotyczy budynku', 'type' => 'text', 'hint' => 'np. Powstańców 11' ),
			'_trrol_wazne_do'  => array( 'label' => 'Ważne do', 'type' => 'date', 'hint' => 'Opcjonalnie — data, po której ogłoszenie jest nieaktualne.' ),
		),
	);
}

/**
 * Metaboksy.
 */
function trrol_add_meta_boxes() {
	foreach ( array_keys( trrol_meta_fields() ) as $type ) {
		add_meta_box(
			'trrol_details',
			'Dane wpisu',
			'trrol_render_meta_box',
			$type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'trrol_add_meta_boxes' );

/**
 * Renderowanie metaboksa.
 *
 * @param WP_Post $post Wpis.
 */
function trrol_render_meta_box( $post ) {
	$all    = trrol_meta_fields();
	$fields = isset( $all[ $post->post_type ] ) ? $all[ $post->post_type ] : array();

	wp_nonce_field( 'trrol_save_meta', 'trrol_meta_nonce' );

	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $key => $field ) {
		$value = (string) get_post_meta( $post->ID, $key, true );
		printf( '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td>', esc_attr( $key ), esc_html( $field['label'] ) );

		if ( 'select' === $field['type'] ) {
			printf( '<select name="%s" id="%s">', esc_attr( $key ), esc_attr( $key ) );
			foreach ( $field['options'] as $opt_val => $opt_label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $opt_val ),
					selected( $value, $opt_val, false ),
					esc_html( $opt_label )
				);
			}
			echo '</select>';
		} elseif ( 'date' === $field['type'] ) {
			printf(
				'<input type="date" name="%s" id="%s" value="%s" class="regular-text">',
				esc_attr( $key ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		} else {
			printf(
				'<input type="text" name="%s" id="%s" value="%s" class="regular-text">',
				esc_attr( $key ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $field['hint'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['hint'] ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Zapis pól dodatkowych.
 *
 * @param int $post_id ID wpisu.
 */
function trrol_save_meta( $post_id ) {
	if ( ! isset( $_POST['trrol_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trrol_meta_nonce'] ) ), 'trrol_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$all  = trrol_meta_fields();
	$type = get_post_type( $post_id );
	if ( ! isset( $all[ $type ] ) ) {
		return;
	}

	foreach ( $all[ $type ] as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'trrol_save_meta' );

/**
 * Kolumny na liście wpisów w kokpicie.
 */
function trrol_admin_columns( $columns ) {
	$screen = get_current_screen();
	$type   = $screen ? $screen->post_type : '';

	$extra = array();
	if ( 'trrol_lokal' === $type ) {
		$extra = array( 'trrol_powierzchnia' => 'Powierzchnia', 'trrol_uklad' => 'Układ' );
	} elseif ( 'trrol_oferta' === $type ) {
		$extra = array( 'trrol_numer' => 'Nr zlecenia', 'trrol_termin' => 'Termin ofert', 'trrol_status' => 'Status' );
	} elseif ( 'trrol_ogloszenie' === $type ) {
		$extra = array( 'trrol_kategoria' => 'Etykieta' );
	}

	if ( ! $extra ) {
		return $columns;
	}

	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			foreach ( $extra as $k => $v ) {
				$new[ $k ] = $v;
			}
		}
	}
	return $new;
}
add_filter( 'manage_trrol_lokal_posts_columns', 'trrol_admin_columns' );
add_filter( 'manage_trrol_oferta_posts_columns', 'trrol_admin_columns' );
add_filter( 'manage_trrol_ogloszenie_posts_columns', 'trrol_admin_columns' );

/**
 * Zawartość kolumn.
 */
function trrol_admin_column_content( $column, $post_id ) {
	$map = array(
		'trrol_powierzchnia' => '_trrol_powierzchnia',
		'trrol_uklad'        => '_trrol_uklad',
		'trrol_numer'        => '_trrol_numer',
		'trrol_kategoria'    => '_trrol_kategoria',
	);

	if ( isset( $map[ $column ] ) ) {
		echo esc_html( get_post_meta( $post_id, $map[ $column ], true ) ?: '—' );
		return;
	}

	if ( 'trrol_termin' === $column ) {
		$termin = get_post_meta( $post_id, '_trrol_termin', true );
		echo $termin ? esc_html( date_i18n( 'd.m.Y', strtotime( $termin ) ) ) : '—';
		return;
	}

	if ( 'trrol_status' === $column ) {
		$open = trrol_oferta_is_open( $post_id );
		printf(
			'<span style="color:%s;font-weight:600">%s</span>',
			$open ? '#2C5AA0' : '#6B7280',
			$open ? 'Otwarty nabór' : 'Zakończony'
		);
	}
}
add_action( 'manage_trrol_lokal_posts_custom_column', 'trrol_admin_column_content', 10, 2 );
add_action( 'manage_trrol_oferta_posts_custom_column', 'trrol_admin_column_content', 10, 2 );
add_action( 'manage_trrol_ogloszenie_posts_custom_column', 'trrol_admin_column_content', 10, 2 );
