/**
 * Złożenie poradnika obsługi panelu w jeden dokument HTML gotowy do druku.
 */
const fs = require('fs');
const path = require('path');

const SHOTS = path.join(__dirname, 'shots');
const FONTS = path.join(__dirname, '..', 'theme', 'trrol', 'assets', 'fonts');
const IMG = path.join(__dirname, '..', 'theme', 'trrol', 'assets', 'img');

const b64 = (p) => fs.readFileSync(p).toString('base64');

/** Wymiary pliku PNG. */
function pngSize(p) {
  const b = fs.readFileSync(p);
  return { w: b.readUInt32BE(16), h: b.readUInt32BE(20) };
}

const SZEROKOSC = 700; // szerokość obrazu na stronie A4 (px)

/**
 * Rysunek z podpisem. `crop` wycina fragment zrzutu (współrzędne oryginału).
 */
function rysunek(nazwa, podpis, crop) {
  const file = path.join(SHOTS, `${nazwa}.png`);
  const nat = pngSize(file);
  const c = Object.assign({ x: 0, y: 0, w: nat.w, h: nat.h }, crop || {});
  const skala = SZEROKOSC / c.w;

  return `
<figure class="rys">
  <div class="rys__ramka" style="width:${Math.round(c.w * skala)}px;height:${Math.round(c.h * skala)}px">
    <img src="data:image/png;base64,${b64(file)}"
         style="width:${Math.round(nat.w * skala)}px;margin-left:${-Math.round(c.x * skala)}px;margin-top:${-Math.round(c.y * skala)}px">
  </div>
  ${podpis ? `<figcaption>${podpis}</figcaption>` : ''}
</figure>`;
}

/**
 * Wąski, wysoki zrzut pokazany jako dwie kolumny obok siebie —
 * inaczej jedna szpalta rozpychałaby stronę na dwie.
 */
function rysunekDwuczesciowy(nazwa, podpis, crop, targetW) {
  const file = path.join(SHOTS, `${nazwa}.png`);
  const nat = pngSize(file);
  const szer = targetW || 336;
  const skala = szer / crop.w;
  const polowa = Math.ceil(crop.h / 2);

  const czesc = (offsetY, wysokosc) => `
    <div class="rys__ramka" style="width:${Math.round(crop.w * skala)}px;height:${Math.round(wysokosc * skala)}px">
      <img src="data:image/png;base64,${b64(file)}"
           style="width:${Math.round(nat.w * skala)}px;margin-left:${-Math.round(crop.x * skala)}px;margin-top:${-Math.round(offsetY * skala)}px">
    </div>`;

  return `
<figure class="rys">
  <div class="rys__para">
    ${czesc(crop.y, polowa)}
    ${czesc(crop.y + polowa, crop.h - polowa)}
  </div>
  ${podpis ? `<figcaption>${podpis}</figcaption>` : ''}
</figure>`;
}

/** Lista numerowana odpowiadająca znacznikom na zrzucie. */
function legenda(pozycje) {
  return `<ol class="legenda">${pozycje
    .map((t) => `<li>${t}</li>`)
    .join('')}</ol>`;
}

const wskazowka = (t) => `<div class="box box--tip"><strong>Podpowiedź.</strong> ${t}</div>`;
const uwaga = (t) => `<div class="box box--warn"><strong>Uwaga.</strong> ${t}</div>`;
const spokojnie = (t) => `<div class="box box--calm">${t}</div>`;

/* ------------------------------------------------------------------ treść */

const strony = [];

/* Okładka */
strony.push(`
<section class="okladka">
  <img class="okladka__logo" src="data:image/png;base64,${b64(path.join(IMG, 'logo-white.png'))}" alt="TRROL Nieruchomości">
  <div class="okladka__srodek">
    <p class="okladka__nad">Poradnik obsługi strony internetowej</p>
    <h1>Jak dodawać treści<br>na stronie trrol.pl</h1>
    <p class="okladka__lead">Instrukcja krok po kroku dla osób, które nie miały wcześniej do czynienia
      z prowadzeniem strony internetowej. Wszystko pokazane na prawdziwych zrzutach ekranu.</p>
  </div>
  <div class="okladka__stopka">
    <div><strong>„TRROL” Nieruchomości Sp. z o.o.</strong><br>ul. Śląska 80, 41-100 Siemianowice Śląskie</div>
    <div class="okladka__data">Wersja 1.0</div>
  </div>
</section>`);

/* Spis treści */
strony.push(`
<section>
  <h2 class="tytul-dzialu">Spis treści</h2>
  <ol class="spis">
    <li><span>Zanim zaczniesz — kilka słów na start</span><em>3</em></li>
    <li><span>Jak wejść do panelu (logowanie)</span><em>4</em></li>
    <li><span>Pierwszy rzut oka na panel</span><em>5</em></li>
    <li><span>Cztery rodzaje treści — co gdzie dodawać</span><em>6</em></li>
    <li><span>Wolne lokale — lista wpisów</span><em>7</em></li>
    <li><span>Wolne lokale — dodawanie nowego lokalu</span><em>8</em></li>
    <li><span>Wolne lokale — pola „Dane wpisu”</span><em>9</em></li>
    <li><span>Oferty dla firm</span><em>10</em></li>
    <li><span>Ogłoszenia dla mieszkańców</span><em>11</em></li>
    <li><span>Aktualności — wpisy na blog</span><em>12</em></li>
    <li><span>Zdjęcia i pliki PDF</span><em>13</em></li>
    <li><span>Strony stałe — „O nas”, „Kontakt”</span><em>14</em></li>
    <li><span>Ustawienia TRROL — dane firmy</span><em>15</em></li>
    <li><span>Ustawienia TRROL — godziny, mapa, Google</span><em>16</em></li>
    <li><span>Szkic czy opublikowany? Jak działa publikowanie</span><em>17</em></li>
    <li><span>Co zrobić, gdy… — najczęstsze sytuacje</span><em>18</em></li>
    <li><span>Ściąga na jedną stronę</span><em>19</em></li>
  </ol>
</section>`);

