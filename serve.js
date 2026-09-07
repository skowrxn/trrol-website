const http=require('http'),fs=require('fs'),path=require('path'),url=require('url');
const ROOT=path.join(__dirname,'src','design');
const T={'.html':'text/html; charset=utf-8','.js':'text/javascript','.css':'text/css','.png':'image/png','.jpg':'image/jpeg','.jpeg':'image/jpeg','.svg':'image/svg+xml','.md':'text/plain; charset=utf-8'};
http.createServer((req,res)=>{
  let p=decodeURIComponent(url.parse(req.url).pathname);
  if(p==='/')p='/TRROL Nieruchomosci - wszystkie ekrany.dc.html';
  const f=path.join(ROOT,p);
  if(!f.startsWith(ROOT)){res.writeHead(403);return res.end('no');}
  fs.readFile(f,(e,d)=>{ if(e){res.writeHead(404);return res.end('404 '+p);} 
    res.writeHead(200,{'Content-Type':T[path.extname(f).toLowerCase()]||'application/octet-stream'});res.end(d);});
}).listen(4173,()=>console.log('design server on http://localhost:4173'));
