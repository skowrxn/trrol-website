<?php
/**
 * Przykładowe wpisy zgodne z zaakceptowaną makietą — tworzone jako SZKICE.
 * Uruchamiane przez: wp eval-file seed.php
 */

$entries = array(

	// ----------------------------------------------------------- Wolne lokale
	array(
		'type'    => 'trrol_lokal',
		'title'   => 'ul. Michałkowicka 24/5 — mieszkanie 41,80 m²',
		'excerpt' => 'Mieszkanie dwupokojowe o powierzchni 41,80 m² na II piętrze kamienicy w Siemianowicach Śląskich, dostępne do wynajęcia od zaraz.',
		'content' => '<p>Mieszkanie składa się z dwóch pokoi, kuchni i łazienki. Kamienica jest po remoncie dachu oraz klatki schodowej, ogrzewanie etażowe gazowe. Lokal wymaga odświeżenia we własnym zakresie — uwzględniamy to w warunkach najmu.</p>
<p>Budynek stoi blisko przystanku autobusowego i sklepów. Preferujemy najem długoterminowy.</p>
<h2>Najważniejsze informacje</h2>
<ol>
<li>powierzchnia 41,80 m², dwa pokoje, II piętro,</li>
<li>ogrzewanie etażowe gazowe, własny piec w lokalu,</li>
<li>kamienica po remoncie dachu i klatki schodowej,</li>
<li>warunki najmu ustalamy indywidualnie przy kontakcie.</li>
</ol>',
		'meta'    => array(
			'_trrol_powierzchnia' => '41,80 m²',
			'_trrol_uklad'        => '2 pokoje, II piętro',
			'_trrol_budynek'      => 'Michałkowicka 24',
			'_trrol_rodzaj'       => 'mieszkalny',
			'_trrol_dostepnosc'   => 'od zaraz',
		),
	),
	array(
		'type'    => 'trrol_lokal',
		'title'   => 'ul. Powstańców 11/2 — kawalerka 28,50 m²',
		'excerpt' => 'Kawalerka na parterze z osobną kuchnią i łazienką, w budynku podłączonym do sieci ciepłowniczej.',
		'content' => '<p>Lokal na parterze, z osobną kuchnią i łazienką. Budynek podłączony do miejskiej sieci ciepłowniczej, co upraszcza rozliczenia za ogrzewanie.</p>
<p>Możliwość wynajmu od zaraz, preferowany najem długoterminowy.</p>
<h2>Najważniejsze informacje</h2>
<ol>
<li>powierzchnia 28,50 m², parter,</li>
<li>ogrzewanie z sieci ciepłowniczej,</li>
<li>osobna kuchnia i łazienka,</li>
<li>warunki najmu ustalamy indywidualnie przy kontakcie.</li>
</ol>',
		'meta'    => array(
			'_trrol_powierzchnia' => '28,50 m²',
			'_trrol_uklad'        => '1 pokój, parter',
			'_trrol_budynek'      => 'Powstańców 11',
			'_trrol_rodzaj'       => 'mieszkalny',
			'_trrol_dostepnosc'   => 'od zaraz',
		),
	),
	array(
		'type'    => 'trrol_lokal',
		'title'   => 'ul. Śląska 62 — lokal użytkowy 63,00 m²',
		'excerpt' => 'Lokal użytkowy na parterze kamienicy, z witryną wystawową i osobnym wejściem od ulicy.',
		'content' => '<p>Lokal na parterze kamienicy, z witryną wystawową i osobnym wejściem od ulicy. Do dyspozycji zaplecze socjalne i toaleta.</p>
<p>Odpowiedni pod usługi, biuro lub handel. Lokal jest obecnie w rezerwacji — w sprawie dostępności prosimy o kontakt z administracją.</p>
<h2>Najważniejsze informacje</h2>
<ol>
<li>powierzchnia 63,00 m², parter,</li>
<li>witryna wystawowa i osobne wejście od ulicy,</li>
<li>zaplecze socjalne i toaleta,</li>
<li>warunki najmu ustalamy indywidualnie przy kontakcie.</li>
</ol>',
		'meta'    => array(
			'_trrol_powierzchnia' => '63,00 m²',
			'_trrol_uklad'        => 'parter, witryna od ulicy',
			'_trrol_budynek'      => 'Śląska 62',
			'_trrol_rodzaj'       => 'uzytkowy',
			'_trrol_dostepnosc'   => 'w rezerwacji',
		),
	),

	// ------------------------------------------------------- Oferty dla firm
	array(
		'type'    => 'trrol_oferta',
		'title'   => 'Malowanie klatki schodowej — ul. Michałkowicka 24',
		'excerpt' => 'Przygotowanie podłoża, gruntowanie i malowanie klatki schodowej (4 kondygnacje, ok. 420 m² ścian i sufitów) oraz lakierowanie balustrady.',
		'content' => '<p>Zapraszamy firmy remontowe do składania ofert na malowanie klatki schodowej w budynku przy ul. Michałkowickiej 24.</p>
<h2>Zakres prac</h2>
<ol>
<li>przygotowanie podłoża — uzupełnienie ubytków, szpachlowanie, szlifowanie,</li>
<li>gruntowanie ścian i sufitów,</li>
<li>dwukrotne malowanie farbą lateksową (4 kondygnacje, ok. 420 m²),</li>
<li>oczyszczenie i lakierowanie balustrady schodowej,</li>
<li>uporządkowanie terenu i wywóz odpadów po pracach.</li>
</ol>
<h2>Wymagania</h2>
<ol>
<li>doświadczenie w pracach malarskich w budynkach mieszkalnych,</li>
<li>ubezpieczenie OC działalności,</li>
<li>gotowość prowadzenia prac bez wyłączania klatki z użytkowania,</li>
<li>oferta powinna zawierać cenę netto oraz proponowany termin realizacji.</li>
</ol>',
		'meta'    => array(
			'_trrol_numer'   => '08/2026',
			'_trrol_termin'  => '2026-09-15',
			'_trrol_status'  => 'otwarte',
			'_trrol_budynek' => 'Michałkowicka 24',
		),
	),
	array(
		'type'    => 'trrol_oferta',
		'title'   => 'Wykonanie instalacji elektrycznej w częściach wspólnych — ul. Śląska 62',
		'excerpt' => 'Wymiana instalacji na klatce schodowej i w piwnicach, montaż nowej rozdzielni, pomiary i protokoły odbiorowe.',
		'content' => '<p>Poszukujemy wykonawcy wymiany instalacji elektrycznej w częściach wspólnych budynku przy ul. Śląskiej 62.</p>
<h2>Zakres prac</h2>
<ol>
<li>demontaż starej instalacji na klatce schodowej i w piwnicach,</li>
<li>ułożenie nowej instalacji wraz z osprzętem i oprawami,</li>
<li>montaż nowej rozdzielni z zabezpieczeniami różnicowoprądowymi,</li>
<li>pomiary powykonawcze i protokoły odbiorowe.</li>
</ol>
<h2>Wymagania</h2>
<ol>
<li>uprawnienia SEP do 1 kV (eksploatacja i dozór),</li>
<li>ubezpieczenie OC działalności,</li>
<li>oferta powinna zawierać cenę netto oraz proponowany termin realizacji.</li>
</ol>',
		'meta'    => array(
			'_trrol_numer'   => '07/2026',
			'_trrol_termin'  => '2026-09-08',
			'_trrol_status'  => 'otwarte',
			'_trrol_budynek' => 'Śląska 62',
		),
	),
	array(
		'type'    => 'trrol_oferta',
		'title'   => 'Remont pokrycia dachowego — ul. Powstańców 11',
		'excerpt' => 'Wymiana pokrycia, obróbki blacharskie, rynny i rury spustowe. Nabór ofert zakończony.',
		'content' => '<p>Nabór ofert na remont pokrycia dachowego przy ul. Powstańców 11 został zakończony. Dziękujemy wszystkim firmom za złożone oferty.</p>
<h2>Zakres prac</h2>
<ol>
<li>demontaż starego pokrycia,</li>
<li>wymiana uszkodzonych elementów więźby,</li>
<li>nowe pokrycie wraz z obróbkami blacharskimi,</li>
<li>wymiana rynien i rur spustowych.</li>
</ol>',
		'meta'    => array(
			'_trrol_numer'   => '06/2026',
			'_trrol_termin'  => '2026-07-30',
			'_trrol_status'  => 'zamkniete',
			'_trrol_budynek' => 'Powstańców 11',
		),
	),

	// ------------------------------------------------------------ Ogłoszenia
	array(
		'type'    => 'trrol_ogloszenie',
		'title'   => 'Deratyzacja budynków — 8 i 9 września',
		'excerpt' => 'W dniach 8–9 września w budynkach przy ul. Michałkowickiej 24 i ul. Powstańców 11 zostanie przeprowadzona akcja deratyzacyjna.',
		'content' => '<p>Preparaty zostaną wyłożone w piwnicach, pomieszczeniach gospodarczych i studzienkach kanalizacyjnych. Prace prowadzi firma z uprawnieniami w zakresie deratyzacji, na podstawie umowy z administracją.</p>
<h2>O czym pamiętać</h2>
<ol>
<li>nie przesuwaj i nie usuwaj wyłożonych preparatów,</li>
<li>dopilnuj, aby dzieci i zwierzęta nie miały dostępu do piwnic,</li>
<li>zabezpiecz artykuły spożywcze przechowywane w pomieszczeniach gospodarczych,</li>
<li>o zauważonych nieprawidłowościach informuj administrację.</li>
</ol>',
		'meta'    => array( '_trrol_kategoria' => 'Ogłoszenia', '_trrol_budynek' => 'Michałkowicka 24, Powstańców 11' ),
	),
	array(
		'type'    => 'trrol_ogloszenie',
		'title'   => 'Odczyt liczników wody — do 31 sierpnia',
		'excerpt' => 'Przypominamy o obowiązku przesłania zdjęcia stanu wodomierza do końca miesiąca.',
		'content' => '<p>Prosimy o przesłanie zdjęcia wodomierza na adres biuro@trrol.pl do 31 sierpnia. Zdjęcie musi obejmować numer licznika oraz czytelny stan.</p>
<p>Brak odczytu oznacza rozliczenie ryczałtowe zgodnie z regulaminem rozliczania wody i ścieków.</p>',
		'meta'    => array( '_trrol_kategoria' => 'Liczniki' ),
	),
	array(
		'type'    => 'trrol_ogloszenie',
		'title'   => 'Wymiana instalacji elektrycznej na klatce — ul. Śląska 62',
		'excerpt' => 'Od 18 sierpnia rozpoczynamy wymianę instalacji na klatce schodowej.',
		'content' => '<p>W godzinach 8:00–15:00 możliwe są krótkie przerwy w dostawie prądu w częściach wspólnych. Instalacja w lokalach pracuje normalnie.</p>
<p>Prosimy o nieblokowanie ciągów komunikacyjnych na czas prac.</p>',
		'meta'    => array( '_trrol_kategoria' => 'Prace', '_trrol_budynek' => 'Śląska 62' ),
	),
	array(
		'type'    => 'trrol_ogloszenie',
		'title'   => 'Zapisy na miejsca parkingowe',
		'excerpt' => 'Osoby zainteresowane rezerwacją miejsca na nowo powstającym parkingu prosimy o zgłaszanie się do administracji.',
		'content' => '<p>Zgłoszenia przyjmujemy do 15 września — telefonicznie, mailowo lub osobiście w biurze przy ul. Śląskiej 80.</p>
<p>O przydziale miejsc decyduje kolejność zgłoszeń mieszkańców budynku.</p>',
		'meta'    => array( '_trrol_kategoria' => 'Parking' ),
	),

	// ----------------------------------------------------------- Aktualności
	array(
		'type'     => 'post',
		'title'    => 'Zakończyliśmy remont dachu przy ul. Michałkowickiej 24',
		'excerpt'  => 'Prace obejmowały wymianę pokrycia, obróbek blacharskich oraz rynien.',
		'content'  => '<p>Zakończyliśmy remont pokrycia dachowego w kamienicy przy ul. Michałkowickiej 24. Prace obejmowały wymianę pokrycia, obróbek blacharskich oraz rynien i rur spustowych.</p>
<p>Budynek jest zabezpieczony przed kolejnym sezonem jesienno-zimowym. Dziękujemy mieszkańcom za cierpliwość w trakcie prowadzenia prac.</p>',
		'category' => 'Remonty',
	),
	array(
		'type'     => 'post',
		'title'    => 'Nowy parking przed budynkiem przy ul. Powstańców',
		'excerpt'  => 'Ruszyły prace nad utwardzeniem terenu i wyznaczeniem miejsc postojowych dla mieszkańców.',
		'content'  => '<p>Rozpoczęliśmy utwardzanie terenu przed budynkiem przy ul. Powstańców i wyznaczanie miejsc postojowych dla mieszkańców.</p>
<p>Zakończenie prac planujemy na koniec września. Osoby zainteresowane rezerwacją miejsca prosimy o kontakt z administracją.</p>',
		'category' => 'Inwestycje',
	),
	array(
		'type'     => 'post',
		'title'    => 'Rozliczenie mediów za pierwsze półrocze',
		'excerpt'  => 'Rozliczenia zostały wysłane do wszystkich najemców.',
		'content'  => '<p>Rozliczenia mediów za pierwsze półrocze zostały wysłane do wszystkich najemców. Uwzględniają rzeczywiste zużycie według wskazań wodomierzy oraz wniesione zaliczki.</p>
<p>W razie pytań zapraszamy do kontaktu z działem czynszów w godzinach pracy biura.</p>',
		'category' => 'Administracja',
	),
	array(
		'type'     => 'post',
		'title'    => 'Przejęliśmy w zarząd kolejną kamienicę w centrum miasta',
		'excerpt'  => 'Rozpoczynamy inwentaryzację techniczną budynku i planowanie prac remontowych.',
		'content'  => '<p>Rozszerzamy zakres zarządzanych nieruchomości o kolejną kamienicę w centrum Siemianowic Śląskich.</p>
<p>Rozpoczynamy inwentaryzację techniczną budynku i planowanie prac remontowych na najbliższe dwa lata, w uzgodnieniu z właścicielem nieruchomości.</p>',
		'category' => 'Firma',
	),
);

$created = 0;
$skipped = 0;

foreach ( $entries as $entry ) {
	$existing = get_page_by_title( $entry['title'], OBJECT, $entry['type'] );
	if ( $existing ) {
		$skipped++;
		continue;
	}

	$id = wp_insert_post( array(
		'post_type'    => $entry['type'],
		'post_status'  => 'draft',
		'post_title'   => $entry['title'],
		'post_excerpt' => $entry['excerpt'],
		'post_content' => $entry['content'],
	), true );

	if ( is_wp_error( $id ) ) {
		echo "BŁĄD: {$entry['title']} — " . $id->get_error_message() . "\n";
		continue;
	}

	if ( ! empty( $entry['meta'] ) ) {
		foreach ( $entry['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	if ( ! empty( $entry['category'] ) ) {
		$term = get_term_by( 'name', $entry['category'], 'category' );
		if ( $term ) {
			wp_set_post_terms( $id, array( $term->term_id ), 'category' );
		}
	}

	$created++;
	echo "szkic: {$entry['title']}\n";
}

echo "\nUtworzono szkiców: $created, pominięto istniejących: $skipped\n";