/* 1. Zanim zaczniesz */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 1</p>
  <h2 class="tytul-dzialu">Zanim zaczniesz — kilka słów na start</h2>

  <p>Strona internetowa TRROL składa się z dwóch części. Pierwsza to <strong>strona, którą widzą
  wszyscy</strong> — mieszkańcy, osoby szukające mieszkania, firmy remontowe. Druga to
  <strong>panel</strong>, czyli zaplecze, do którego wchodzi tylko osoba z hasłem. W panelu dodaje się
  i zmienia treści. Ten poradnik dotyczy panelu.</p>

  <p>Panel działa w przeglądarce internetowej — tej samej, w której czyta się pocztę czy przegląda
  internet. Nie trzeba niczego instalować.</p>

  <h3>Dwa adresy, które warto zapamiętać</h3>
  <table class="tab">
    <tr><th>Strona dla wszystkich</th><td><code>trrol.pl</code></td></tr>
    <tr><th>Panel do zarządzania</th><td><code>trrol.pl/wp-admin</code></td></tr>
  </table>

  ${wskazowka(`Dodaj adres panelu do zakładek w przeglądarce — nie będziesz musiała za każdym razem
  go wpisywać. W przeglądarce Chrome robi się to gwiazdką po prawej stronie paska adresu.`)}

  <h3>Czego nie da się zepsuć</h3>
  <p>To najważniejsza rzecz na start: <strong>bardzo trudno tu coś nieodwracalnie zepsuć</strong>.
  System zapisuje historię zmian, więc do wcześniejszej wersji tekstu zawsze można wrócić. Wpisy,
  których nie chcesz publikować, zostają jako <em>szkice</em> — widzisz je tylko Ty. Usunięte wpisy
  trafiają najpierw do kosza, a nie znikają od razu.</p>

  ${spokojnie(`<strong>Jeśli czegoś nie jesteś pewna — po prostu nie klikaj „Opublikuj”.</strong>
  Zapisz jako szkic i wróć do tego później albo zadzwoń do nas. Nic się nie stanie.`)}

  <h3>Kilka słów, które będą się powtarzać</h3>
  <table class="tab tab--slownik">
    <tr><th>Wpis</th><td>Pojedyncza rzecz dodana na stronę: jeden wolny lokal, jedno ogłoszenie, jedna aktualność.</td></tr>
    <tr><th>Szkic</th><td>Wpis zapisany, ale jeszcze niewidoczny dla nikogo poza Tobą.</td></tr>
    <tr><th>Opublikowany</th><td>Wpis widoczny na stronie dla wszystkich.</td></tr>
    <tr><th>Zajawka</th><td>Krótkie streszczenie wpisu — to ono pokazuje się na listach i kafelkach.</td></tr>
    <tr><th>Panel</th><td>Zaplecze strony, dostępne po zalogowaniu.</td></tr>
  </table>
</section>`);

/* 2. Logowanie */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 2</p>
  <h2 class="tytul-dzialu">Jak wejść do panelu (logowanie)</h2>

  <p>W pasku adresu przeglądarki wpisz <code>trrol.pl/wp-admin</code> i naciśnij Enter.
  Zobaczysz taki ekran:</p>

  ${rysunek('logowanie', 'Ekran logowania do panelu', { x: 480, y: 30, w: 700, h: 520 })}

  ${legenda([
    '<strong>Nazwa użytkownika</strong> — wpisz login, który dostałaś. Można też wpisać adres e-mail przypisany do konta.',
    '<strong>Hasło</strong> — wpisz hasło. Zamiast liter zobaczysz kropki i tak ma być: chodzi o to, żeby nikt nie podejrzał hasła zza pleców.',
    '<strong>Zapamiętaj mnie</strong> — jeśli zaznaczysz, komputer nie będzie pytał o hasło przez najbliższe dwa tygodnie. Zaznaczaj tylko na swoim służbowym komputerze, nigdy na cudzym.',
    '<strong>Zaloguj się</strong> — kliknij, żeby wejść do panelu.',
    '<strong>Nie pamiętasz hasła?</strong> — kliknij, wpisz swój adres e-mail, a system wyśle wiadomość z linkiem do ustawienia nowego hasła.',
  ])}

  ${uwaga(`Jeśli po zalogowaniu przez dłuższy czas nic nie klikasz, system sam Cię wyloguje —
  to normalne zabezpieczenie. Wtedy po prostu zaloguj się jeszcze raz.`)}

  ${wskazowka(`Hasło zapisz w bezpiecznym miejscu, ale <strong>nie na karteczce przyklejonej do
  monitora</strong>. Jeśli musisz je komuś przekazać, lepiej zrobić to telefonicznie niż mailem.`)}
</section>`);

/* 3. Kokpit */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 3</p>
  <h2 class="tytul-dzialu">Pierwszy rzut oka na panel</h2>

  <p>Po zalogowaniu widzisz <strong>Kokpit</strong> — stronę powitalną panelu. Najważniejsze jest
  <strong>ciemne menu po lewej stronie</strong>. To z niego wchodzisz wszędzie indziej.</p>

  ${rysunek('kokpit', 'Kokpit — menu po lewej i ściąga przygotowana specjalnie dla Was', { x: 0, y: 0, w: 1600, h: 900 })}

  ${legenda([
    '<strong>Wolne lokale</strong> — wykaz mieszkań i lokali użytkowych do wynajęcia.',
    '<strong>Oferty dla firm</strong> — zapytania ofertowe na remonty, na które zgłaszają się wykonawcy.',
    '<strong>Ogłoszenia</strong> — bieżące komunikaty dla mieszkańców.',
    '<strong>Wpisy</strong> — aktualności, czyli firmowy blog.',
    '<strong>Strony</strong> — stałe podstrony: „O nas”, „Kontakt”, regulaminy.',
    '<strong>Media</strong> — wszystkie zdjęcia i pliki PDF wgrane na stronę.',
    '<strong>Ustawienia TRROL</strong> — telefony, e-mail, adres, godziny otwarcia.',
    '<strong>Nazwa strony u góry</strong> — kliknij, żeby zobaczyć stronę oczami odwiedzającego.',
    '<strong>Ściąga „TRROL — jak zarządzać stroną”</strong> — skrót do najważniejszych miejsc, przygotowany specjalnie dla Was.',
  ])}

  ${wskazowka(`Menu po lewej jest zawsze w tym samym miejscu, na każdym ekranie panelu.
  Jeśli się zgubisz — spójrz w lewo i kliknij to, czego szukasz. Albo kliknij „Kokpit”, żeby wrócić na start.`)}
