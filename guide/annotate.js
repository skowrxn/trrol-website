/**
 * Nakładka opisująca elementy panelu: numerowane znaczniki, ramki i strzałki.
 *
 * Wstrzykiwana do zalogowanej karty przeglądarki. Nic nie zmienia w danych —
 * dokłada tylko warstwę graficzną na czas zrobienia zrzutu.
 */
(function () {
  const KOLOR = '#E8590C';
  const KOLOR_TLO = '#FFF4E6';

  function usun() {
    document.querySelectorAll('.trrol-annot-layer').forEach((n) => n.remove());
  }

  function warstwa() {
    let l = document.querySelector('.trrol-annot-layer');
    if (l) return l;

    const style = document.createElement('style');
    style.className = 'trrol-annot-layer';
    style.textContent = `
      .trrol-ring{position:absolute;border:3px solid ${KOLOR};border-radius:6px;
        box-shadow:0 0 0 3px rgba(232,89,12,.18);pointer-events:none;z-index:2147483000}
      .trrol-badge{position:absolute;width:34px;height:34px;border-radius:50%;
        background:${KOLOR};color:#fff;font:700 19px/34px -apple-system,'Segoe UI',Arial,sans-serif;
        text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.35);z-index:2147483001;pointer-events:none}
      .trrol-label{position:absolute;background:${KOLOR_TLO};border:2px solid ${KOLOR};
        border-radius:8px;padding:8px 12px;color:#7A2E00;
        font:600 15px/1.35 -apple-system,'Segoe UI',Arial,sans-serif;
        max-width:330px;box-shadow:0 3px 10px rgba(0,0,0,.18);z-index:2147483001;pointer-events:none}
      .trrol-arrow{position:absolute;height:3px;background:${KOLOR};transform-origin:0 50%;
        z-index:2147483000;pointer-events:none}
      .trrol-arrow::after{content:'';position:absolute;right:-2px;top:-6px;width:0;height:0;
        border-left:14px solid ${KOLOR};border-top:7.5px solid transparent;border-bottom:7.5px solid transparent}
    `;
    document.head.appendChild(style);

    l = document.createElement('div');
    l.className = 'trrol-annot-layer';
    l.style.cssText = 'position:absolute;inset:0;pointer-events:none;z-index:2147483000';
    document.body.appendChild(l);
    return l;
  }

  function prostokat(el, ramka) {
    const r = el.getBoundingClientRect();
    let ox = window.scrollX;
    let oy = window.scrollY;

    /* Element leży w ramce (iframe) — doliczamy jej położenie na stronie. */
    if (ramka) {
      const fr = ramka.getBoundingClientRect();
      ox = fr.left + window.scrollX;
      oy = fr.top + window.scrollY;
    }

    return { x: r.left + ox, y: r.top + oy, w: r.width, h: r.height };
  }

  /**
   * Zamiana ramek na wersję „wmurowaną" (srcdoc), żeby przetrwały zapis strony.
   * Bez tego kanwa edytora byłaby po odtworzeniu pusta.
   */
  window.trrolInlineFrames = function () {
    let n = 0;
    document.querySelectorAll('iframe').forEach((f) => {
      let doc;
      try {
        doc = f.contentDocument;
      } catch (e) {
        return;
      }
      if (!doc || !doc.documentElement) return;

      doc.querySelectorAll('script').forEach((s) => s.remove());
      if (!doc.querySelector('base')) {
        const b = doc.createElement('base');
        b.href = 'https://trrol.pl/';
        doc.head.prepend(b);
      }

      f.removeAttribute('src');
      f.srcdoc = '<!DOCTYPE html>' + doc.documentElement.outerHTML;
      n++;
    });
    return n;
  };

  /**
   * @param {Array} items  {sel|box, num, label, side, pad}
   */
  window.trrolAnnotate = function (items) {
    usun();
    const L = warstwa();
    const braki = [];

    items.forEach((it) => {
      let box = it.box;
      if (!box) {
        let el;
        let ramka = null;

        if (it.frame) {
          ramka = document.querySelector(it.frame);
          try {
            el = ramka && ramka.contentDocument
              ? ramka.contentDocument.querySelector(it.sel)
              : null;
          } catch (e) {
            el = null;
          }
        } else {
          el = document.querySelector(it.sel);
        }

        if (!el) {
          braki.push(it.sel);
          return;
        }
        box = prostokat(el, ramka);
      }

      const pad = it.pad === undefined ? 4 : it.pad;

      if (it.ring !== false) {
        const ring = document.createElement('div');
        ring.className = 'trrol-ring';
        ring.style.left = box.x - pad + 'px';
        ring.style.top = box.y - pad + 'px';
        ring.style.width = box.w + pad * 2 + 'px';
        ring.style.height = box.h + pad * 2 + 'px';
        L.appendChild(ring);
      }

      const side = it.side || 'right';
      const badge = document.createElement('div');
      badge.className = 'trrol-badge';
      badge.textContent = it.num;

      let bx, by;
      if (side === 'right') { bx = box.x + box.w + 12; by = box.y + box.h / 2 - 17; }
      else if (side === 'left') { bx = box.x - 46; by = box.y + box.h / 2 - 17; }
      else if (side === 'top') { bx = box.x + box.w / 2 - 17; by = box.y - 46; }
      else { bx = box.x + box.w / 2 - 17; by = box.y + box.h + 12; }

      badge.style.left = Math.max(4, bx) + 'px';
      badge.style.top = Math.max(4, by) + 'px';
      L.appendChild(badge);

      if (it.label) {
        const lab = document.createElement('div');
        lab.className = 'trrol-label';
        lab.textContent = it.label;
        L.appendChild(lab);

        const lw = it.labelWidth || 300;
        lab.style.maxWidth = lw + 'px';

        let lx, ly;
        if (side === 'right') { lx = bx + 46; ly = by - 6; }
        else if (side === 'left') { lx = Math.max(4, bx - lw - 22); ly = by - 6; }
        else if (side === 'top') { lx = bx + 46; ly = by - 6; }
        else { lx = bx + 46; ly = by - 6; }

        const maxX = document.documentElement.scrollWidth - lw - 12;
        lab.style.left = Math.min(Math.max(4, lx), Math.max(4, maxX)) + 'px';
        lab.style.top = ly + 'px';
      }
    });

    return { ok: items.length - braki.length, braki: braki };
  };

  /** Strzałka z punktu do punktu (współrzędne dokumentu). */
  window.trrolArrow = function (x1, y1, x2, y2) {
    const L = warstwa();
    const a = document.createElement('div');
    a.className = 'trrol-arrow';
    const dx = x2 - x1;
    const dy = y2 - y1;
    a.style.left = x1 + 'px';
    a.style.top = y1 + 'px';
    a.style.width = Math.sqrt(dx * dx + dy * dy) - 14 + 'px';
    a.style.transform = `rotate(${Math.atan2(dy, dx)}rad)`;
    L.appendChild(a);
  };

  /** Przycięcie widoku do wskazanego obszaru — żeby zrzut nie miał pustych pól. */
  window.trrolCrop = function (sel, marginesDol) {
    const el = document.querySelector(sel);
    if (!el) return null;
    const b = el.getBoundingClientRect();
    return Math.ceil(b.bottom + window.scrollY + (marginesDol || 40));
  };

  /** Wysyłka gotowego kodu strony na serwer zbierający zrzuty. */
  window.trrolSave = async function (nazwa) {
    window.trrolInlineFrames();
    document.querySelectorAll('script').forEach((s) => s.remove());
    const html = '<!DOCTYPE html>' + document.documentElement.outerHTML;
    await fetch('http://localhost:4180/save?name=' + encodeURIComponent(nazwa), {
      method: 'POST',
      headers: { 'Content-Type': 'text/plain' },
      body: html,
    });
    return nazwa + ': ' + Math.round(html.length / 1024) + ' KB';
  };

  return 'annotator gotowy';
})();
