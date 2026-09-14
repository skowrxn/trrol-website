/**
 * Treść poradnika. Styl: rzeczowy, prosty, bezosobowy.
 *
 * Eksportuje funkcję, która przyjmuje narzędzia do składania rysunków
 * i zwraca listę stron (sekcji) dokumentu.
 */
module.exports = function tresc({ rysunek, rysunekDwuczesciowy, logo }) {
  /**
   * Objaśnienia do znaczników na zrzucie.
   * Pozycja może być tekstem (numer kolejny) albo parą [numer, tekst],
   * gdy numeracja na zrzucie nie zaczyna się od 1.
   */
  const legenda = (pozycje) => {
    let n = 0;
    return `<ol class="legenda">${pozycje
      .map((p) => {
        const [nr, tekst] = Array.isArray(p) ? p : [++n, p];
        if (Array.isArray(p)) n = nr;
        return `<li data-n="${nr}">${tekst}</li>`;
      })
      .join('')}</ol>`;
  };

  const info = (t) => `<div class="box box--tip"><strong>Informacja.</strong> ${t}</div>`;
  const uwaga = (t) => `<div class="box box--warn"><strong>Uwaga.</strong> ${t}</div>`;

  const s = [];

  /* Okładka */
  s.push(`
<section class="okladka">
  <img class="okladka__logo" src="data:image/png;base64,${logo}" alt="TRROL Nieruchomości">
  <div class="okladka__srodek">
    <p class="okladka__nad">Instrukcja obsługi</p>
    <h1>Panel administracyjny<br>strony trrol.pl</h1>
    <p class="okladka__lead">Logowanie, dodawanie wpisów, zarządzanie plikami
      i zmiana ustawień serwisu.</p>
  </div>
  <div class="okladka__stopka">
    <div><strong>„TRROL” Nieruchomości Sp. z o.o.</strong><br>ul. Śląska 80, 41-100 Siemianowice Śląskie</div>
    <div class="okladka__data">Wersja 1.0</div>
  </div>
</section>`);

  /* Spis treści */
  s.push(`
<section>
  <h2 class="tytul-dzialu">Spis treści</h2>
  <ol class="spis">
    <li><span>Informacje ogólne</span><em>3</em></li>
    <li><span>Logowanie do panelu</span><em>4</em></li>
    <li><span>Kokpit i menu główne</span><em>5</em></li>
    <li><span>Rodzaje treści w serwisie</span><em>6</em></li>
    <li><span>Wolne lokale — lista wpisów</span><em>7</em></li>
    <li><span>Wolne lokale — dodawanie wpisu</span><em>8</em></li>
    <li><span>Wolne lokale — dane lokalu</span><em>9</em></li>
    <li><span>Oferty dla firm</span><em>10</em></li>
    <li><span>Ogłoszenia dla mieszkańców</span><em>11</em></li>
    <li><span>Aktualności</span><em>12</em></li>
    <li><span>Zdjęcia i pliki PDF</span><em>13</em></li>
    <li><span>Strony stałe</span><em>14</em></li>
    <li><span>Ustawienia TRROL — dane firmy i kontakt</span><em>15</em></li>
    <li><span>Ustawienia TRROL — godziny, dokumenty, Google</span><em>16</em></li>
    <li><span>Publikowanie i wycofywanie wpisów</span><em>17</em></li>
    <li><span>Najczęstsze problemy</span><em>18</em></li>
    <li><span>Zestawienie najważniejszych czynności</span><em>19</em></li>
  </ol>
</section>`);

  /* 1. Informacje ogólne */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 1</p>
  <h2 class="tytul-dzialu">Informacje ogólne</h2>

  <p>Serwis trrol.pl składa się z dwóch części: <strong>strony publicznej</strong>, dostępnej dla
  wszystkich odwiedzających, oraz <strong>panelu administracyjnego</strong>, w którym po zalogowaniu
  dodaje się i edytuje treści. Niniejsza instrukcja dotyczy panelu.</p>

  <p>Panel działa w przeglądarce internetowej i nie wymaga instalowania dodatkowego oprogramowania.</p>

  <h3>Adresy</h3>
  <table class="tab">
    <tr><th>Strona publiczna</th><td><code>trrol.pl</code></td></tr>
    <tr><th>Panel administracyjny</th><td><code>trrol.pl/wp-admin</code></td></tr>
  </table>

  <h3>Bezpieczeństwo zmian</h3>
  <p>Wprowadzane zmiany są w dużej mierze odwracalne:</p>
  <ul class="lista">
    <li>system zapisuje historię wersji każdego wpisu, dzięki czemu można przywrócić wcześniejszą treść,</li>
    <li>wpis zapisany jako <strong>szkic</strong> nie jest widoczny na stronie publicznej,</li>
    <li>usunięte wpisy trafiają do kosza, skąd można je przywrócić.</li>
  </ul>

  <h3>Podstawowe pojęcia</h3>
  <table class="tab tab--slownik">
    <tr><th>Wpis</th><td>Pojedyncza pozycja dodana do serwisu, np. jeden wolny lokal, jedno ogłoszenie lub jedna aktualność.</td></tr>
    <tr><th>Szkic</th><td>Wpis zapisany w panelu, niewidoczny na stronie publicznej.</td></tr>
    <tr><th>Opublikowany</th><td>Wpis widoczny na stronie publicznej.</td></tr>
    <tr><th>Zajawka</th><td>Krótkie streszczenie wpisu wyświetlane na listach i kafelkach.</td></tr>
    <tr><th>Media</th><td>Biblioteka zdjęć i plików PDF wgranych do serwisu.</td></tr>
  </table>
</section>`);

  /* 2. Logowanie */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 2</p>
  <h2 class="tytul-dzialu">Logowanie do panelu</h2>

  <p>Aby przejść do ekranu logowania, należy wpisać w pasku adresu przeglądarki
  <code>trrol.pl/wp-admin</code>.</p>

  ${rysunek('logowanie', 'Ekran logowania', { x: 480, y: 30, w: 700, h: 520 })}

  ${legenda([
    '<strong>Nazwa użytkownika lub adres e-mail</strong> — login przypisany do konta.',
    '<strong>Hasło</strong> — znaki hasła są ukrywane podczas wpisywania.',
    '<strong>Zapamiętaj mnie</strong> — po zaznaczeniu przeglądarka pozostaje zalogowana przez 14 dni. Opcję zaleca się stosować wyłącznie na komputerze służbowym.',
    '<strong>Zaloguj się</strong> — przejście do panelu.',
    '<strong>Nie pamiętasz hasła?</strong> — po podaniu adresu e-mail system wysyła wiadomość z odnośnikiem do ustawienia nowego hasła.',
  ])}

  ${info(`Po dłuższym okresie bezczynności sesja wygasa i system prosi o ponowne zalogowanie.`)}

  ${info(`Adres panelu warto dodać do zakładek przeglądarki.`)}
</section>`);

  /* 3. Kokpit */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 3</p>
  <h2 class="tytul-dzialu">Kokpit i menu główne</h2>

  <p>Po zalogowaniu wyświetla się <strong>Kokpit</strong>. Nawigacja po panelu odbywa się za pomocą
  menu po lewej stronie ekranu, dostępnego na każdej podstronie panelu.</p>

  ${rysunek('kokpit', 'Kokpit i menu główne', { x: 0, y: 0, w: 1600, h: 900 })}

  ${legenda([
    '<strong>Wolne lokale</strong> — wykaz mieszkań i lokali użytkowych do wynajęcia.',
    '<strong>Oferty dla firm</strong> — zapytania ofertowe na prace remontowe.',
    '<strong>Ogłoszenia</strong> — komunikaty dla mieszkańców.',
    '<strong>Wpisy</strong> — aktualności publikowane w serwisie.',
    '<strong>Strony</strong> — podstrony stałe, m.in. „O nas”, „Kontakt”, regulaminy.',
    '<strong>Media</strong> — biblioteka zdjęć i plików PDF.',
    '<strong>Ustawienia TRROL</strong> — dane kontaktowe, godziny otwarcia, dokumenty.',
    '<strong>Nazwa serwisu</strong> — otwiera stronę publiczną.',
    '<strong>TRROL — jak zarządzać stroną</strong> — skrót do najczęściej używanych sekcji.',
  ])}