</section>`);

/* 4. Cztery rodzaje treści */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 4</p>
  <h2 class="tytul-dzialu">Cztery rodzaje treści — co gdzie dodawać</h2>

  <p>Strona ma cztery osobne miejsca na wpisy. Wygląda to podobnie, ale każde służy do czego innego
  i inaczej pokazuje się odwiedzającym. Ta tabela rozwiązuje większość wątpliwości:</p>

  <table class="tab tab--rodzaje">
    <thead><tr><th>Chcesz napisać, że…</th><th>Dodaj w</th><th>Kto to zobaczy</th></tr></thead>
    <tbody>
      <tr>
        <td>zwolniło się mieszkanie albo lokal użytkowy</td>
        <td><strong>Wolne lokale</strong></td>
        <td>osoby szukające mieszkania do wynajęcia</td>
      </tr>
      <tr>
        <td>szukacie firmy do remontu, malowania, elektryki</td>
        <td><strong>Oferty dla firm</strong></td>
        <td>firmy remontowe i wykonawcy</td>
      </tr>
      <tr>
        <td>będzie deratyzacja, przerwa w prądzie, odczyt liczników</td>
        <td><strong>Ogłoszenia</strong></td>
        <td>mieszkańcy budynków</td>
      </tr>
      <tr>
        <td>skończył się remont, przejęliście nowy budynek</td>
        <td><strong>Wpisy</strong> (aktualności)</td>
        <td>wszyscy odwiedzający stronę</td>
      </tr>
    </tbody>
  </table>

  <h3>Jak to działa w praktyce</h3>
  <p>Każdy z tych czterech rodzajów ma swoją własną podstronę z listą oraz swoje miejsce na stronie
  głównej. Nie trzeba nic nigdzie „podpinać” ani przestawiać — <strong>wystarczy dodać wpis
  w odpowiednim miejscu, a strona sama go pokaże</strong> tam, gdzie trzeba.</p>

  <p>Jeśli w danym miejscu nie ma żadnego wpisu, strona nie wygląda na pustą ani zepsutą — pokazuje
  wtedy grzeczny komunikat, na przykład „Obecnie nie dysponujemy wolnymi lokalami” razem z zachętą
  do kontaktu. Nie musisz więc trzymać tam wpisów „na siłę”.</p>

  ${wskazowka(`Nie wiesz, gdzie coś wpisać? Zadaj sobie pytanie: <em>dla kogo</em> jest ta informacja.
  Dla mieszkańca — Ogłoszenia. Dla szukającego mieszkania — Wolne lokale. Dla firmy remontowej —
  Oferty dla firm. Dla wszystkich — Aktualności.`)}
</section>`);

/* 5. Lista lokali */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 5</p>
  <h2 class="tytul-dzialu">Wolne lokale — lista wpisów</h2>

  <p>Kliknij w menu po lewej <strong>Wolne lokale</strong>. Zobaczysz listę wszystkich lokali, jakie
  są w systemie — i tych widocznych na stronie, i tych odłożonych jako szkice.</p>

  ${rysunek('lista-lokali', 'Lista wolnych lokali', { x: 0, y: 0, w: 1600, h: 560 })}

  ${legenda([
    '<strong>Dodaj wolny lokal</strong> — od tego przycisku zaczynasz nowy wpis.',
    '<strong>Wszystkie / Szkice</strong> — szybkie filtry. „Szkice” pokaże tylko te wpisy, które nie są jeszcze widoczne na stronie.',
    '<strong>Tytuł wpisu</strong> — kliknij w niego, żeby otworzyć wpis do edycji.',
    '<strong>Powierzchnia i Układ</strong> — te kolumny pokazują dane, które wpiszesz przy lokalu. Dzięki nim widzisz najważniejsze informacje bez otwierania wpisu.',
    '<strong>Szkic</strong> — takie oznaczenie przy tytule znaczy, że ten wpis <u>nie jest jeszcze widoczny</u> na stronie.',
    '<strong>Edytuj / Szybka edycja / Przenieś do kosza / Podejrzyj</strong> — te odnośniki pojawiają się, gdy najedziesz myszką na wiersz. „Podejrzyj” pokazuje, jak wpis wygląda na stronie.',
  ])}

  ${uwaga(`„Przenieś do kosza” nie usuwa wpisu na zawsze — wpis leży w koszu i można go stamtąd
  przywrócić. Dopiero opróżnienie kosza usuwa go trwale.`)}
