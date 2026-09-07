<?php
/**
 * Strona główna.
 *
 * @package trrol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$trrol_ogloszenie = get_posts( array( 'post_type' => 'trrol_ogloszenie', 'posts_per_page' => 1 ) );
$trrol_lokale     = get_posts( array( 'post_type' => 'trrol_lokal', 'posts_per_page' => 3 ) );
$trrol_oferty     = trrol_featured_oferty( 2 );
$trrol_aktualnosc = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );
?>

<main>

	<section class="hero">
		<div class="hero__media">
			<img src="<?php echo esc_url( trrol_img( 'budynek-1.jpg' ) ); ?>" alt="Budynek mieszkalny po termomodernizacji, ujęcie z ulicy" width="1448" height="772" fetchpriority="high" decoding="async">
		</div>
		<div class="hero__scrim"></div>
		<div class="hero__inner">
			<h1 class="h-display hero__title">Zarządzamy nieruchomościami</h1>
			<p class="hero__lead">Kompleksowa administracja nieruchomości prywatnych w Siemianowicach Śląskich i okolicach. Od <?php echo esc_html( trrol_opt( 'rok_zalozenia' ) ); ?> roku.</p>
			<div class="hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url( trrol_archive_url( 'trrol_lokal' ) ); ?>">Zobacz wolne lokale</a>
				<a class="btn btn--ghost-light" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Skontaktuj się z administracją</a>
			</div>
		</div>
	</section>

	<section class="container">
		<div class="paths">
			<a class="path-card" href="<?php echo esc_url( trrol_archive_url( 'trrol_ogloszenie' ) ); ?>">
				<?php echo trrol_icon( 'dom' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 class="path-card__title">Jestem mieszkańcem</h2>
				<p class="path-card__text">Ogłoszenia administracji, czynsze, zgłoszenie awarii i regulamin do pobrania.</p>
				<span class="path-card__cta">Ogłoszenia dla mieszkańców →</span>
			</a>
			<a class="path-card" href="<?php echo esc_url( trrol_archive_url( 'trrol_lokal' ) ); ?>">
				<?php echo trrol_icon( 'lupa' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 class="path-card__title">Szukam lokalu</h2>
				<p class="path-card__text">Wykaz wolnych lokali mieszkalnych i użytkowych z aktualnymi metrażami.</p>
				<span class="path-card__cta">Wolne lokale →</span>
			</a>
			<a class="path-card" href="<?php echo esc_url( trrol_archive_url( 'trrol_oferta' ) ); ?>">
				<?php echo trrol_icon( 'narzedzia' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 class="path-card__title">Jestem firmą remontową</h2>
				<p class="path-card__text">Aktualne zapytania ofertowe na prace remontowe i konserwacyjne.</p>
				<span class="path-card__cta">Oferty dla firm →</span>
			</a>
		</div>
	</section>

	<section class="container section">
		<div class="highlight-row">
			<?php if ( $trrol_ogloszenie ) : ?>
				<?php $trrol_o = $trrol_ogloszenie[0]; ?>
				<a class="notice-card" href="<?php echo esc_url( get_permalink( $trrol_o ) ); ?>">
					<span class="card__meta"><?php echo esc_html( trrol_entry_meta( $trrol_o ) ); ?></span>
					<span class="notice-card__title"><?php echo esc_html( get_the_title( $trrol_o ) ); ?></span>
					<span class="notice-card__text"><?php echo esc_html( trrol_card_excerpt( $trrol_o, 40 ) ); ?></span>
					<span class="notice-card__foot">
						<span class="link-arrow">Czytaj całość →</span>
						<span class="card__foot-note">Wszystkie ogłoszenia</span>
					</span>
				</a>
			<?php else : ?>
				<div class="notice-card">
					<span class="card__meta">Ogłoszenia</span>
					<span class="notice-card__title">Brak bieżących ogłoszeń</span>
					<span class="notice-card__text">W tym miejscu publikujemy komunikaty dla mieszkańców — terminy odczytów, planowane prace i sprawy porządkowe.</span>
					<span class="notice-card__foot">
						<a class="link-arrow" href="<?php echo esc_url( trrol_archive_url( 'trrol_ogloszenie' ) ); ?>">Wszystkie ogłoszenia →</a>
					</span>
				</div>
			<?php endif; ?>

			<?php trrol_docs_card(); ?>
		</div>
	</section>

	<section class="container section">
		<div class="section-head">
			<h2 class="h-section">Wolne lokale</h2>
			<a class="link-arrow" href="<?php echo esc_url( trrol_archive_url( 'trrol_lokal' ) ); ?>">Cały wykaz →</a>
		</div>

		<?php if ( $trrol_lokale ) : ?>
			<div class="grid grid--auto mt-40">
				<?php foreach ( $trrol_lokale as $trrol_post ) : ?>
					<?php trrol_render_card( $trrol_post ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="empty">
				<span class="empty__icon"><?php echo trrol_icon( 'dom-pusty' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Obecnie nie dysponujemy wolnymi lokalami</p>
				<p class="empty__text">Lokale zwalniają się nieregularnie. Zostaw kontakt do siebie, a odezwiemy się, gdy pojawi się nowa oferta.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Zostaw kontakt</a>
				</div>
			</div>
		<?php endif; ?>

		<?php trrol_legal_disclaimer(); ?>
	</section>

	<section class="container section">
		<div class="section-head">
			<h2 class="h-section">Oferty dla firm</h2>
			<a class="link-arrow" href="<?php echo esc_url( trrol_archive_url( 'trrol_oferta' ) ); ?>">Wszystkie zlecenia →</a>
		</div>

		<?php if ( $trrol_oferty ) : ?>
			<div class="grid grid--2 mt-40">
				<?php foreach ( $trrol_oferty as $trrol_post ) : ?>
					<?php trrol_render_card( $trrol_post ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="empty">
				<span class="empty__icon"><?php echo trrol_icon( 'narzedzia' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<p class="empty__title">Nie prowadzimy teraz naboru ofert</p>
				<p class="empty__text">Zapytania ofertowe na prace remontowe publikujemy tutaj na bieżąco. Zajrzyj za jakiś czas albo napisz do nas.</p>
				<div class="empty__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Napisz do nas</a>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<section class="container section">
		<div class="about-split">
			<div class="about-split__media">
				<img src="<?php echo esc_url( trrol_img( 'budynek-2.jpg' ) ); ?>" alt="Elewacja budynku w zarządzie od strony ulicy" width="1448" height="772" loading="lazy" decoding="async">
			</div>
			<div class="about-split__body">
				<h2 class="h-section">O nas</h2>
				<p>Od <?php echo esc_html( trrol_opt( 'rok_zalozenia' ) ); ?> r. specjalizujemy się w kompleksowej administracji i zarządzaniu nieruchomościami stanowiącymi własność prywatną. Naszą główną specjalizacją jest zarządzanie <strong>kamienicami i budynkami mieszkalnymi</strong>, w tym nieruchomościami wymagającymi remontów, modernizacji i bieżącego nadzoru technicznego.</p>
				<p>Naszym zadaniem jest <strong>odciążenie właściciela nieruchomości z codziennych obowiązków</strong> związanych z jej utrzymaniem i zarządzaniem.</p>
				<div class="stat">
					<p class="stat__value">od <?php echo esc_html( trrol_opt( 'rok_zalozenia' ) ); ?> r.</p>
					<p class="stat__label">Zarządzamy nieruchomościami</p>
				</div>
				<p style="margin-top:32px"><a class="link-arrow" href="<?php echo esc_url( home_url( '/o-nas/' ) ); ?>">Poznaj naszą firmę →</a></p>
			</div>
		</div>
	</section>

	<section class="container section">
		<h2 class="h-section">Zakres usług</h2>
		<div class="services mt-40">
			<?php foreach ( trrol_services() as $trrol_service ) : ?>
				<div class="service">
					<?php echo trrol_icon( $trrol_service['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3 class="service__title"><?php echo esc_html( $trrol_service['title'] ); ?></h3>
					<p class="service__text"><?php echo esc_html( $trrol_service['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="band">
		<div class="band__grid">
			<h2>Kontakt<br>z administracją</h2>
			<div class="contact-stack">
				<div class="contact-stack__item">
					<span class="contact-stack__label">Sekretariat</span>
					<a class="contact-stack__value" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_sekretariat' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_sekretariat' ) ); ?></a>
				</div>
				<div class="contact-stack__item">
					<span class="contact-stack__label">E-mail</span>
					<a class="contact-stack__value contact-stack__value--sm" href="mailto:<?php echo esc_attr( trrol_opt( 'email' ) ); ?>"><?php echo esc_html( trrol_opt( 'email' ) ); ?></a>
				</div>
				<div class="contact-stack__item">
					<span class="contact-stack__label">Biuro</span>
					<span class="contact-stack__value contact-stack__value--xs"><?php echo esc_html( trrol_opt( 'ulica' ) . ', ' . trrol_opt( 'miasto' ) ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<section class="container section">
		<div class="section-head">
			<h2 class="h-section">Aktualności</h2>
			<a class="link-arrow" href="<?php echo esc_url( trrol_archive_url( 'post' ) ); ?>">Wszystkie aktualności →</a>
		</div>

		<?php if ( $trrol_aktualnosc ) : ?>
			<div class="grid grid--3 mt-40">
				<?php foreach ( $trrol_aktualnosc as $trrol_post ) : ?>
					<?php trrol_render_card( $trrol_post ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="lead lead--mt">Wkrótce pojawią się tu informacje o pracach na budynkach i sprawach firmy.</p>
		<?php endif; ?>
	</section>

	<section class="container section">
		<div class="faq-layout">
			<h2 class="h-section">Najczęstsze<br>pytania</h2>
			<div>
				<?php foreach ( trrol_faq_items() as $trrol_i => $trrol_faq ) : ?>
					<?php $trrol_open = ( 0 === $trrol_i ); ?>
					<div class="faq-item">
						<button
							class="faq-item__q"
							type="button"
							aria-expanded="<?php echo $trrol_open ? 'true' : 'false'; ?>"
							aria-controls="faq-a-<?php echo (int) $trrol_i; ?>">
							<span><?php echo esc_html( $trrol_faq['q'] ); ?></span>
							<span class="faq-item__icon" aria-hidden="true"><?php echo $trrol_open ? '−' : '+'; ?></span>
						</button>
						<p class="faq-item__a<?php echo $trrol_open ? ' is-open' : ''; ?>" id="faq-a-<?php echo (int) $trrol_i; ?>">
							<?php echo esc_html( $trrol_faq['a'] ); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="container section section--last">
		<div class="office">
			<div class="office__map">
				<iframe
					src="<?php echo esc_url( trrol_opt( 'mapa_embed' ) ); ?>"
					title="Mapa — biuro TRROL, <?php echo esc_attr( trrol_opt( 'ulica' ) ); ?>"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					allowfullscreen></iframe>
			</div>
			<div class="office__body">
				<h2 class="h-sub">Biuro i godziny otwarcia</h2>
				<?php trrol_hours_table(); ?>
				<div class="office__contacts">
					<div>
						<span class="office__label">Telefon</span>
						<a class="office__value" href="<?php echo esc_attr( trrol_tel_href( trrol_opt( 'tel_sekretariat' ) ) ); ?>"><?php echo esc_html( trrol_opt( 'tel_sekretariat' ) ); ?></a>
					</div>
					<div>
						<span class="office__label">Faks</span>
						<span class="office__value"><?php echo esc_html( trrol_opt( 'faks' ) ); ?></span>
					</div>
				</div>
				<div class="office__actions">
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Wszystkie kontakty</a>
					<a class="btn btn--outline btn--sm" href="<?php echo esc_url( home_url( '/kontakt/#formularz' ) ); ?>">Napisz do nas</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
