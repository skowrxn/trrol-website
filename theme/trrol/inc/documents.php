<?php
/**
 * Dokumenty do pobrania: typ treści, wybór pliku z biblioteki mediów, lista w kokpicie.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja typu treści.
 *
 * Edycja w klasycznym widoku (bez edytora blokowego) — dokument to nazwa, plik
 * i opcjonalny opis, więc prosty formularz jest tu czytelniejszy.
 */
function trrol_register_documents() {
	register_post_type( 'trrol_dokument', array(
		'labels' => array(
			'name'               => 'Dokumenty do pobrania',
			'singular_name'      => 'Dokument',
			'add_new'            => 'Dodaj dokument',
			'add_new_item'       => 'Dodaj dokument do pobrania',
			'edit_item'          => 'Edytuj dokument',
			'new_item'           => 'Nowy dokument',
			'view_item'          => 'Zobacz dokument',
			'search_items'       => 'Szukaj dokumentów',
			'not_found'          => 'Brak dokumentów',
			'not_found_in_trash' => 'Brak dokumentów w koszu',
			'menu_name'          => 'Dokumenty',
			'enter_title_here'   => 'Nazwa dokumentu, np. Wniosek o wymianę wodomierza',
		),
		'public'        => true,
		'has_archive'   => 'dokumenty',
		'rewrite'       => array( 'slug' => 'dokumenty', 'with_front' => false ),
		'menu_icon'     => 'dashicons-media-document',
		'menu_position' => 23,
		'supports'      => array( 'title', 'page-attributes' ),
		'show_in_rest'  => false,
	) );
}
add_action( 'init', 'trrol_register_documents' );

/**
 * Tekst w polu tytułu.
 */
add_filter( 'enter_title_here', function ( $text, $post ) {
	return 'trrol_dokument' === $post->post_type
		? 'Nazwa dokumentu, np. Wniosek o wymianę wodomierza'
		: $text;
}, 10, 2 );

/**
 * Metaboks z plikiem i opisem.
 */
