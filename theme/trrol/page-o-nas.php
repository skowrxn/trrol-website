<?php
/**
 * Strona: O nas.
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

$rok = trrol_opt( 'rok_zalozenia' );
?>

<main>
	<section class="container section--first">
		<h1 class="h-page" style="max-width:680px">Od <?php echo esc_html( $rok ); ?> r. zarządzamy<br>nieruchomościami prywatnymi</h1>
		<div class="about-hero__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'trrol-hero', array( 'alt' => 'Elewacja budynku w zarządzie od strony ulicy' ) ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( trrol_img( 'budynek-2.jpg' ) ); ?>" alt="Elewacja budynku w zarządzie od strony ulicy">
			<?php endif; ?>
		</div>
	</section>

	<section class="container section--tight">
		<div class="about-layout">
			<div class="about-layout__body entry__body" style="border-top:0;padding-top:0;margin-top:0;max-width:none">
				<?php the_content(); ?>
			</div>

			<aside class="sidebar">
				<div class="panel panel--lg">
					<h2 class="panel__title">Dane spółki</h2>
					<div class="company-facts">
						<div>
							<span class="company-facts__k">Nazwa</span>
							<span class="company-facts__v"><?php echo esc_html( trrol_opt( 'firma' ) ); ?></span>
						</div>
						<div>
							<span class="company-facts__k">Siedziba</span>
							<span class="company-facts__v"><?php echo esc_html( trrol_opt( 'ulica' ) ); ?><br><?php echo esc_html( trrol_opt( 'miasto' ) ); ?></span>
						</div>
						<div>
							<span class="company-facts__k">NIP · REGON</span>
							<span class="company-facts__v"><?php echo esc_html( trrol_opt( 'nip' ) ); ?> · <?php echo esc_html( trrol_opt( 'regon' ) ); ?></span>
						</div>
						<div>
							<span class="company-facts__k">KRS</span>
							<span class="company-facts__v"><?php echo esc_html( trrol_opt( 'krs' ) ); ?></span>
						</div>
						<div>
							<span class="company-facts__k">Kontakt</span>
							<a class="company-facts__v" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_sekretariat' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_sekretariat' ) ); ?></a>
							<a class="company-facts__v" href="mailto:<?php echo esc_attr( trrol_opt( 'email' ) ); ?>"><?php echo esc_html( trrol_opt( 'email' ) ); ?></a>
						</div>
					</div>
				</div>
			</aside>
		</div>
	</section>

	<section class="band band--tight">
		<div class="band__grid">
			<div>
				<h2>Szukasz zarządcy<br>dla swojej kamienicy?</h2>
				<p class="band__lead">Porozmawiajmy o stanie budynku, sytuacji lokatorskiej i planach na najbliższe lata. Przygotujemy propozycję zakresu współpracy.</p>
			</div>
			<div class="band__actions">
				<a class="btn btn--white" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Skontaktuj się z nami</a>
				<a class="btn btn--ghost-light" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_sekretariat' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_sekretariat' ) ); ?></a>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