</section>`);

  /* 4. Rodzaje treści */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 4</p>
  <h2 class="tytul-dzialu">Rodzaje treści w serwisie</h2>

  <p>Serwis zawiera cztery rodzaje wpisów. Każdy z nich ma własną podstronę z listą oraz własne
  miejsce na stronie głównej.</p>

  <table class="tab tab--rodzaje">
    <thead><tr><th>Rodzaj informacji</th><th>Sekcja w panelu</th><th>Odbiorcy</th></tr></thead>
    <tbody>
      <tr>
        <td>wolne mieszkanie lub lokal użytkowy do wynajęcia</td>
        <td><strong>Wolne lokale</strong></td>
        <td>osoby poszukujące lokalu</td>
      </tr>
      <tr>
        <td>poszukiwanie wykonawcy prac remontowych</td>
        <td><strong>Oferty dla firm</strong></td>
        <td>firmy remontowe i wykonawcy</td>
      </tr>
      <tr>
        <td>deratyzacja, przerwy w dostawie mediów, odczyty liczników</td>
        <td><strong>Ogłoszenia</strong></td>
        <td>mieszkańcy budynków</td>
      </tr>
      <tr>
        <td>zakończone remonty, inwestycje, informacje o firmie</td>
        <td><strong>Wpisy</strong> (aktualności)</td>
        <td>wszyscy odwiedzający</td>
      </tr>
    </tbody>
  </table>

  <h3>Wyświetlanie wpisów</h3>
  <p>Opublikowany wpis automatycznie pojawia się na odpowiedniej podstronie oraz na stronie głównej.
  Nie jest wymagana żadna dodatkowa konfiguracja.</p>

  <p>Jeżeli w danej sekcji nie ma opublikowanych wpisów, strona wyświetla stosowny komunikat,
  np. „Obecnie nie dysponujemy wolnymi lokalami”, wraz z odnośnikiem do kontaktu.</p>
</section>`);

  /* 5. Lista lokali */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 5</p>
  <h2 class="tytul-dzialu">Wolne lokale — lista wpisów</h2>

  <p>Po wybraniu z menu pozycji <strong>Wolne lokale</strong> wyświetla się lista wszystkich lokali —
  zarówno opublikowanych, jak i zapisanych jako szkice.</p>

  ${rysunek('lista-lokali', 'Lista wolnych lokali', { x: 0, y: 0, w: 1600, h: 560 })}

  ${legenda([
    '<strong>Dodaj wolny lokal</strong> — utworzenie nowego wpisu.',
    '<strong>Wszystkie / Szkice</strong> — filtrowanie listy według statusu.',
    '<strong>Tytuł</strong> — kliknięcie otwiera wpis do edycji.',
    '<strong>Powierzchnia, Układ</strong> — dane wprowadzone w polach lokalu.',
    '<strong>Szkic</strong> — oznaczenie wpisu, który nie jest widoczny na stronie publicznej.',
    '<strong>Edytuj / Szybka edycja / Przenieś do kosza / Podejrzyj</strong> — odnośniki widoczne po najechaniu kursorem na wiersz. „Podejrzyj” pokazuje wygląd wpisu na stronie.',
  ])}

  ${info(`Wpis przeniesiony do kosza można przywrócić. Trwałe usunięcie następuje dopiero
  po opróżnieniu kosza.`)}