</section>`);

/* 6. Edytor lokalu */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 6</p>
  <h2 class="tytul-dzialu">Wolne lokale — dodawanie nowego lokalu</h2>

  <p>Po kliknięciu <strong>Dodaj wolny lokal</strong> otwiera się edytor. Wygląda jak zwykła kartka
  papieru — u góry tytuł, niżej treść.</p>

  ${rysunek('edytor-lokal', 'Edytor wpisu — tak wygląda dodawanie lokalu', { x: 0, y: 0, w: 1600, h: 900 })}

  ${legenda([
    '<strong>Tytuł</strong> — najważniejsze pole. Wpisz adres i metraż, na przykład: <em>ul. Michałkowicka 24/5 — mieszkanie 41,80 m²</em>. Ten tekst zobaczą ludzie na liście lokali.',
    '<strong>Treść</strong> — opis lokalu zwykłym językiem: ile pokoi, które piętro, jakie ogrzewanie, co warto wiedzieć. Pisz tak, jakbyś tłumaczyła to przez telefon.',
    '<strong>Zapisz szkic</strong> — zapisuje pracę, ale nie pokazuje wpisu na stronie. Używaj często, żeby nie stracić tekstu.',
    '<strong>Ustawienia</strong> — otwiera i zamyka panel boczny po prawej (widoczny na obrazku).',
    '<strong>Opublikuj</strong> — dopiero to sprawia, że wpis pojawia się na stronie dla wszystkich.',
  ])}

  ${wskazowka(`Tekst piszesz normalnie, jak w Wordzie. Enter tworzy nowy akapit. Żeby zrobić listę
  punktowaną albo pogrubić słowo, zaznacz tekst — nad zaznaczeniem pojawi się mały pasek z narzędziami.`)}

  ${uwaga(`<strong>Nie podajemy cen najmu.</strong> Tak ustaliliśmy — warunki omawiacie indywidualnie
  przy kontakcie. Pod każdym lokalem strona sama dokłada formularz kontaktowy i zastrzeżenie prawne.`)}
</section>`);

/* 7. Dane wpisu lokalu */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 7</p>
  <h2 class="tytul-dzialu">Wolne lokale — pola „Dane wpisu”</h2>

  <p>Pod treścią wpisu, po przewinięciu w dół, znajdziesz dwie ramki. Pierwsza to
  <strong>Dane wpisu</strong> — te informacje strona pokazuje w tabelce obok opisu lokalu.</p>

  ${rysunek('dane-wpisu-lokal', 'Pola dodatkowe przy wolnym lokalu', { x: 250, y: 45, w: 1110, h: 890 })}

  ${legenda([
    '<strong>Powierzchnia</strong> — wpisz z jednostką, np. <em>41,80 m²</em>.',
    '<strong>Układ</strong> — krótko, np. <em>2 pokoje, II piętro</em>.',
    '<strong>Dotyczy budynku</strong> — sam adres budynku, np. <em>Michałkowicka 24</em>. Dzięki temu strona łączy ze sobą wpisy dotyczące tego samego budynku.',
    '<strong>Rodzaj lokalu</strong> — wybierz z listy: mieszkalny albo użytkowy.',
    '<strong>Dostępność</strong> — np. <em>od zaraz</em> albo <em>w rezerwacji</em>.',
    '<strong>Tytuł w Google</strong> — zostaw puste. Wypełnij tylko, jeśli chcesz, żeby w wyszukiwarce widniał inny tytuł niż na stronie.',
    '<strong>Opis w Google</strong> — też można zostawić puste. Strona sama weźmie początek opisu lokalu.',
  ])}

  ${spokojnie(`<strong>Pola 6 i 7 możesz spokojnie pomijać.</strong> Zostały przygotowane tak, żeby
  wszystko działało dobrze także wtedy, gdy nic w nich nie wpiszesz.`)}
</section>`);

/* 8. Oferty dla firm */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 8</p>
  <h2 class="tytul-dzialu">Oferty dla firm</h2>

  <p>To miejsce na zapytania ofertowe — kiedy szukacie wykonawcy do malowania klatki, wymiany
  instalacji czy remontu dachu. Firmy czytają ogłoszenie i składają ofertę przez formularz.</p>

  <p>Wpis dodajesz tak samo jak lokal: <strong>Oferty dla firm → Dodaj zlecenie</strong>. W tytule
  napisz, czego dotyczy praca i przy którym budynku. W treści opisz zakres prac — im konkretniej,
  tym sensowniejsze oferty dostaniecie.</p>

  ${rysunek('dane-wpisu-oferta', 'Pola dodatkowe przy zleceniu dla firm', { x: 250, y: 40, w: 1110, h: 470 })}

  ${legenda([
    '<strong>Numer zlecenia</strong> — Wasze oznaczenie, np. <em>08/2026</em>. Pokazuje się przy tytule i ułatwia rozmowy telefoniczne z wykonawcami.',
    '<strong>Termin składania ofert</strong> — wybierz datę z kalendarzyka. <u>Po tej dacie strona sama oznaczy zlecenie jako zamknięte</u> i schowa formularz — nie trzeba o tym pamiętać.',
    '<strong>Status naboru</strong> — normalnie zostaw „Otwarty nabór”. Przestaw na „Nabór zakończony”, jeśli chcesz zamknąć zbieranie ofert wcześniej niż w terminie.',
    '<strong>Dotyczy budynku</strong> — adres budynku, którego dotyczą prace.',
  ])}

  ${wskazowka(`Zlecenia po terminie nie znikają ze strony — zostają widoczne jako wyszarzone,
  z adnotacją, że nabór został zakończony. To buduje zaufanie: wykonawcy widzą, że naprawdę
  zlecacie prace.`)}

  <p>Oferty od firm przychodzą <strong>e-mailem na adres ustawiony w Ustawieniach TRROL</strong>,
  razem z załączonym plikiem PDF, jeśli firma go dołączy.</p>
</section>`);

/* 9. Ogłoszenia */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 9</p>
  <h2 class="tytul-dzialu">Ogłoszenia dla mieszkańców</h2>

  <p>Tu trafiają bieżące komunikaty: deratyzacja, odczyty liczników, planowane prace, zapisy na
  miejsca parkingowe. <strong>Najnowsze ogłoszenie pokazuje się od razu na stronie głównej</strong>,
  w widocznym miejscu — mieszkaniec nie musi go szukać.</p>

  <p>Dodajesz przez <strong>Ogłoszenia → Dodaj ogłoszenie</strong>.</p>

  ${rysunek('dane-wpisu-ogloszenie', 'Pola dodatkowe przy ogłoszeniu', { x: 250, y: 40, w: 1110, h: 400 })}

  ${legenda([
    '<strong>Etykieta</strong> — jedno–dwa słowa mówiące, czego dotyczy ogłoszenie: <em>Liczniki</em>, <em>Prace</em>, <em>Parking</em>. Pokazuje się przy dacie. Jeśli zostawisz puste, strona napisze po prostu „Ogłoszenia”.',
    '<strong>Dotyczy budynku</strong> — adres, jeśli ogłoszenie dotyczy jednego konkretnego budynku.',
    '<strong>Ważne do</strong> — opcjonalna data, po której sprawa przestaje być aktualna. Pomaga Wam potem odróżnić, co jeszcze obowiązuje.',
  ])}

  ${wskazowka(`W ogłoszeniach dla mieszkańców pisz konkretnie i krótko: <em>co</em>, <em>kiedy</em>
  i <em>o czym trzeba pamiętać</em>. Najważniejsze informacje daj na początku — wiele osób czyta
  tylko pierwsze zdanie.`)}

  ${uwaga(`Ogłoszenia zostają na stronie, dopóki ich nie usuniesz. Raz na jakiś czas warto przejrzeć
  listę i przenieść do kosza te, które są już nieaktualne.`)}
