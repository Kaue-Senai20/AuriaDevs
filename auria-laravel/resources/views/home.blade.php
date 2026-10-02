@php $admin = $admin ?? false; @endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auria Books - Biblioteca Virtual</title>
<style>
*{box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif}
body{margin:0;background:#f2f2f2}
header{background:#e30613;color:#fff;padding:18px 40px;display:flex;justify-content:space-between;align-items:center;font-size:1.4rem;font-weight:bold}
header nav a{color:#fff;text-decoration:none;margin-left:20px}
.busca{display:flex;gap:16px;padding:24px 40px;max-width:1200px;margin:auto}
.busca input{height:52px;border:2px solid #777;border-radius:16px;padding:0 20px;font-size:1rem}
#q{flex:1;text-align:center}
#fw{width:260px;text-align:center}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:28px;padding:8px 40px 60px;max-width:1200px;margin:auto}
.card{background:#fff;border-radius:20px;padding:20px;text-align:center;box-shadow:0 8px 24px rgba(0,0,0,.12);cursor:pointer}
.capa{height:200px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:1.1rem;padding:12px;margin-bottom:12px}
.disp{color:green;font-weight:bold}.emp{color:#999;font-weight:bold}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.65);display:none;align-items:center;justify-content:center;padding:20px;z-index:10}
.modal{background:#fff;border-radius:20px;max-width:760px;width:100%;overflow:hidden}
.modal .top{background:#e30613;color:#fff;padding:14px 24px;font-style:italic;font-weight:bold;font-size:1.3rem;display:flex;justify-content:space-between;align-items:center}
.modal .top button{background:none;border:0;color:#fff;font-size:1.8rem;cursor:pointer}
.modal .body{display:flex;gap:24px;padding:24px}
.modal .capa{width:200px;height:280px;flex-shrink:0}
.modal .btn{width:calc(100% - 48px);margin:0 24px 24px;height:54px;background:#e30613;color:#fff;border:0;border-radius:14px;font-size:1.1rem;font-weight:bold;cursor:pointer}
.confirm{text-align:center;color:#fff;font-size:1.6rem;font-weight:bold}
.confirm .row{display:flex;gap:24px;justify-content:center;margin-top:20px}
.confirm button{background:#e30613;color:#fff;border:0;border-radius:16px;padding:14px 0;width:280px;font-size:1.2rem;font-weight:bold;cursor:pointer}
.toast{position:fixed;top:40%;left:50%;transform:translateX(-50%);background:#e30613;color:#fff;font-weight:bold;font-size:1.2rem;padding:18px 60px;border-radius:16px;display:none;z-index:20;text-align:center}
.toast small{display:block;font-weight:normal;font-size:.9rem;margin-top:6px}
.stars{color:#ccc;cursor:pointer;font-size:1.4rem}
.stars.on{color:gold}
</style>
</head>
<body>
<header><span>Auria Books</span><nav>
@if($admin)
<a href="{{ url('/dashboard') }}">Dashboard</a><a href="{{ url('/gerenciamento-acervo') }}">Acervo</a><a href="{{ url('/gerenciamento-usuarios') }}">Usuários</a><a href="{{ url('/relatorios') }}">Relatórios</a>
@endif
<a href="{{ url('/perfil') }}">Ver perfil</a></nav></header>
<div class="busca">
<input id="q" placeholder="Buscar no acervo..." oninput="filtrar()">
<input id="fw" value="Filtro" readonly style="cursor:pointer" onclick="alert('Filtro: Todos / Disponíveis / Emprestados')" title="Filtro">
</div>
<div class="grid" id="grid"></div>

<div class="overlay" id="ov1">
<div class="modal">
<div class="top"><span>Detalhes do livro</span><button onclick="fechar('ov1')">✕</button></div>
<div class="body">
<div class="capa" id="mCapa"></div>
<div>
<h2 id="mTit" style="margin:0"></h2>
<p><b>Autor:</b> <span id="mAut"></span><br>
<b>Nota:</b> <span id="mNota"></span> <span class="stars" id="st">★★★★★</span><br>
<b>Editora:</b> <span id="mEd"></span><br>
<b>Gênero:</b> <span id="mGen"></span><br>
<b>Ano:</b> <span id="mAno"></span></p>
<p><b>Sinopse:</b><br><span id="mSin"></span></p>
<p>• <span id="mSt" style="font-weight:bold"></span></p>
</div>
</div>
<button class="btn" onclick="confirmar()">Manifestar interesse</button>
</div>
</div>

<div class="overlay" id="ov2">
<div class="confirm">
<p>DESEJA MANIFESTAR INTERESSE?</p>
<div class="row"><button onclick="sim()">SIM</button><button onclick="fechar('ov2')">NÃO</button></div>
</div>
</div>

<div class="toast" id="t1">Seu interesse foi salvo com sucesso!<small>(Clique em qualquer canto da tela)</small></div>
<div class="toast" id="t2">Sua avaliação foi salva com sucesso!<small>(Clique em qualquer canto da tela)</small></div>

<script>
var livros=[
{t:'Diario de Um Banana',a:'Jeff Kinney',n:'⭐ 4,8/5 — 28 avaliações',e:'Vergara e Riba',g:'ficção e comédia',y:'2011',s:'Greg Heffley está de volta com mais uma aventura hilariante em "Diário de um Banana - Dias de Cão". O verão chegou, e Greg está ansioso para aproveitar as férias. Mas, como sempre, as coisas não saem como ele planejou.',d:'Disponível — 1 de 1 exemplares',c:'linear-gradient(135deg,#f7c600,#e09b00)',st:'disp',full:'Diário de um Banana. Dias de Cão - Volume 4'},
{t:'Instalações Eletricas',a:'Norberto Nery',n:'⭐ 4,2/5 — 15 avaliações',e:'Érica',g:'técnico',y:'2020',s:'Princípios e aplicações de instalações elétricas: projetos, materiais, SPDA e cabeamento estruturado.',d:'Emprestado',c:'linear-gradient(135deg,#222,#555)',st:'emp',full:'Instalações Elétricas - Princípios e Aplicações'},
{t:'A autobiografia de Martin',a:'Martin Luther King',n:'⭐ 4,9/5 — 41 avaliações',e:'Zahar',g:'autobiografia',y:'2014',s:'A história de uma das maiores vozes por justiça e igualdade do século XX.',d:'Disponível',c:'linear-gradient(135deg,#7a4a21,#3d2412)',st:'disp',full:'A autobiografia de Martin Luther King'},
{t:'Python para leigos',a:'John Paul Mueller',n:'⭐ 4,5/5 — 33 avaliações',e:'Alta Books',g:'técnico',y:'2021',s:'Aprenda a trabalhar com Python: sintaxe, funções e seu primeiro programa.',d:'Disponível',c:'linear-gradient(135deg,#306998,#FFD43B)',st:'disp',full:'Python para leigos'},
{t:'Fotovoltaica',a:'Vários autores',n:'⭐ 4,3/5 — 12 avaliações',e:'Érica',g:'técnico',y:'2019',s:'Conceitos e aplicações: sistemas isolados e conectados à rede. 2ª edição.',d:'Disponível',c:'linear-gradient(135deg,#1a3a6b,#4a90d9)',st:'disp',full:'Fotovoltaica - Conceitos e Aplicações'},
{t:'O Hobbit',a:'J. R. R. Tolkien',n:'⭐ 4,9/5 — 87 avaliações',e:'Vergara e Riba',g:'fantasia',y:'2012',s:'Bilbo Bolseiro parte em uma jornada inesperada com anões rumo à Montanha Solitária.',d:'Emprestado',c:'linear-gradient(135deg,#2d5a3d,#7ab648)',st:'emp',full:'O Hobbit'}];
function render(){
var g=document.getElementById('grid');g.innerHTML='';
livros.forEach(function(l,i){
var d=document.createElement('div');d.className='card';
d.innerHTML='<div class="capa" style="background:'+l.c+'">'+l.t+'</div><h3>'+l.t+'</h3><p class="'+l.st+'">'+(l.st==='disp'?'Disponível':'Emprestado')+'</p>';
d.onclick=function(){abrir(i)};g.appendChild(d);});
}
function abrir(i){var l=livros[i];
document.getElementById('mCapa').style.background=l.c;document.getElementById('mCapa').textContent=l.t;
document.getElementById('mTit').textContent=l.full;document.getElementById('mAut').textContent=l.a;
document.getElementById('mNota').textContent=l.n;document.getElementById('mEd').textContent=l.e;
document.getElementById('mGen').textContent=l.g;document.getElementById('mAno').textContent=l.y;
document.getElementById('mSin').textContent=l.s;document.getElementById('mSt').textContent=l.d;
document.getElementById('ov1').style.display='flex';}
function fechar(id){document.getElementById(id).style.display='none';}
function confirmar(){fechar('ov1');document.getElementById('ov2').style.display='flex';}
function sim(){fechar('ov2');toast('t1');}
function toast(id){var t=document.getElementById(id);t.style.display='block';
t.onclick=function(){t.style.display='none';};}
function filtrar(){var q=document.getElementById('q').value.toLowerCase();
var cards=document.getElementById('grid').children;
livros.forEach(function(l,i){cards[i].style.display=l.t.toLowerCase().includes(q)?'':'none';});}
document.getElementById('st').onclick=function(){this.className='stars on';toast('t2');};
render();
</script>
</body>
</html>