</section>`);

  /* 6. Edytor lokalu */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 6</p>
  <h2 class="tytul-dzialu">Wolne lokale — dodawanie wpisu</h2>

  <p>Przycisk <strong>Dodaj wolny lokal</strong> otwiera edytor wpisu. W górnej części wpisuje się
  tytuł, poniżej treść.</p>

  ${rysunek('edytor-lokal', 'Edytor wpisu', { x: 0, y: 0, w: 1600, h: 900 })}

  ${legenda([
    '<strong>Tytuł</strong> — adres i metraż lokalu, np. <em>ul. Michałkowicka 24/5 — mieszkanie 41,80 m²</em>. Tytuł wyświetla się na liście lokali.',
    '<strong>Treść</strong> — opis lokalu: liczba pokoi, piętro, rodzaj ogrzewania, stan techniczny.',
    '<strong>Zapisz szkic</strong> — zapisuje wpis bez publikowania.',
    [5, '<strong>Opublikuj</strong> — udostępnia wpis na stronie publicznej.'],
    [6, '<strong>Ustawienia</strong> — pokazuje lub ukrywa panel boczny z ustawieniami wpisu (widoczny po prawej stronie).'],
  ])}

  <h3>Formatowanie tekstu</h3>
  <p>Klawisz Enter tworzy nowy akapit. Po zaznaczeniu fragmentu tekstu nad nim pojawia się pasek
  narzędzi umożliwiający m.in. pogrubienie, dodanie odnośnika lub utworzenie listy.</p>

  ${uwaga(`Przy wolnych lokalach nie podaje się ceny najmu. Warunki ustalane są indywidualnie.
  Pod każdym lokalem strona automatycznie wyświetla formularz kontaktowy oraz zastrzeżenie prawne.`)}
</section>`);

  /* 7. Dane lokalu */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 7</p>
  <h2 class="tytul-dzialu">Wolne lokale — dane lokalu</h2>

  <p>Poniżej treści wpisu znajdują się dwie sekcje z dodatkowymi polami. Dane z sekcji
  <strong>Dane wpisu</strong> wyświetlane są w tabeli obok opisu lokalu.</p>

  ${rysunek('dane-wpisu-lokal', 'Pola dodatkowe wolnego lokalu', { x: 250, y: 45, w: 1110, h: 890 })}

  ${legenda([
    '<strong>Powierzchnia</strong> — wraz z jednostką, np. <em>41,80 m²</em>.',
    '<strong>Układ</strong> — np. <em>2 pokoje, II piętro</em>.',
    '<strong>Dotyczy budynku</strong> — adres budynku, np. <em>Michałkowicka 24</em>. Pozwala powiązać wpisy dotyczące tego samego budynku.',
    '<strong>Rodzaj lokalu</strong> — lokal mieszkalny lub użytkowy.',
    '<strong>Dostępność</strong> — np. <em>od zaraz</em>, <em>w rezerwacji</em>.',
    '<strong>Tytuł w Google</strong> — pole opcjonalne; pozostawione puste przyjmuje tytuł wpisu.',
    '<strong>Opis w Google</strong> — pole opcjonalne; pozostawione puste przyjmuje początek treści wpisu.',
  ])}

  ${info(`Pola sekcji SEO (6 i 7) nie są wymagane. Serwis generuje tytuł i opis dla wyszukiwarek
  automatycznie.`)}