</section>`);

/* 10. Aktualności */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 10</p>
  <h2 class="tytul-dzialu">Aktualności — wpisy na blog</h2>

  <p>Aktualności to firmowy blog: zakończone remonty, nowe inwestycje, przejęcie kolejnej kamienicy.
  Dodajesz je przez <strong>Wpisy → Dodaj wpis</strong>. Przy aktualnościach dochodzą dwie rzeczy,
  których nie ma przy innych wpisach: <strong>zdjęcie</strong> i <strong>kategoria</strong>.</p>

  <p>Obie znajdziesz w panelu po prawej stronie edytora. Jeśli go nie widzisz, kliknij przycisk
  <strong>Ustawienia</strong> w prawym górnym rogu.</p>

  ${rysunekDwuczesciowy('sidebar-aktualnosc', 'Panel boczny przy aktualności — górna i dolna część obok siebie', { x: 512, y: 40, w: 532, h: 1010 }, 330)}

  ${legenda([
    '<strong>Ustaw obrazek wyróżniający</strong> — zdjęcie, które pokaże się przy wpisie. Kliknij, wybierz zdjęcie z biblioteki albo wgraj nowe z komputera.',
    '<strong>Edytuj zajawkę</strong> — dwa–trzy zdania streszczenia. To one pokazują się na kafelku na stronie głównej. Jeśli zostawisz puste, strona weźmie początek tekstu.',
    '<strong>Status</strong> — „Szkic” albo „Opublikowany”.',
    '<strong>Opublikuj</strong> — data publikacji. Można ustawić datę w przyszłości, a wtedy wpis pojawi się sam o wyznaczonej porze.',
    '<strong>Kategorie</strong> — zaznacz jedną: Remonty, Inwestycje, Administracja albo Firma. Kategoria pokazuje się przy dacie i grupuje wpisy.',
  ])}

  ${wskazowka(`Zdjęcie nie jest obowiązkowe, ale wpisy ze zdjęciem wyglądają dużo lepiej.
  Wystarczy zwykłe zdjęcie z telefonu — na przykład wyremontowana klatka schodowa albo nowy parking.`)}
</section>`);

/* 11. Media */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 11</p>
  <h2 class="tytul-dzialu">Zdjęcia i pliki PDF</h2>

  <p><strong>Media</strong> to magazyn wszystkich plików wgranych na stronę — zdjęć i dokumentów PDF.
  Raz wgrany plik można wykorzystać w dowolnym miejscu, wiele razy.</p>

  ${rysunek('media', 'Biblioteka mediów', { x: 0, y: 0, w: 1600, h: 700 })}

  ${legenda([
    '<strong>Dodaj plik multimedialny</strong> — kliknij, a potem przeciągnij plik z komputera albo kliknij „Wybierz pliki”.',
    '<strong>Lista plików</strong> — wszystko, co już jest wgrane. Klikając w nazwę, zobaczysz szczegóły i adres pliku.',
  ])}

  <h3>Na co zwrócić uwagę przy zdjęciach</h3>
  <ul class="lista">
    <li><strong>Nie zmniejszaj zdjęć przed wgraniem</strong> — strona sama przygotuje mniejsze wersje.
      Wgrywaj zdjęcia prosto z telefonu albo aparatu.</li>
    <li><strong>Nazywaj pliki sensownie.</strong> <code>klatka-michalkowicka-24.jpg</code> jest dużo
      lepsze niż <code>IMG_20260907_0001.jpg</code> — łatwiej potem znaleźć.</li>
    <li><strong>Nie wgrywaj zdjęć z twarzami mieszkańców</strong> bez ich zgody.</li>
  </ul>

  <h3>Wymiana regulaminu na nowszą wersję</h3>
  <p>Jeśli kiedyś zmieni się regulamin: wgraj nowy plik PDF w Mediach, kliknij w niego, skopiuj
  <strong>adres pliku</strong>, a potem wklej go w <strong>Ustawieniach TRROL</strong> w polu
  „PDF — Regulamin użytkowania lokali”. Strona od razu zacznie podawać nową wersję do pobrania.</p>
</section>`);

/* 12. Strony */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 12</p>
  <h2 class="tytul-dzialu">Strony stałe — „O nas”, „Kontakt”</h2>

  <p>Strony to podstrony, które są na miejscu przez cały czas i rzadko się zmieniają — w odróżnieniu
  od wpisów, których przybywa.</p>

  ${rysunek('strony', 'Lista stron stałych', { x: 0, y: 0, w: 1600, h: 700 })}

  ${legenda([
    '<strong>O nas</strong> — opis firmy. Tekst zmieniasz tak samo jak treść każdego wpisu.',
    '<strong>Kontakt</strong> — telefony, godziny i mapa <u>nie są</u> wpisane w treść tej strony. Biorą się z <strong>Ustawień TRROL</strong>, więc zmieniaj je tam.',
    '<strong>Polityka prywatności</strong> — dokument wymagany przepisami. Zmieniaj tylko po konsultacji z osobą odpowiedzialną za ochronę danych.',
  ])}

  ${uwaga(`Nie usuwaj stron „Kontakt”, „Regulaminy” ani „Polityka prywatności” i nie zmieniaj ich
  adresów. Strona główna i stopka odwołują się do nich w kilku miejscach — po usunięciu odnośniki
  przestałyby działać.`)}

  <h3>Regulaminy</h3>
  <p>Strona „Regulaminy” ma dwie podstrony: regulamin użytkowania lokali oraz regulamin rozliczania
  wody. Każdą można czytać na stronie i pobrać jako PDF. Jeśli zmieni się treść regulaminu, trzeba
  poprawić <em>oba</em> miejsca — tekst na podstronie i plik PDF w Ustawieniach.</p>
</section>`);

