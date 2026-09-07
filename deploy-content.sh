#!/bin/bash
# Wdrożenie struktury i treści serwisu TRROL.
set -e
cd ~/domains/trrol.pl/public_html
WP="wp --skip-plugins --skip-themes=."
WP="wp"

say() { printf '\n\033[1m== %s\033[0m\n' "$1"; }

# ---------------------------------------------------------------- Ustawienia
say "Ustawienia serwisu"
$WP option update blogname "TRROL Nieruchomości"
$WP option update blogdescription "Zarządzanie i administracja nieruchomościami — Siemianowice Śląskie"

php -r '
require "wp-load.php";
update_option( "trrol_options", array(
  "firma" => "„TRROL” Nieruchomości Sp. z o.o.",
  "ulica" => "ul. Śląska 80",
  "miasto" => "41-100 Siemianowice Śl.",
  "nip" => "6431746635",
  "regon" => "241504484",
  "krs" => "0000349526",
  "tel_sekretariat" => "(32) 228-00-03",
  "tel_administracja" => "(32) 228-00-03",
  "tel_ksiegowosc" => "(32) 228-00-03",
  "tel_windykacja" => "(32) 228-00-03",
  "faks" => "(32) 220-45-69",
  "email" => "biuro@trrol.pl",
  "email_formularze" => "biuro@trrol.pl",
  "rok_zalozenia" => "2017",
  "godziny_pn" => "7:00–17:00",
  "godziny_wt" => "7:00–15:00",
  "godziny_sr" => "7:00–15:00",
  "godziny_cz" => "7:00–15:00",
  "godziny_pt" => "7:00–13:00",
  "godziny_weekend" => "nieczynne",
  "mapa_embed" => "https://www.google.com/maps?q=" . rawurlencode( "ul. Śląska 80, 41-100 Siemianowice Śląskie" ) . "&output=embed",
) );
echo "Zapisano ustawienia TRROL\n";
'

# ---------------------------------------------------------------- Logo
say "Logo"
LOGO_ID=$($WP media import wp-content/themes/trrol/assets/img/logo-full.png --title="TRROL Nieruchomości — logo" --porcelain 2>/dev/null || true)
if [ -n "$LOGO_ID" ]; then
  $WP theme mod set custom_logo "$LOGO_ID"
  echo "Logo ustawione (ID $LOGO_ID)"
fi

# ---------------------------------------------------------------- Regulaminy PDF
say "Pliki PDF regulaminów"
PDF1=$($WP media import /tmp/trrol-content/regulamin-uzytkowania-lokali.pdf --title="Regulamin użytkowania lokali" --porcelain)
PDF2=$($WP media import /tmp/trrol-content/rozliczanie-wody-i-sciekow.pdf --title="Regulamin rozliczania wody i ścieków" --porcelain)
PDF1_URL=$($WP post get "$PDF1" --field=guid)
PDF2_URL=$($WP post get "$PDF2" --field=guid)
echo "PDF 1: $PDF1_URL"
echo "PDF 2: $PDF2_URL"

php -r '
require "wp-load.php";
$o = get_option( "trrol_options", array() );
$o["pdf_regulamin"] = $argv[1];
$o["pdf_woda"] = $argv[2];
update_option( "trrol_options", $o );
echo "Podpięto PDF-y do ustawień\n";
' "$PDF1_URL" "$PDF2_URL"

# ---------------------------------------------------------------- Strony
say "Strony"