</section>`);

  /* 8. Oferty dla firm */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 8</p>
  <h2 class="tytul-dzialu">Oferty dla firm</h2>

  <p>Sekcja służy do publikowania zapytań ofertowych na prace remontowe i konserwacyjne.
  Wykonawcy składają oferty za pomocą formularza umieszczonego pod treścią zlecenia.</p>

  <p>Nowe zlecenie dodaje się przez <strong>Oferty dla firm → Dodaj zlecenie</strong>. Tytuł powinien
  określać rodzaj prac i budynek, a treść — szczegółowy zakres prac.</p>

  ${rysunek('dane-wpisu-oferta', 'Pola dodatkowe zlecenia', { x: 250, y: 40, w: 1110, h: 470 })}

  ${legenda([
    '<strong>Numer zlecenia</strong> — wewnętrzne oznaczenie, np. <em>08/2026</em>, wyświetlane przy tytule.',
    '<strong>Termin składania ofert</strong> — data wybierana z kalendarza. Po jej upływie zlecenie jest automatycznie oznaczane jako zamknięte, a formularz przestaje być dostępny.',
    '<strong>Status naboru</strong> — domyślnie „Otwarty nabór”. Opcja „Nabór zakończony” pozwala zamknąć zlecenie przed terminem.',
    '<strong>Dotyczy budynku</strong> — adres budynku, którego dotyczą prace.',
  ])}

  ${info(`Zlecenia po terminie pozostają widoczne na stronie z oznaczeniem „Nabór zakończony”.`)}

  <p>Oferty przesłane przez formularz trafiają na adres e-mail wskazany w <strong>Ustawieniach
  TRROL</strong>, wraz z załączonym plikiem PDF, jeśli został dołączony.</p>
</section>`);

  /* 9. Ogłoszenia */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 9</p>
  <h2 class="tytul-dzialu">Ogłoszenia dla mieszkańców</h2>

  <p>Sekcja służy do publikowania bieżących komunikatów, takich jak deratyzacja, odczyty liczników,
  planowane prace czy zapisy na miejsca parkingowe. <strong>Najnowsze ogłoszenie wyświetlane jest
  na stronie głównej.</strong></p>

  <p>Nowe ogłoszenie dodaje się przez <strong>Ogłoszenia → Dodaj ogłoszenie</strong>.</p>

  ${rysunek('dane-wpisu-ogloszenie', 'Pola dodatkowe ogłoszenia', { x: 250, y: 40, w: 1110, h: 400 })}

  ${legenda([
    '<strong>Etykieta</strong> — krótkie określenie tematu, np. <em>Liczniki</em>, <em>Prace</em>, <em>Parking</em>, wyświetlane obok daty. Bez wypełnienia wyświetla się etykieta „Ogłoszenia”.',
    '<strong>Dotyczy budynku</strong> — adres, jeżeli ogłoszenie dotyczy jednego budynku.',
    '<strong>Ważne do</strong> — opcjonalna data, po której ogłoszenie przestaje być aktualne.',
  ])}

  ${info(`Najważniejsze informacje (co, kiedy, gdzie) zaleca się umieszczać w pierwszym zdaniu
  ogłoszenia.`)}

  ${uwaga(`Ogłoszenia pozostają na stronie do momentu ich wycofania lub usunięcia. Nieaktualne
  ogłoszenia należy okresowo przenosić do kosza lub zmieniać ich status na szkic.`)}