function trrol_document_meta_box() {
	add_meta_box( 'trrol_dokument_plik', 'Plik do pobrania', 'trrol_render_document_box', 'trrol_dokument', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'trrol_document_meta_box' );

/**
 * Widok metaboksu.
 *
 * @param WP_Post $post Dokument.
 */
function trrol_render_document_box( $post ) {
	wp_nonce_field( 'trrol_save_document', 'trrol_document_nonce' );

	$att  = (int) get_post_meta( $post->ID, '_trrol_plik', true );
	$opis = (string) get_post_meta( $post->ID, '_trrol_opis', true );
	$file = $att ? trrol_document_file( $post->ID ) : null;
	?>
	<div class="trrol-doc">
		<input type="hidden" name="trrol_plik" id="trrol-plik-id" value="<?php echo esc_attr( $att ? $att : '' ); ?>">

		<div class="trrol-doc__file" id="trrol-plik-info"<?php echo $file ? '' : ' hidden'; ?>>
			<span class="dashicons dashicons-media-document"></span>
			<span>
				<strong id="trrol-plik-nazwa"><?php echo $file ? esc_html( wp_basename( get_attached_file( $att ) ) ) : ''; ?></strong><br>
				<span id="trrol-plik-meta" class="description"><?php echo $file ? esc_html( trim( $file['ext'] . ' · ' . $file['size'], ' ·' ) ) : ''; ?></span>
			</span>
		</div>

		<p class="trrol-doc__empty" id="trrol-plik-brak"<?php echo $file ? ' hidden' : ''; ?>>
			Nie wybrano pliku. Dokument bez pliku nie jest wyświetlany na stronie.
		</p>

		<p>
			<button type="button" class="button button-primary" id="trrol-plik-wybierz">
				<?php echo $file ? 'Zmień plik' : 'Wybierz plik'; ?>
			</button>
			<button type="button" class="button" id="trrol-plik-usun"<?php echo $file ? '' : ' hidden'; ?>>Usuń plik</button>
		</p>
		<p class="description">Plik można wgrać z komputera albo wybrać z biblioteki mediów. Obsługiwane są m.in. PDF, DOC, DOCX, XLS i XLSX.</p>

		<hr>

		<p><label for="trrol-opis"><strong>Opis</strong> (opcjonalnie)</label></p>
		<textarea name="trrol_opis" id="trrol-opis" rows="3" class="large-text" maxlength="400"><?php echo esc_textarea( $opis ); ?></textarea>
		<p class="description">Krótkie wyjaśnienie wyświetlane pod nazwą dokumentu, np. „Obowiązuje od 1 stycznia 2024 r.”.</p>
	</div>
	<?php
}

/**
 * Zapis pliku i opisu.
 *
 * @param int $post_id ID dokumentu.
 */
function trrol_save_document( $post_id ) {
	if ( ! isset( $_POST['trrol_document_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trrol_document_nonce'] ) ), 'trrol_save_document' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$att = isset( $_POST['trrol_plik'] ) ? absint( $_POST['trrol_plik'] ) : 0;
	if ( $att && 'attachment' === get_post_type( $att ) ) {
		update_post_meta( $post_id, '_trrol_plik', $att );
	} else {
		delete_post_meta( $post_id, '_trrol_plik' );
	}

	$opis = isset( $_POST['trrol_opis'] ) ? sanitize_textarea_field( wp_unslash( $_POST['trrol_opis'] ) ) : '';
	if ( '' === $opis ) {
		delete_post_meta( $post_id, '_trrol_opis' );
	} else {
		update_post_meta( $post_id, '_trrol_opis', $opis );
	}
}
add_action( 'save_post_trrol_dokument', 'trrol_save_document' );

/**
 * Okno wyboru pliku z biblioteki mediów.
 *
 * @param string $hook Ekran kokpitu.
 */
function trrol_document_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'trrol_dokument' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();

	wp_add_inline_style( 'wp-admin', '
		.trrol-doc__file{display:flex;gap:10px;align-items:center;padding:12px 14px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;margin:4px 0 12px}
		.trrol-doc__file .dashicons{font-size:28px;width:28px;height:28px;color:#2C5AA0}
		.trrol-doc__empty{padding:12px 14px;background:#fcf9e8;border-left:4px solid #dba617;margin:4px 0 12px}
		.trrol-doc [hidden]{display:none!important}
	' );

	$script = <<<'JS'
jQuery(function ($) {
	var frame;
	var $id = $('#trrol-plik-id'), $info = $('#trrol-plik-info'), $brak = $('#trrol-plik-brak');
	var $nazwa = $('#trrol-plik-nazwa'), $meta = $('#trrol-plik-meta');
	var $wybierz = $('#trrol-plik-wybierz'), $usun = $('#trrol-plik-usun');

	$wybierz.on('click', function (e) {
		e.preventDefault();
		if (frame) { frame.open(); return; }
		frame = wp.media({
			title: 'Wybierz plik do pobrania',
			button: { text: 'Użyj tego pliku' },
			multiple: false
		});
		frame.on('select', function () {
			var f = frame.state().get('selection').first().toJSON();
			$id.val(f.id);
			$nazwa.text(f.filename || f.title);
			$meta.text([(f.subtype || '').toUpperCase(), f.filesizeHumanReadable || ''].filter(Boolean).join(' · '));
			$info.prop('hidden', false);
			$brak.prop('hidden', true);
			$usun.prop('hidden', false);
			$wybierz.text('Zmień plik');
			if (!$('#title').val()) { $('#title').val(f.title).trigger('input'); $('#title-prompt-text').addClass('screen-reader-text'); }
		});
		frame.open();
	});

	$usun.on('click', function (e) {
		e.preventDefault();
		$id.val('');
		$info.prop('hidden', true);
		$brak.prop('hidden', false);
		$usun.prop('hidden', true);
		$wybierz.text('Wybierz plik');
	});
});
JS;
	wp_add_inline_script( 'media-editor', $script );
}
add_action( 'admin_enqueue_scripts', 'trrol_document_admin_assets' );

/**
 * Komunikat, gdy dokument nie ma pliku.
 */
function trrol_document_missing_file_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'trrol_dokument' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$post = get_post();
	if ( $post && 'auto-draft' !== $post->post_status && ! trrol_document_file( $post->ID ) ) {
		echo '<div class="notice notice-warning"><p><strong>Ten dokument nie ma pliku.</strong> Wybierz plik w sekcji „Plik do pobrania” — bez niego dokument nie pojawi się na stronie.</p></div>';
	}
}
add_action( 'admin_notices', 'trrol_document_missing_file_notice' );

/**
 * Kolumny na liście dokumentów.
 */
add_filter( 'manage_trrol_dokument_posts_columns', function ( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['trrol_plik']      = 'Plik';
			$new['trrol_kolejnosc'] = 'Kolejność';
		}
	}
	return $new;
} );

add_action( 'manage_trrol_dokument_posts_custom_column', function ( $column, $post_id ) {
	if ( 'trrol_plik' === $column ) {
		$file = trrol_document_file( $post_id );
		if ( $file ) {
			printf(
				'<a href="%s" target="_blank" rel="noopener">%s</a><br><span style="color:#646970">%s</span>',
				esc_url( $file['url'] ),
				esc_html( wp_basename( get_attached_file( $file['id'] ) ) ),
				esc_html( trim( $file['ext'] . ' · ' . $file['size'], ' ·' ) )
			);
		} else {
			echo '<span style="color:#b32d2e;font-weight:600">brak pliku</span>';
		}
	}
	if ( 'trrol_kolejnosc' === $column ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}, 10, 2 );

/**
 * Lista w kokpicie i na stronie — w ustalonej kolejności.
 */
function trrol_document_order( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	$is_admin_list = is_admin() && 'trrol_dokument' === $query->get( 'post_type' ) && ! $query->get( 'orderby' );
	$is_archive    = ! is_admin() && $query->is_post_type_archive( 'trrol_dokument' );

	if ( $is_admin_list || $is_archive ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $is_archive ) {
		$query->set( 'posts_per_page', -1 );
	}
}
add_action( 'pre_get_posts', 'trrol_document_order' );

/**
 * Pojedynczy dokument nie ma własnej strony — adres prowadzi do pliku albo do listy.
 */
function trrol_document_single_redirect() {
	if ( ! is_singular( 'trrol_dokument' ) ) {
		return;
	}
	$file = trrol_document_file( get_queried_object_id() );
	wp_safe_redirect( $file ? $file['url'] : trrol_archive_url( 'trrol_dokument' ), 302 );
	exit;
}
add_action( 'template_redirect', 'trrol_document_single_redirect', 3 );

/**
 * Pojedyncze dokumenty nie trafiają do mapy strony — to tylko przekierowania do plików.
 */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['trrol_dokument'] );
	return $types;
} );
