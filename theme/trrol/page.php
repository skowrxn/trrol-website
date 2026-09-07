<?php
/**
 * Strona statyczna.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main>
		<section class="container section--first section--last">
			<?php if ( ! is_front_page() ) : ?>
				<?php trrol_breadcrumbs(); ?>
			<?php endif; ?>

			<h1 class="h-page" style="margin-top:24px"><?php the_title(); ?></h1>

			<div class="entry__body" style="border-top:0;padding-top:24px;margin-top:0">
				<?php the_content(); ?>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