</section>`);

  /* 10. Aktualności */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 10</p>
  <h2 class="tytul-dzialu">Aktualności</h2>

  <p>Aktualności zawierają informacje o działalności firmy: zakończonych remontach, inwestycjach
  i nowych budynkach w zarządzie. Nowy wpis dodaje się przez <strong>Wpisy → Dodaj wpis</strong>.</p>

  <p>W przypadku aktualności dostępne są dodatkowo <strong>obrazek wyróżniający</strong> oraz
  <strong>kategoria</strong>. Obie opcje znajdują się w panelu bocznym edytora, otwieranym
  przyciskiem <strong>Ustawienia</strong> w prawym górnym rogu.</p>

  ${rysunekDwuczesciowy('sidebar-aktualnosc', 'Panel boczny wpisu (górna i dolna część)', { x: 512, y: 40, w: 532, h: 1010 }, 330)}

  ${legenda([
    '<strong>Ustaw obrazek wyróżniający</strong> — zdjęcie wyświetlane przy wpisie, wybierane z biblioteki mediów lub wgrywane z komputera.',
    '<strong>Edytuj zajawkę</strong> — krótkie streszczenie wyświetlane na kafelku. Bez wypełnienia używany jest początek treści.',
    '<strong>Status</strong> — szkic lub opublikowany.',
    '<strong>Opublikuj</strong> — data publikacji. Ustawienie daty przyszłej powoduje automatyczną publikację we wskazanym terminie.',
    '<strong>Kategorie</strong> — Remonty, Inwestycje, Administracja lub Firma. Kategoria wyświetlana jest obok daty.',
  ])}
</section>`);

  /* 11. Media */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 11</p>
  <h2 class="tytul-dzialu">Zdjęcia i pliki PDF</h2>

  <p>Sekcja <strong>Media</strong> zawiera wszystkie pliki wgrane do serwisu. Raz wgrany plik może
  być wykorzystany w wielu miejscach.</p>

  ${rysunek('media', 'Biblioteka mediów', { x: 0, y: 0, w: 1600, h: 700 })}

  ${legenda([
    '<strong>Dodaj plik multimedialny</strong> — wgranie pliku przez przeciągnięcie go do okna lub wybór z dysku.',
    '<strong>Lista plików</strong> — kliknięcie nazwy wyświetla szczegóły pliku, w tym jego adres.',
  ])}

  <h3>Zalecenia dotyczące zdjęć</h3>
  <ul class="lista">
    <li>Zdjęcia można wgrywać w oryginalnym rozmiarze — serwis automatycznie tworzy wersje pomniejszone.</li>
    <li>Nazwy plików powinny opisywać zawartość, np. <code>klatka-michalkowicka-24.jpg</code>.</li>
    <li>Publikowanie wizerunku mieszkańców wymaga ich zgody.</li>
  </ul>

  <h3>Aktualizacja regulaminu w formacie PDF</h3>
  <ol class="kroki">
    <li>Wgrać nowy plik PDF w sekcji <strong>Media</strong>.</li>
    <li>Otworzyć szczegóły pliku i skopiować jego adres.</li>
    <li>Wkleić adres w <strong>Ustawieniach TRROL</strong>, w polu „PDF — Regulamin użytkowania lokali”.</li>
    <li>Zapisać zmiany.</li>
  </ol>