/* 13-14. Ustawienia */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 13</p>
  <h2 class="tytul-dzialu">Ustawienia TRROL — dane firmy</h2>

  <p>To jedno z najważniejszych miejsc w panelu. <strong>Dane wpisane tutaj pojawiają się na całej
  stronie naraz</strong> — w nagłówku, w stopce, na stronie kontaktu, przy każdym lokalu.
  Zmieniasz numer telefonu w jednym miejscu, a poprawia się wszędzie.</p>

  ${rysunek('ustawienia', 'Ustawienia TRROL — dane spółki i kontakt', { x: 160, y: 30, w: 1440, h: 1240 })}

  ${legenda([
    '<strong>Nazwa spółki</strong> — pełna nazwa. Pokazuje się w stopce i w treści zgody na przetwarzanie danych.',
    '<strong>Telefon — sekretariat</strong> — numer widoczny w nagłówku strony, na samej górze każdej podstrony.',
    '<strong>E-mail publiczny</strong> — adres pokazywany odwiedzającym.',
    '<strong>E-mail dla formularzy</strong> — <u>tu przychodzą wiadomości wysłane przez formularze</u> ze strony. Może być inny niż publiczny, np. skrzynka wspólna działu.',
  ])}

  ${uwaga(`Po każdej zmianie trzeba zjechać na sam dół strony i kliknąć <strong>Zapisz zmiany</strong>.
  Bez tego poprawki nie zostaną zapamiętane.`)}
</section>`);

strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 14</p>
  <h2 class="tytul-dzialu">Ustawienia TRROL — godziny, mapa, Google</h2>

  ${rysunek('ustawienia-1240', 'Dalsza część ustawień', { x: 160, y: 0, w: 1440, h: 1150 })}

  ${legenda([
    '<strong>Godziny otwarcia</strong> — wpisuj w formacie <code>7:00–17:00</code>. Te godziny widać na stronie kontaktu i na stronie głównej. Trafiają też do Google, więc warto, żeby były aktualne.',
    '<strong>Sobota, niedziela</strong> — zwykle „nieczynne”.',
    '<strong>Adres osadzenia mapy</strong> — techniczne pole. Nie ruszaj, chyba że zmieni się adres biura.',
    '<strong>PDF — Regulamin</strong> — adres pliku PDF z regulaminem. Jak go zmienić, opisuje rozdział 11.',
    '<strong>Opis strony głównej</strong> — jedno zdanie, które Google pokazuje pod tytułem w wynikach wyszukiwania. Najlepiej do 155 znaków.',
    '<strong>Kod weryfikacyjny Google</strong> — do wklejenia, gdy będziecie podłączać stronę do Google Search Console. Zostaw puste, jeśli tego nie robicie.',
    '<strong>Zapisz zmiany</strong> — pamiętaj o kliknięciu.',
  ])}

  ${wskazowka(`Pól, których nie rozumiesz, po prostu nie ruszaj — strona działa poprawnie
  z ustawieniami, które już w nich są.`)}
</section>`);

/* 15. Publikowanie */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 15</p>
  <h2 class="tytul-dzialu">Szkic czy opublikowany? Jak działa publikowanie</h2>

  <p>To pytanie wraca najczęściej, więc warto je wyjaśnić raz a dobrze. Każdy wpis jest w jednym
  z dwóch stanów:</p>

  <table class="tab tab--stany">
    <tr>
      <th>Szkic</th>
      <td>Wpis istnieje tylko w panelu. <strong>Nikt z zewnątrz go nie widzi.</strong>
        Możesz go pisać przez tydzień, wracać, poprawiać. Na liście ma dopisek „— Szkic”.</td>
    </tr>
    <tr>
      <th>Opublikowany</th>
      <td>Wpis jest <strong>widoczny na stronie dla wszystkich</strong> i może się pojawić w Google.</td>
    </tr>
  </table>

  <h3>Jak opublikować wpis</h3>
  <ol class="kroki">
    <li>Otwórz wpis do edycji.</li>
    <li>Kliknij niebieski przycisk <strong>Opublikuj</strong> w prawym górnym rogu.</li>
    <li>System zapyta jeszcze raz, dla pewności — kliknij <strong>Opublikuj</strong> ponownie.</li>
    <li>Gotowe. Kliknij <strong>Zobacz wpis</strong>, żeby sprawdzić, jak wyszedł.</li>
  </ol>

  <h3>Jak wycofać wpis ze strony</h3>
  <p>Otwórz wpis, kliknij przy pozycji <strong>Status</strong> słowo „Opublikowany” i zmień na
  <strong>Szkic</strong>. Wpis zniknie ze strony, ale zostanie w panelu — możesz go opublikować
  ponownie, kiedy zechcesz.</p>

  ${spokojnie(`<strong>Wpisy przygotowane przy uruchomieniu strony czekają jako szkice.</strong>
  To przykłady pokazujące, jak wygląda gotowy wpis. Przejrzyj je, popraw dane na prawdziwe
  i opublikuj te, które są aktualne. Resztę możesz spokojnie przenieść do kosza.`)}

  ${uwaga(`Zanim opublikujesz wpis o wolnym lokalu, sprawdź jeszcze raz <strong>adres i metraż</strong>.
  To informacje, na podstawie których ludzie do Was dzwonią.`)}
