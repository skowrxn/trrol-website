<?php
/**
 * Template Name: Regulaminy
 *
 * Strona regulaminu z listą dokumentów w kolumnie bocznej.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) {
	the_post();
}

$current = get_post();

/* Strona nadrzędna „Regulaminy” — dokumenty to jej strony podrzędne. */
$root_id = $current->post_parent ? $current->post_parent : $current->ID;
$root    = get_post( $root_id );

$docs = get_pages( array(
	'child_of'    => $root_id,
	'parent'      => $root_id,
	'sort_column' => 'menu_order,post_title',
) );

/* Wejście na stronę nadrzędną pokazuje pierwszy dokument. */
$showing = $current;
if ( $current->ID === $root_id && $docs ) {
	$showing = $docs[0];
}

$pdf_map = array(
	'regulamin-uzytkowania-lokali' => trrol_opt( 'pdf_regulamin' ),
	'rozliczanie-wody-i-sciekow'   => trrol_opt( 'pdf_woda' ),
);
$showing_pdf = isset( $pdf_map[ $showing->post_name ] ) ? $pdf_map[ $showing->post_name ] : '';
?>

<main>
	<section class="container" style="padding-top:56px">
		<?php
		$crumbs = array( array( 'label' => $root->post_title, 'url' => $current->ID === $root_id ? '' : get_permalink( $root ) ) );
		if ( $current->ID !== $root_id ) {
			$crumbs[] = array( 'label' => $showing->post_title );
		}
		trrol_breadcrumbs( $crumbs );
		?>

		<div class="docs-layout">
			<aside class="docs-nav">
				<p class="docs-nav__label">Dokumenty</p>
				<div class="docs-nav__list">
					<?php foreach ( $docs as $doc ) : ?>
						<?php
						$active = ( $doc->ID === $showing->ID );
						$desc   = $doc->post_excerpt ? $doc->post_excerpt : '';
						?>
						<a class="docs-nav__item<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_permalink( $doc ) ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
							<span class="docs-nav__name"><?php echo esc_html( $doc->post_title ); ?></span>
							<?php if ( $desc ) : ?>
								<span class="docs-nav__desc"><?php echo esc_html( $desc ); ?></span>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>

				<?php if ( trrol_opt( 'pdf_regulamin' ) || trrol_opt( 'pdf_woda' ) ) : ?>
					<div class="docs-nav__dl">
						<p class="docs-nav__label" style="padding-bottom:8px">Do pobrania</p>
						<?php if ( trrol_opt( 'pdf_regulamin' ) ) : ?>
							<p style="margin-bottom:8px"><a href="<?php echo esc_url( trrol_opt( 'pdf_regulamin' ) ); ?>" download>Regulamin użytkowania lokali (PDF)</a></p>
						<?php endif; ?>
						<?php if ( trrol_opt( 'pdf_woda' ) ) : ?>
							<p><a href="<?php echo esc_url( trrol_opt( 'pdf_woda' ) ); ?>" download>Rozliczanie wody i ścieków (PDF)</a></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</aside>

			<article class="doc-body">
				<h1><?php echo esc_html( $showing->post_title ); ?></h1>
				<?php if ( $showing->post_excerpt ) : ?>
					<p class="doc-body__intro"><?php echo esc_html( $showing->post_excerpt ); ?></p>
				<?php endif; ?>

				<?php if ( $showing_pdf ) : ?>
					<div class="doc-body__dl" style="margin-top:24px;padding-top:0;border-top:0">
						<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $showing_pdf ); ?>" download>Pobierz PDF</a>
					</div>
				<?php endif; ?>

				<?php echo apply_filters( 'the_content', $showing->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</article>
		</div>
	</section>

	<div class="section--last"></div>
</main>

<?php
get_footer();