</section>`);

  /* 12. Strony */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 12</p>
  <h2 class="tytul-dzialu">Strony stałe</h2>

  <p>Strony stałe to podstrony o niezmiennym charakterze, w odróżnieniu od wpisów, które są
  regularnie dodawane.</p>

  ${rysunek('strony', 'Lista stron stałych', { x: 0, y: 0, w: 1600, h: 700 })}

  ${legenda([
    '<strong>O nas</strong> — opis firmy. Treść edytuje się tak samo jak treść wpisu.',
    '<strong>Kontakt</strong> — numery telefonów, godziny otwarcia i mapa pobierane są z <strong>Ustawień TRROL</strong> i tam należy je zmieniać.',
    '<strong>Polityka prywatności</strong> — dokument wymagany przepisami. Zmiany powinny być konsultowane z osobą odpowiedzialną za ochronę danych osobowych.',
  ])}

  ${uwaga(`Nie należy usuwać ani zmieniać adresów stron „Kontakt”, „Regulaminy” i „Polityka
  prywatności”. Odnośniki do tych stron znajdują się w wielu miejscach serwisu.`)}

  <h3>Regulaminy</h3>
  <p>Strona „Regulaminy” zawiera dwie podstrony: regulamin użytkowania lokali oraz regulamin
  rozliczania wody i ścieków. Każdy dokument dostępny jest do przeczytania na stronie i do pobrania
  w formacie PDF. Przy zmianie regulaminu należy zaktualizować zarówno treść podstrony, jak i plik PDF.</p>
</section>`);

  /* 13. Ustawienia — cz. 1 */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 13</p>
  <h2 class="tytul-dzialu">Ustawienia TRROL — dane firmy i kontakt</h2>

  <p>Dane wprowadzone w tej sekcji wyświetlane są w całym serwisie: w nagłówku, stopce, na stronie
  kontaktowej i przy wpisach. Zmiana wprowadzona w jednym miejscu jest widoczna na wszystkich
  podstronach.</p>

  ${rysunek('ustawienia', 'Ustawienia TRROL — dane spółki i kontakt', { x: 160, y: 30, w: 1440, h: 1240 })}

  ${legenda([
    '<strong>Nazwa spółki</strong> — pełna nazwa wyświetlana w stopce i w klauzuli zgody na przetwarzanie danych.',
    '<strong>Telefon — sekretariat</strong> — numer wyświetlany w nagłówku strony.',
    '<strong>E-mail publiczny</strong> — adres wyświetlany odwiedzającym.',
    '<strong>E-mail dla formularzy</strong> — adres, na który przesyłane są wiadomości z formularzy kontaktowych i ofertowych.',
  ])}

  ${uwaga(`Zmiany zostają zapisane dopiero po kliknięciu przycisku <strong>Zapisz zmiany</strong>
  na dole strony.`)}