</section>`);

/* 16. FAQ */
strony.push(`
<section>
  <p class="nr-dzialu">Rozdział 16</p>
  <h2 class="tytul-dzialu">Co zrobić, gdy… — najczęstsze sytuacje</h2>

  <dl class="faq">
    <dt>Zamknęłam okno i nie wiem, czy zapisało</dt>
    <dd>Wejdź w listę wpisów — jeśli wpis tam jest, został zapisany. Panel zapisuje szkice
      automatycznie co jakiś czas, więc rzadko coś przepada.</dd>

    <dt>Wpisałam wszystko, ale nie widzę wpisu na stronie</dt>
    <dd>Prawie na pewno został jako <strong>szkic</strong>. Otwórz go i kliknij „Opublikuj”.
      Sprawdź też, czy edytowałaś właściwy rodzaj wpisu — lokal nie pojawi się w ogłoszeniach.</dd>

    <dt>Pomyliłam się i chcę wrócić do poprzedniej wersji tekstu</dt>
    <dd>W panelu bocznym edytora, przy pozycji „Wersje”, kliknij liczbę wersji. Zobaczysz wszystkie
      wcześniejsze warianty i możesz przywrócić dowolny.</dd>

    <dt>Usunęłam wpis przez pomyłkę</dt>
    <dd>Wejdź w listę wpisów i kliknij u góry <strong>Kosz</strong>. Znajdziesz tam wpis
      i odnośnik „Przywróć”.</dd>

    <dt>Zmieniłam numer telefonu, ale na stronie jest stary</dt>
    <dd>Sprawdź, czy po zmianie kliknęłaś <strong>Zapisz zmiany</strong> na dole Ustawień TRROL.
      Jeśli tak, odśwież stronę klawiszami <strong>Ctrl + F5</strong> — przeglądarka mogła pokazać
      zapamiętaną, starą wersję.</dd>

    <dt>Nie przychodzą wiadomości z formularza</dt>
    <dd>Zajrzyj do folderu <strong>spam</strong>. Sprawdź też w Ustawieniach TRROL, czy pole
      „E-mail dla formularzy” zawiera właściwy adres.</dd>

    <dt>Strona wygląda dziwnie, wszystko się rozjechało</dt>
    <dd>Najpierw odśwież: <strong>Ctrl + F5</strong>. Jeśli to nie pomoże, zadzwoń do nas —
      nie próbuj naprawiać tego samodzielnie w panelu.</dd>

    <dt>Nie mam już wolnych lokali — czy usunąć wpisy?</dt>
    <dd>Wystarczy zmienić ich status na <strong>Szkic</strong>. Strona sama pokaże wtedy komunikat
      „Obecnie nie dysponujemy wolnymi lokalami”. Gdy lokal znów się zwolni, wystarczy opublikować
      wpis ponownie — nie trzeba pisać go od nowa.</dd>
  </dl>
</section>`);

/* 17. Ściąga */
strony.push(`
<section>
  <h2 class="tytul-dzialu">Ściąga na jedną stronę</h2>

  <table class="tab tab--sciaga">
    <tr><th>Wejście do panelu</th><td><code>trrol.pl/wp-admin</code></td></tr>
    <tr><th>Nowy wolny lokal</th><td>Wolne lokale → Dodaj wolny lokal → tytuł z adresem i metrażem → opis → Dane wpisu → <strong>Opublikuj</strong></td></tr>
    <tr><th>Nowe zlecenie dla firm</th><td>Oferty dla firm → Dodaj zlecenie → numer zlecenia + termin ofert → <strong>Opublikuj</strong></td></tr>
    <tr><th>Nowe ogłoszenie</th><td>Ogłoszenia → Dodaj ogłoszenie → etykieta (np. Liczniki) → <strong>Opublikuj</strong></td></tr>
    <tr><th>Nowa aktualność</th><td>Wpisy → Dodaj wpis → zdjęcie + kategoria → <strong>Opublikuj</strong></td></tr>
    <tr><th>Zmiana telefonu, godzin, e-maila</th><td>Ustawienia TRROL → popraw → <strong>Zapisz zmiany</strong></td></tr>
    <tr><th>Nowy regulamin PDF</th><td>Media → wgraj plik → skopiuj adres → Ustawienia TRROL → wklej → Zapisz</td></tr>
    <tr><th>Ukrycie wpisu bez usuwania</th><td>Otwórz wpis → Status → zmień na <strong>Szkic</strong></td></tr>
    <tr><th>Odzyskanie usuniętego wpisu</th><td>Lista wpisów → Kosz → Przywróć</td></tr>
    <tr><th>Strona pokazuje starą treść</th><td><strong>Ctrl + F5</strong></td></tr>
  </table>

  <div class="zapamietaj">
    <h3>Trzy rzeczy, o których warto pamiętać</h3>
    <ol>
      <li><strong>Zapisuj często.</strong> Przycisk „Zapisz szkic” nie publikuje niczego — jest zupełnie bezpieczny.</li>
      <li><strong>Nic nie jest widoczne, dopóki nie klikniesz „Opublikuj”.</strong> Możesz spokojnie eksperymentować.</li>
      <li><strong>Dane kontaktowe zmieniaj tylko w Ustawieniach TRROL.</strong> Poprawią się wtedy w całym serwisie naraz.</li>
    </ol>
  </div>

  <div class="pomoc">
    <p class="pomoc__tytul">Coś nie działa albo masz wątpliwość?</p>
    <p>Nie próbuj naprawiać tego metodą prób i błędów w panelu — po prostu się odezwij.
    Większość spraw da się wyjaśnić przez telefon w kilka minut.</p>
  </div>
</section>`);

/* ------------------------------------------------------------------ szablon */

const css = `
@page { size: A4; margin: 15mm 14mm 14mm; }
@font-face{font-family:'Manrope';font-style:normal;font-weight:400 700;font-display:block;
  src:url(data:font/woff2;base64,${b64(path.join(FONTS, 'manrope-latin-ext.woff2'))}) format('woff2');
  unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;}
