/**
 * Przygotowanie zapisanej strony do zrzutu: podpięcie adresu bazowego,
 * usunięcie skryptów i wstrzyknięcie warstwy z opisami.
 *
 * Użycie: node prepare.js <nazwa> [plik-konfiguracji-opisow.json]
 */
const fs = require('fs');
const path = require('path');

const RAW = path.join(__dirname, 'raw');
const PREP = path.join(__dirname, 'prepared');
fs.mkdirSync(PREP, { recursive: true });

const nazwa = process.argv[2];
if (!nazwa) {
  console.error('podaj nazwę zrzutu');
  process.exit(1);
}

const annotator = fs.readFileSync(path.join(__dirname, 'annotate.js'), 'utf8');
const configPath = process.argv[3];
const config = configPath && fs.existsSync(configPath)
  ? fs.readFileSync(configPath, 'utf8')
  : null;

let html = fs.readFileSync(path.join(RAW, `${nazwa}.html`), 'utf8');

/* Skrypty strony są zbędne — zrzut ma być statyczny i przewidywalny. */
html = html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '');
html = html.replace(/<script\b[^>]*\/>/gi, '');

/* Adres bazowy, żeby style i grafiki wczytały się z serwera. */
if (!/<base\s/i.test(html)) {
  html = html.replace(/<head([^>]*)>/i, '<head$1>\n<base href="https://trrol.pl/">');
}

/* Warstwa z opisami. */
if (config) {
  const inject = `
<script>${annotator}</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var cfg = ${config};
  if (cfg.items && cfg.items.length) window.trrolAnnotate(cfg.items);
  if (cfg.arrows) cfg.arrows.forEach(function (a) { window.trrolArrow.apply(null, a); });
});
</script>`;
  html = html.replace(/<\/body>/i, inject + '\n</body>');
}

const out = path.join(PREP, `${nazwa}.html`);
fs.writeFileSync(out, html, 'utf8');
console.log(`gotowe: prepared/${nazwa}.html (${Math.round(html.length / 1024)} KB)`);

/* Przesunięcie treści w górę — pozwala wyciąć fragment długiej strony. */
const top = (process.argv[4] || '').replace('--top=', '');
if (top) {
  const shifted = fs.readFileSync(out, 'utf8').replace(
    /<\/head>/i,
    `<style>html{margin-top:-${parseInt(top, 10)}px}</style></head>`
  );
  const out2 = path.join(PREP, `${nazwa}--${top}.html`);
  fs.writeFileSync(out2, shifted, 'utf8');
  console.log(`wycinek: prepared/${nazwa}--${top}.html`);
}
