/**
 * Przygotowanie obrazka Open Graph (1200×630) do udostępnień w mediach społecznościowych.
 */
const fs = require('fs');
const path = require('path');

const IMG = path.join(__dirname, 'theme/trrol/assets/img');
const b64 = (f) => fs.readFileSync(path.join(IMG, f)).toString('base64');

const html = `<!DOCTYPE html>
<html lang="pl"><head><meta charset="utf-8">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{width:1200px;height:630px;overflow:hidden;position:relative;
       font-family:'Manrope',-apple-system,'Segoe UI',Arial,sans-serif;background:#10203A}
  .photo{position:absolute;inset:0}
  .photo img{width:100%;height:100%;object-fit:cover}
  .scrim{position:absolute;inset:0;
         background:linear-gradient(90deg,rgba(16,32,58,.96) 0%,rgba(16,32,58,.86) 46%,rgba(16,32,58,.35) 100%)}
  .inner{position:relative;height:100%;padding:66px 72px;display:flex;flex-direction:column;justify-content:space-between}
  .logo{height:64px;width:auto;display:block;align-self:flex-start}
  h1{font-size:62px;font-weight:600;line-height:1.08;letter-spacing:-.02em;color:#fff;max-width:760px}
  p{margin-top:22px;font-size:26px;line-height:1.45;color:#D6DFEC;max-width:680px}
  .foot{display:flex;align-items:center;gap:28px;font-size:22px;color:#fff}
  .foot .sep{width:6px;height:6px;border-radius:50%;background:#7FA5DC;display:block}
  .rule{height:4px;width:96px;background:#2C5AA0;border-radius:2px;margin-bottom:26px}
</style></head><body>
<div class="photo"><img src="data:image/jpeg;base64,${b64('budynek-1.jpg')}" alt=""></div>
<div class="scrim"></div>
<div class="inner">
  <img class="logo" src="data:image/png;base64,${b64('logo-white.png')}" alt="TRROL Nieruchomości">
  <div>
    <div class="rule"></div>
    <h1>Zarządzamy nieruchomościami</h1>
    <p>Administracja kamienic i budynków mieszkalnych w Siemianowicach Śląskich. Od 2017 roku.</p>
  </div>
  <div class="foot">
    <span>trrol.pl</span><span class="sep"></span>
    <span>(32) 228-00-03</span><span class="sep"></span>
    <span>ul. Śląska 80, Siemianowice Śl.</span>
  </div>
</div>
</body></html>`;

fs.writeFileSync(path.join(__dirname, 'docs/og-image.html'), html, 'utf8');
console.log('OK docs/og-image.html');