</section>`);

  /* 14. Ustawienia — cz. 2 */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 14</p>
  <h2 class="tytul-dzialu">Ustawienia TRROL — godziny, dokumenty, Google</h2>

  ${rysunek('ustawienia-1240', 'Ustawienia TRROL — dalsza część', { x: 160, y: 32, w: 1440, h: 990 })}

  ${legenda([
    [5, '<strong>Godziny otwarcia</strong> — w formacie <code>7:00–17:00</code>. Wyświetlane na stronie kontaktowej i stronie głównej oraz przekazywane wyszukiwarce Google.'],
    '<strong>Sobota, niedziela</strong> — zazwyczaj „nieczynne”.',
    '<strong>Adres osadzenia mapy</strong> — pole techniczne; zmiana wymagana wyłącznie przy zmianie adresu biura.',
    '<strong>PDF — Regulamin użytkowania lokali</strong> — adres pliku PDF (zob. rozdział 11).',
    '<strong>Opis strony głównej</strong> — tekst wyświetlany w wynikach wyszukiwania Google pod tytułem strony, maksymalnie ok. 155 znaków.',
    '<strong>Kod weryfikacyjny Google</strong> — kod z usługi Google Search Console, potwierdzający własność serwisu.',
  ])}

  <p>Po wprowadzeniu zmian należy kliknąć przycisk <strong>Zapisz zmiany</strong> na dole strony.</p>

  ${info(`Pozostałe pola sekcji SEO (wizytówka Google, profil na Facebooku, współrzędne biura)
  są opcjonalne.`)}
</section>`);

  /* 15. Publikowanie */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 15</p>
  <h2 class="tytul-dzialu">Publikowanie i wycofywanie wpisów</h2>

  <p>Każdy wpis ma jeden z dwóch statusów:</p>

  <table class="tab tab--stany">
    <tr>
      <th>Szkic</th>
      <td>Wpis zapisany w panelu, <strong>niewidoczny na stronie publicznej</strong>. Na liście
        wpisów oznaczony dopiskiem „— Szkic”.</td>
    </tr>
    <tr>
      <th>Opublikowany</th>
      <td>Wpis <strong>widoczny na stronie publicznej</strong> i dostępny dla wyszukiwarek.</td>
    </tr>
  </table>

  <h3>Publikacja wpisu</h3>
  <ol class="kroki">
    <li>Otworzyć wpis do edycji.</li>
    <li>Kliknąć przycisk <strong>Opublikuj</strong> w prawym górnym rogu.</li>
    <li>Potwierdzić publikację, klikając ponownie <strong>Opublikuj</strong>.</li>
    <li>Opcjonalnie kliknąć <strong>Zobacz wpis</strong>, aby sprawdzić jego wygląd na stronie.</li>
  </ol>

  <h3>Wycofanie wpisu ze strony</h3>
  <p>W panelu bocznym wpisu, przy pozycji <strong>Status</strong>, należy zmienić status na
  <strong>Szkic</strong> i zapisać wpis. Wpis przestaje być widoczny na stronie, ale pozostaje
  w panelu i może zostać ponownie opublikowany.</p>

  ${info(`Przykładowe wpisy przygotowane przy uruchomieniu serwisu zapisane są jako szkice.
  Przed publikacją należy zweryfikować i uzupełnić zawarte w nich dane.`)}

  ${uwaga(`Przed publikacją wolnego lokalu należy sprawdzić poprawność adresu i metrażu.`)}
</section>`);

  /* 16. Problemy */
  s.push(`