mkpage() { # slug tytul plik-tresci [rodzic] [szablon]
  local slug="$1" title="$2" file="$3" parent="${4:-0}" tpl="${5:-}"
  local id
  id=$($WP post list --post_type=page --name="$slug" --field=ID --post_status=any | head -1)
  if [ -z "$id" ]; then
    id=$($WP post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" \
         --post_parent="$parent" --post_content="$(cat "$file")" --porcelain)
    echo "utworzono: $title (ID $id)"
  else
    $WP post update "$id" --post_title="$title" --post_content="$(cat "$file")" --post_parent="$parent" >/dev/null
    echo "zaktualizowano: $title (ID $id)"
  fi
  [ -n "$tpl" ] && $WP post meta update "$id" _wp_page_template "$tpl" >/dev/null
  echo "$id"
}

echo "" > /tmp/trrol-content/pusta.html

ID_HOME=$(mkpage "strona-glowna" "Strona główna" /tmp/trrol-content/pusta.html | tail -1)
ID_ONAS=$(mkpage "o-nas" "O nas" /tmp/trrol-content/o-nas.html | tail -1)
ID_KONTAKT=$(mkpage "kontakt" "Kontakt" /tmp/trrol-content/pusta.html | tail -1)
ID_AKT=$(mkpage "aktualnosci" "Aktualności" /tmp/trrol-content/pusta.html | tail -1)
ID_PRIV=$(mkpage "polityka-prywatnosci" "Polityka prywatności" /tmp/trrol-content/polityka-prywatnosci.html | tail -1)

ID_REG=$(mkpage "regulaminy" "Regulaminy" /tmp/trrol-content/pusta.html 0 "template-regulaminy.php" | tail -1)
ID_REG1=$(mkpage "regulamin-uzytkowania-lokali" "Regulamin użytkowania lokali" /tmp/trrol-content/wp-regulamin-uzytkowania-lokali.html "$ID_REG" "template-regulaminy.php" | tail -1)
ID_REG2=$(mkpage "rozliczanie-wody-i-sciekow" "Rozliczanie wody i ścieków" /tmp/trrol-content/wp-rozliczanie-wody-i-sciekow.html "$ID_REG" "template-regulaminy.php" | tail -1)

$WP post update "$ID_REG1" --post_excerpt="Zasady korzystania z lokali i części wspólnych" --menu_order=1 >/dev/null
$WP post update "$ID_REG2" --post_excerpt="Odczyty wodomierzy, zaliczki i rozliczenia półroczne" --menu_order=2 >/dev/null

# Strona główna i strona wpisów
$WP option update show_on_front page
$WP option update page_on_front "$ID_HOME"
$WP option update page_for_posts "$ID_AKT"
$WP option update wp_page_for_privacy_policy "$ID_PRIV"
echo "Strona główna: $ID_HOME, Aktualności: $ID_AKT, Prywatność: $ID_PRIV"

# Zdjęcie wyróżniające dla „O nas”
IMG2=$($WP media import wp-content/themes/trrol/assets/img/budynek-2.jpg --title="Budynek w zarządzie TRROL" --porcelain 2>/dev/null || true)
[ -n "$IMG2" ] && $WP post meta update "$ID_ONAS" _thumbnail_id "$IMG2" >/dev/null && echo "Ustawiono zdjęcie na stronie O nas"

# ---------------------------------------------------------------- Menu
say "Menu główne"
$WP menu delete "Menu główne" 2>/dev/null || true
$WP menu create "Menu główne" >/dev/null
$WP menu item add-custom "Menu główne" "Strona główna" "$(  $WP option get home )/" >/dev/null
$WP menu item add-post-type "Menu główne" archive trrol_lokal --title="Wolne lokale" >/dev/null 2>&1 || \
  $WP menu item add-custom "Menu główne" "Wolne lokale" "$($WP option get home)/wolne-lokale/" >/dev/null
$WP menu item add-custom "Menu główne" "Oferty dla firm" "$($WP option get home)/oferty-dla-firm/" >/dev/null
$WP menu item add-custom "Menu główne" "Ogłoszenia" "$($WP option get home)/ogloszenia/" >/dev/null
$WP menu item add-post "Menu główne" "$ID_AKT" --title="Aktualności" >/dev/null
$WP menu item add-post "Menu główne" "$ID_ONAS" --title="O nas" >/dev/null
$WP menu item add-post "Menu główne" "$ID_KONTAKT" --title="Kontakt" >/dev/null
$WP menu location assign "Menu główne" primary >/dev/null
echo "Menu przypisane do lokalizacji primary"

# ---------------------------------------------------------------- Kategorie
say "Kategorie aktualności"
for kat in Remonty Inwestycje Administracja Firma; do
  $WP term create category "$kat" >/dev/null 2>&1 || true
done
$WP term list category --fields=name --format=csv | tail -n +2 | tr '\n' ' '
echo ""

say "Gotowe"
