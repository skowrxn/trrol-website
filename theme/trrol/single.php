<?php
/**
 * Wpis pojedynczy — wspólny szablon dla wszystkich typów treści.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/entry', 'single' );
}

get_footer();