@font-face{font-family:'Manrope';font-style:normal;font-weight:400 700;font-display:block;
  src:url(data:font/woff2;base64,${b64(path.join(FONTS, 'manrope-latin.woff2'))}) format('woff2');
  unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;}

*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Manrope',-apple-system,'Segoe UI',Arial,sans-serif;color:#10203A;
  font-size:10.3pt;line-height:1.55;-webkit-print-color-adjust:exact;print-color-adjust:exact}
section{page-break-after:always;break-after:page}
section:last-child{page-break-after:auto;break-after:auto}

h2.tytul-dzialu{font-size:20pt;font-weight:600;line-height:1.15;letter-spacing:-.02em;margin-bottom:13px;
  padding-bottom:10px;border-bottom:3px solid #2C5AA0}
h3{font-size:12pt;font-weight:600;margin:16px 0 7px;color:#10203A}
p{margin-bottom:8px}
p.nr-dzialu{font-size:8.5pt;font-weight:700;letter-spacing:.16em;text-transform:uppercase;
  color:#2C5AA0;margin-bottom:6px}
code{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:.92em;background:#EEF2F8;border:1px solid #D8E2F0;border-radius:4px;padding:1px 5px;color:#1B3B6F}
strong{font-weight:700}
u{text-decoration:underline;text-underline-offset:2px}

/* okładka */
.okladka{height:232mm;overflow:hidden;display:flex;flex-direction:column;justify-content:space-between;
  background:#10203A;color:#fff;margin:-15mm -14mm 0;padding:20mm 20mm}
.okladka__logo{height:72px;width:auto;align-self:flex-start}
.okladka__nad{font-size:10pt;letter-spacing:.18em;text-transform:uppercase;color:#7FA5DC;margin-bottom:14px}
.okladka h1{font-size:34pt;font-weight:600;line-height:1.1;letter-spacing:-.025em;color:#fff}
.okladka__lead{margin-top:20px;font-size:12pt;line-height:1.6;color:#C7D3E4;max-width:135mm}
.okladka__stopka{display:flex;justify-content:space-between;align-items:flex-end;
  border-top:1px solid rgba(255,255,255,.22);padding-top:14px;font-size:9.5pt;color:#9FB2CC}
.okladka__stopka strong{color:#fff}

/* spis treści */
.spis{list-style:none;counter-reset:s;margin-top:8px}
.spis li{counter-increment:s;display:flex;align-items:baseline;gap:10px;padding:7px 0;
  border-bottom:1px dotted #C9CFD8}
.spis li::before{content:counter(s) ".";font-weight:700;color:#2C5AA0;min-width:22px}
.spis span{flex:1}
.spis em{font-style:normal;color:#6B7280;font-size:9.5pt}

/* rysunki */
.rys{margin:13px 0}
.rys__para{display:flex;gap:14px;align-items:flex-start}
.rys__ramka{overflow:hidden;border:1px solid #D3D7DE;border-radius:6px;background:#fff}
.rys__ramka img{display:block;max-width:none}
figcaption{margin-top:7px;font-size:8.8pt;color:#6B7280;font-style:italic}

/* legenda */
ol.legenda{list-style:none;counter-reset:l;margin:11px 0 3px}
ol.legenda li{counter-increment:l;position:relative;padding-left:28px;margin-bottom:5px;font-size:9.9pt;line-height:1.48}
ol.legenda li::before{content:counter(l);position:absolute;left:0;top:1px;width:20px;height:20px;
  border-radius:50%;background:#E8590C;color:#fff;font-size:8.6pt;font-weight:700;
  text-align:center;line-height:20px}

ul.lista{margin:8px 0 8px 18px}
ul.lista li{margin-bottom:6px}
ol.kroki{margin:8px 0 8px 20px}
ol.kroki li{margin-bottom:5px}

/* ramki */
.box{border-radius:7px;padding:9px 13px;margin:10px 0;font-size:9.7pt;line-height:1.5}
.box--tip{background:#EEF4FC;border-left:4px solid #2C5AA0}
.box--warn{background:#FEF3E2;border-left:4px solid #E8590C}
.box--calm{background:#EAF6EE;border-left:4px solid #2F855A}

/* tabele */
.tab{width:100%;border-collapse:collapse;margin:10px 0;font-size:9.8pt}
.tab th,.tab td{border:1px solid #D8DCE3;padding:6px 10px;text-align:left;vertical-align:top}
.tab th{background:#F3F5F8;font-weight:700;width:32%}
.tab thead th{width:auto;background:#10203A;color:#fff}
.tab--rodzaje th{width:auto}
.tab--rodzaje td:nth-child(2){white-space:nowrap}
.tab--slownik th{width:24%}
.tab--stany th{width:22%;background:#EEF4FC;color:#10203A}
.tab--sciaga th{width:34%}

/* faq */
.faq dt{font-weight:700;margin-top:11px;color:#10203A;font-size:10.6pt}
.faq dt::before{content:"▸ ";color:#2C5AA0}
.faq dd{margin-left:15px;color:#3C4658}

/* końcówka */
.zapamietaj{background:#10203A;color:#fff;border-radius:8px;padding:16px 20px;margin-top:20px}
.zapamietaj h3{color:#fff;margin:0 0 9px}
.zapamietaj ol{margin-left:18px}
.zapamietaj li{margin-bottom:6px;color:#DCE4EF}
.zapamietaj strong{color:#fff}
.pomoc{margin-top:16px;border:2px solid #2C5AA0;border-radius:8px;padding:14px 18px;background:#F7FAFF}
.pomoc__tytul{font-weight:700;font-size:11.5pt;color:#2C5AA0;margin-bottom:5px}
`;

const html = `<!DOCTYPE html>
<html lang="pl"><head><meta charset="utf-8">
<title>TRROL — poradnik obsługi strony</title>
<style>${css}</style></head>
<body>
${strony.join('\n')}
</body></html>`;

const out = path.join(__dirname, 'poradnik.html');
fs.writeFileSync(out, html, 'utf8');
console.log(`gotowe: poradnik.html (${Math.round(html.length / 1024)} KB, stron: ${strony.length})`);
