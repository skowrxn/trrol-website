/**
 * Serwer pomocniczy do zbierania zrzutów panelu.
 *
 * Zalogowana karta przeglądarki wysyła tu gotowy (już opisany) kod strony,
 * a serwer zapisuje go na dysku. Dzięki temu nie trzeba nigdzie wpisywać hasła —
 * korzystamy z sesji, która jest już otwarta w przeglądarce.
 */
const http = require('http');
const fs = require('fs');
const path = require('path');

const OUT = path.join(__dirname, 'raw');
fs.mkdirSync(OUT, { recursive: true });

const PORT = 4180;

http
  .createServer((req, res) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Headers', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');

    if (req.method === 'OPTIONS') {
      res.writeHead(204);
      return res.end();
    }

    if (req.method !== 'POST') {
      if (!req.url.startsWith('/save')) {
        if (req.url === '/' || req.url.startsWith('/?')) req.url = '/poradnik.html';
        const f = path.join(__dirname, decodeURIComponent(req.url.split('?')[0]));
        if (f.startsWith(__dirname) && fs.existsSync(f) && fs.statSync(f).isFile()) {
          const t = { '.html':'text/html; charset=utf-8', '.js':'text/javascript', '.png':'image/png', '.pdf':'application/pdf' }[path.extname(f)] || 'application/octet-stream';
          res.writeHead(200, { 'Content-Type': t });
          return res.end(fs.readFileSync(f));
        }
      }
      if (req.url.startsWith('/annotate.js')) {
        res.writeHead(200, { 'Content-Type': 'text/javascript; charset=utf-8' });
        return res.end(fs.readFileSync(path.join(__dirname, 'annotate.js')));
      }
      res.writeHead(200, { 'Content-Type': 'text/plain' });
      return res.end('capture server ready');
    }

    const name = (new URL(req.url, 'http://x').searchParams.get('name') || 'zrzut')
      .replace(/[^a-z0-9_-]/gi, '');

    const chunks = [];
    req.on('data', (c) => chunks.push(c));
    req.on('end', () => {
      const body = Buffer.concat(chunks);
      const file = path.join(OUT, `${name}.html`);
      fs.writeFileSync(file, body);
      console.log(`zapisano ${name}.html  (${(body.length / 1024).toFixed(0)} KB)`);
      res.writeHead(200, { 'Content-Type': 'text/plain' });
      res.end('ok');
    });
  })
  .listen(PORT, () => console.log(`serwer zrzutów: http://localhost:${PORT}`));
