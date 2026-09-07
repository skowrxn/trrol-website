/**
 * Konwersja regulaminów z tekstu na HTML: wersja dla strony WWW i wersja do druku (PDF).
 */
const fs = require('fs');
const path = require('path');

const DOCS = path.join(__dirname, 'docs');

const CONFIG = [
  {
    slug: 'regulamin-uzytkowania-lokali',
    src: 'regulamin-porzadek.txt',
    title: 'Regulamin użytkowania lokali',
    subtitle: 'Regulamin porządku domowego w budynkach zarządzanych przez „TRROL” Nieruchomości Sp. z o.o.',
    note: '',
    skip: 5, // dane spółki + tytuł w pliku źródłowym
  },
  {
    slug: 'rozliczanie-wody-i-sciekow',
    src: 'regulamin-woda.txt',
    title: 'Rozliczanie wody i ścieków',
    subtitle:
      'Regulamin rozliczania zużycia wody wodociągowej i odprowadzenia ścieków w lokalach i budynkach zarządzanych przez „TRROL” Nieruchomości Sp. z o.o. w Siemianowicach Śląskich oraz użytkowania wodomierzy lokalowych i ustalania opłat za wodę i odprowadzenie ścieków.',
    note: 'Zatwierdzony Uchwałą Zarządu Spółki nr 1/12/2023 z dnia 01.12.2023 r. Obowiązuje od 1 stycznia 2024 r.',
    skip: 3,
  },
];

const esc = (s) =>
  s
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

/** Czy linia jest nagłówkiem sekcji (zapisana wersalikami). */
function isHeading(line) {
  const letters = line.replace(/[^\p{L}]/gu, '');
  if (letters.length < 3) return false;
  const lower = letters.replace(/[^\p{Ll}]/gu, '');
  return lower.length === 0 && line.length < 120;
}

/** Czy linia zaczyna paragraf §. */
function isSection(line) {
  return /^§\s*\d+/.test(line);
}

/** Czy linia jest podpunktem (zaczyna się małą literą, literą z nawiasem albo myślnikiem). */
function isSubItem(line) {
  return /^[a-ząćęłńóśżź]/.test(line) || /^[a-z]\)/.test(line) || /^[-–]\s/.test(line);
}

/** Rozbicie linii z wypunktowaniem „•” na akapit + listę. */
function splitBullets(line) {
  if (!line.includes('•')) return null;
  const parts = line.split('•').map((p) => p.trim()).filter(Boolean);
  return { intro: parts.shift(), bullets: parts };
}

function parse(lines) {
  const blocks = [];
  let list = null; // bieżąca lista <ol>
  let sub = null; // bieżąca lista zagnieżdżona <ul>

  const closeSub = () => {
    if (sub && list) {
      list.items[list.items.length - 1].sub = sub;
      sub = null;
    }
  };
  const closeList = () => {
    closeSub();
    if (list && list.items.length) blocks.push(list);
    list = null;
  };

  for (const raw of lines) {
    const line = raw.trim();
    if (!line) continue;

    if (isSection(line)) {
      closeList();
      blocks.push({ type: 'section', text: line });
      list = { type: 'list', items: [] };
      continue;
    }

    if (isHeading(line)) {
      closeList();
      const roman = /^(?:[IVX]+)\./.test(line);
      blocks.push({ type: roman ? 'h2' : 'h3', text: line });
      continue;
    }

    if (!list) list = { type: 'list', items: [] };

    if (isSubItem(line) && list.items.length) {
      if (!sub) sub = [];
      sub.push(line);
      continue;
    }

    closeSub();
    const bullets = splitBullets(line);
    if (bullets) {
      list.items.push({ text: bullets.intro, sub: bullets.bullets });
    } else {
      list.items.push({ text: line });
    }
  }

  closeList();
  return blocks;
}