<section>
  <p class="nr-dzialu">Rozdział 16</p>
  <h2 class="tytul-dzialu">Najczęstsze problemy</h2>

  <dl class="faq">
    <dt>Wpis nie jest widoczny na stronie</dt>
    <dd>Wpis ma najprawdopodobniej status szkicu i wymaga publikacji. Należy również sprawdzić,
      czy został dodany w odpowiedniej sekcji — np. wolny lokal nie pojawi się wśród ogłoszeń.</dd>

    <dt>Okno przeglądarki zostało zamknięte przed zapisaniem wpisu</dt>
    <dd>Edytor automatycznie zapisuje szkice w regularnych odstępach czasu. Wpis powinien być
      dostępny na liście wpisów.</dd>

    <dt>Konieczność przywrócenia wcześniejszej wersji tekstu</dt>
    <dd>W panelu bocznym edytora, w pozycji „Wersje”, dostępna jest historia zmian wraz
      z możliwością przywrócenia wybranej wersji.</dd>

    <dt>Wpis został usunięty przez pomyłkę</dt>
    <dd>Na liście wpisów należy wybrać filtr <strong>Kosz</strong>, a następnie przy wybranym
      wpisie kliknąć „Przywróć”.</dd>

    <dt>Po zmianie danych kontaktowych strona wyświetla stare dane</dt>
    <dd>Należy sprawdzić, czy zmiany w Ustawieniach TRROL zostały zapisane, a następnie odświeżyć
      stronę skrótem klawiszowym <strong>Ctrl + F5</strong>.</dd>

    <dt>Wiadomości z formularzy nie docierają</dt>
    <dd>Należy sprawdzić folder spam oraz poprawność adresu w polu „E-mail dla formularzy”
      w Ustawieniach TRROL.</dd>

    <dt>Strona wyświetla się nieprawidłowo</dt>
    <dd>Należy odświeżyć stronę skrótem <strong>Ctrl + F5</strong>. Jeżeli problem nie ustąpi,
      należy skontaktować się z obsługą techniczną serwisu.</dd>

    <dt>Brak wolnych lokali</dt>
    <dd>Nie ma potrzeby usuwania wpisów — wystarczy zmienić ich status na szkic. Strona wyświetli
      wówczas komunikat o braku wolnych lokali, a wpis można ponownie opublikować w przyszłości.</dd>
  </dl>
</section>`);

  /* 17. Zestawienie */
  s.push(`
<section>
  <h2 class="tytul-dzialu">Zestawienie najważniejszych czynności</h2>

  <table class="tab tab--sciaga">
    <tr><th>Logowanie do panelu</th><td><code>trrol.pl/wp-admin</code></td></tr>
    <tr><th>Dodanie wolnego lokalu</th><td>Wolne lokale → Dodaj wolny lokal → tytuł (adres i metraż) → opis → Dane wpisu → <strong>Opublikuj</strong></td></tr>
    <tr><th>Dodanie zlecenia dla firm</th><td>Oferty dla firm → Dodaj zlecenie → numer zlecenia i termin ofert → <strong>Opublikuj</strong></td></tr>
    <tr><th>Dodanie ogłoszenia</th><td>Ogłoszenia → Dodaj ogłoszenie → etykieta → <strong>Opublikuj</strong></td></tr>
    <tr><th>Dodanie aktualności</th><td>Wpisy → Dodaj wpis → obrazek wyróżniający i kategoria → <strong>Opublikuj</strong></td></tr>
    <tr><th>Zmiana danych kontaktowych</th><td>Ustawienia TRROL → edycja pól → <strong>Zapisz zmiany</strong></td></tr>
    <tr><th>Aktualizacja regulaminu PDF</th><td>Media → wgranie pliku → skopiowanie adresu → Ustawienia TRROL → <strong>Zapisz zmiany</strong></td></tr>
    <tr><th>Wycofanie wpisu</th><td>Edycja wpisu → Status → <strong>Szkic</strong></td></tr>
    <tr><th>Przywrócenie usuniętego wpisu</th><td>Lista wpisów → Kosz → Przywróć</td></tr>
    <tr><th>Odświeżenie strony</th><td><strong>Ctrl + F5</strong></td></tr>
  </table>

  <div class="zapamietaj">
    <h3>Zasady ogólne</h3>
    <ol>
      <li><strong>Zapisz szkic</strong> zapisuje wpis bez publikowania go na stronie.</li>
      <li>Wpis jest widoczny publicznie dopiero po kliknięciu <strong>Opublikuj</strong>.</li>
      <li>Dane kontaktowe zmienia się wyłącznie w <strong>Ustawieniach TRROL</strong>.</li>
    </ol>
  </div>

  <div class="pomoc">
    <p class="pomoc__tytul">Wsparcie techniczne</p>
    <p>W przypadku problemów, których nie obejmuje niniejsza instrukcja, należy skontaktować się
    z obsługą techniczną serwisu.</p>
  </div>
</section>`);

  return s;
};
