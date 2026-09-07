# TRROL Nieruchomości — strona trrol.pl

Wdrożenie serwisu WordPress dla „TRROL” Nieruchomości Sp. z o.o. (Siemianowice Śląskie),
na podstawie makiety z Claude Design i ustaleń ze spotkania z klientką.

## Co jest w repozytorium

```
theme/trrol/          dedykowany motyw WordPress (bez page buildera)
content/              treści stron i skrypt tworzący przykładowe wpisy
docs/                 regulaminy: tekst źródłowy, HTML na stronę i gotowe PDF-y
build-regulaminy.js   konwersja regulaminów z .docx do HTML + wersji do druku
deploy-content.sh     skrypt tworzący strony, menu, ustawienia i podpinający PDF-y
serve.js              lokalny serwer statyczny do podglądu makiety źródłowej
```

Nie trafiają do repozytorium: `credentials.txt`, `backup/`, `src/` (materiały źródłowe
z Dysku Google, zawierają dane osobowe) oraz `shots/`.

## Serwer

- Hosting: cyber_Folks, DirectAdmin, `s67.cyber-folks.pl`
- Katalog: `~/domains/trrol.pl/public_html`
- WordPress 7.1 (pl_PL), PHP 8.2, MySQL — baza `trrol_wp`
- Dostęp: `ssh -o MACs=hmac-sha2-512-etm@openssh.com trrol@s67.cyber-folks.pl -p 222`

## Struktura serwisu

| Adres | Co to jest |
| --- | --- |
| `/` | strona główna (szablon `front-page.php`) |
| `/wolne-lokale/` | wykaz wolnych lokali — typ treści `trrol_lokal` |
| `/oferty-dla-firm/` | zapytania ofertowe — typ treści `trrol_oferta` |
| `/ogloszenia/` | komunikaty dla mieszkańców — typ treści `trrol_ogloszenie` |
| `/aktualnosci/` | blog firmowy — standardowe wpisy WordPressa |
| `/o-nas/` | strona o firmie (szablon `page-o-nas.php`) |
| `/kontakt/` | dane działów, formularz, godziny, mapa (`page-kontakt.php`) |
| `/regulaminy/` | dwa regulaminy + PDF-y do pobrania (szablon `template-regulaminy.php`) |
| `/polityka-prywatnosci/` | polityka prywatności (do weryfikacji prawnej) |

Wszystkie wpisy korzystają ze wspólnego szablonu `template-parts/entry-single.php`,
zgodnie z notatką w makiecie.

## Formularze

Trzy formularze, bez wtyczek — obsługa w `inc/forms.php`, wysyłka przez `wp_mail()`
na adres z ustawień motywu.

| Formularz | Gdzie |
| --- | --- |
| kontaktowy | `/kontakt/` |
| zapytanie o lokal | `/wolne-lokale/` i każdy wpis lokalu |
| oferta firmy (z załącznikiem PDF) | wpis zlecenia z otwartym naborem |

Zabezpieczenia: nonce WordPressa, pole-pułapka (honeypot), minimalny czas wypełniania,
limit 6 zgłoszeń na godzinę z jednego adresu IP, wymagana zgoda RODO.
Załączniki przyjmowane tylko jako PDF do 8 MB, zapisywane w
`wp-content/uploads/trrol-oferty/` z blokadą dostępu z zewnątrz (HTTP 403).

## Edycja treści przez klienta

Wszystko z poziomu kokpitu WordPressa, bez kodu:

- **Wolne lokale / Oferty dla firm / Ogłoszenia / Aktualności** — osobne pozycje w menu bocznym,
  każdy typ ma własne pola (powierzchnia, układ, numer zlecenia, termin ofert, etykieta).
- **Ustawienia TRROL** — telefony, e-mail, adres, godziny otwarcia, adres mapy i pliki PDF regulaminów.
  Zmiana w jednym miejscu aktualizuje całą stronę.
- Na pulpicie kokpitu jest kafelek z krótką instrukcją.

Gdy nie ma żadnych lokali lub zleceń, strona pokazuje zaprojektowany stan pusty
z zachętą do kontaktu — nie wygląda na niedokończoną.

## Regulaminy

Źródło: `.docx` od klientki. `build-regulaminy.js` zamienia je na HTML (paragrafy §,
listy numerowane, podpunkty) w dwóch wersjach: treść na stronę i dokument do druku,
z którego headless Chrome generuje PDF z logo i stopką.

```bash
node build-regulaminy.js
# potem: chrome --headless --print-to-pdf=... docs/print-<slug>.html
```

## Design

Makieta: eksport z Claude Design (9 ekranów + widok mobilny). Motyw odtwarza jej
system: Manrope, kolory `#10203A` / `#2C5AA0` / `#F5F5F3`, kontener 1240 px,
karty z obramowaniem `#E4E4E0` i promieniem 12 px, ciemne pasy kontaktowe.
Punkty łamania: 1180 px (menu mobilne), 1024 px, 760 px.