function render(blocks) {
  const out = [];
  for (const block of blocks) {
    if (block.type === 'h2') {
      out.push(`<h2>${esc(block.text)}</h2>`);
    } else if (block.type === 'h3') {
      out.push(`<h3>${esc(block.text)}</h3>`);
    } else if (block.type === 'section') {
      out.push(`<h3 class="par">${esc(block.text)}</h3>`);
    } else if (block.type === 'list') {
      if (block.items.length === 1 && !block.items[0].sub) {
        out.push(`<p>${esc(block.items[0].text)}</p>`);
        continue;
      }
      out.push('<ol>');
      for (const item of block.items) {
        out.push(`<li>${esc(item.text)}`);
        if (item.sub && item.sub.length) {
          out.push('<ul>');
          for (const s of item.sub) out.push(`<li>${esc(s)}</li>`);
          out.push('</ul>');
        }
        out.push('</li>');
      }
      out.push('</ol>');
    }
  }
  return out.join('\n');
}

const PRINT_CSS = `
@page { size: A4; margin: 20mm 18mm 18mm; }
*{box-sizing:border-box}
body{margin:0;font-family:'Manrope',-apple-system,'Segoe UI',Arial,sans-serif;color:#10203A;
  font-size:10.5pt;line-height:1.6;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.head{border-bottom:2px solid #2C5AA0;padding-bottom:14px;margin-bottom:26px;
  display:flex;justify-content:space-between;align-items:flex-start;gap:24px}
.head img{height:42px;width:auto}
.head__co{font-size:8.5pt;line-height:1.5;color:#6B7280;text-align:right}
h1{font-size:19pt;font-weight:600;line-height:1.25;margin:0 0 10px;letter-spacing:-0.01em}
.sub{font-size:10pt;line-height:1.55;color:#4B5563;margin:0 0 6px}
.note{font-size:9pt;color:#6B7280;margin:0 0 22px;padding-bottom:18px;border-bottom:1px solid #E4E4E0}
h2{font-size:12.5pt;font-weight:600;margin:24px 0 10px;color:#10203A;
  padding-bottom:6px;border-bottom:1px solid #E4E4E0;break-after:avoid}
h3{font-size:11pt;font-weight:600;margin:18px 0 8px;color:#10203A;break-after:avoid}
h3.par{color:#2C5AA0;margin-top:16px}
p{margin:0 0 9px;text-align:justify}
ol,ul{margin:0 0 10px;padding-left:20px}
li{margin-bottom:6px;text-align:justify}
ol>li::marker{color:#2C5AA0;font-weight:600}
ul{list-style:none;padding-left:16px;margin-top:6px}
ul>li{position:relative;padding-left:14px}
ul>li::before{content:"–";position:absolute;left:0;color:#2C5AA0}
.foot{margin-top:28px;padding-top:12px;border-top:1px solid #E4E4E0;
  font-size:8.5pt;color:#9CA3AF;display:flex;justify-content:space-between;gap:16px}
`;

const COMPANY = [
  '„TRROL” Nieruchomości Sp. z o.o.',
  'ul. Śląska 80, 41-100 Siemianowice Śląskie',
  'KRS 0000349526 · REGON 241504484 · NIP 6431746635',
];

for (const cfg of CONFIG) {
  const raw = fs.readFileSync(path.join(DOCS, cfg.src), 'utf8').split(/\r?\n/);
  const body = render(parse(raw.slice(cfg.skip)));

  fs.writeFileSync(path.join(DOCS, `wp-${cfg.slug}.html`), body, 'utf8');

  const logo = fs
    .readFileSync(path.join(__dirname, 'theme/trrol/assets/img/logo-full.png'))
    .toString('base64');

  const printDoc = `<!DOCTYPE html>
<html lang="pl"><head><meta charset="utf-8"><title>${esc(cfg.title)}</title>
<style>${PRINT_CSS}</style></head><body>
<div class="head">
  <img src="data:image/png;base64,${logo}" alt="TRROL Nieruchomości">
  <div class="head__co">${COMPANY.map(esc).join('<br>')}</div>
</div>
<h1>${esc(cfg.title)}</h1>
<p class="sub">${esc(cfg.subtitle)}</p>
<p class="note">${cfg.note ? esc(cfg.note) : 'Regulamin obowiązuje od dnia jego akceptacji przez Zarządcę.'}</p>
${body}
<div class="foot"><span>${esc(cfg.title)} — „TRROL” Nieruchomości Sp. z o.o.</span><span>trrol.pl</span></div>
</body></html>`;

  fs.writeFileSync(path.join(DOCS, `print-${cfg.slug}.html`), printDoc, 'utf8');
  console.log(`OK  ${cfg.slug}  (${body.length} znaków treści)`);
}
