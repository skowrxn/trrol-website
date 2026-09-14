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

/* Dane logowania czytane z credentials.txt (plik poza repozytorium). */
function daneDostepu() {
  const plik = path.join(__dirname, '..', 'credentials.txt');
  if (!fs.existsSync(plik)) return null;
  const blok = fs.readFileSync(plik, 'utf8').split(/^WORDPRESS/m)[1] || '';
  const login = (blok.match(/login:\s*(\S+)/) || [])[1];
  const haslo = (blok.match(/hasło:\s*(\S+)/) || [])[1];
  return login && haslo ? { login, haslo } : null;
}

const strony = require('./content.js')({
  dostep: daneDostepu(),
  rysunek,
  rysunekDwuczesciowy,
  logo: b64(path.join(IMG, 'logo-white.png')),
});

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
ol.legenda li::before{content:attr(data-n);position:absolute;left:0;top:1px;width:20px;height:20px;
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
