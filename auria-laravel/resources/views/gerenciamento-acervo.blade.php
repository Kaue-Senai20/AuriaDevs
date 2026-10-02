<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auria Books - Gerenciamento de acervo</title>
<style>
*{box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif}
body{margin:0;background:#f7f7f7}
header{background:#e30613;color:#fff;padding:16px 32px;display:flex;gap:28px;font-size:1.2rem;font-weight:bold;flex-wrap:wrap}
header a{color:#fff;text-decoration:none}header a.on{text-decoration:underline}
.wrap{padding:24px 32px}
.top{display:flex;justify-content:space-between;margin:16px 0}
.fbtn{border:2px solid #777;background:#fff;border-radius:14px;padding:10px 28px;font-size:1rem;cursor:pointer}
.nbtn{background:#e30613;color:#fff;border:0;border-radius:14px;padding:12px 28px;font-size:1rem;font-weight:bold;cursor:pointer}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{border:1px solid #e0e0e0;padding:12px;text-align:left}
th{background:#eee}
.pill{background:#eee;border-radius:12px;padding:4px 14px;font-size:.85rem;font-weight:bold;white-space:nowrap}
.st-d{color:green;font-weight:bold}.st-r{color:#1976d2;font-weight:bold}.st-e{color:#999;font-weight:bold}
.ic{cursor:pointer;font-size:1.3rem;margin-right:14px}.del{color:#e30613}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;padding:20px;z-index:10}
.modal{background:#fff;border-radius:20px;max-width:720px;width:100%;overflow:hidden}
.modal .top2{background:#e30613;color:#fff;padding:14px 24px;font-style:italic;font-weight:bold;font-size:1.3rem;display:flex;justify-content:space-between;align-items:center}
.modal .top2 button{background:none;border:0;color:#fff;font-size:1.8rem;cursor:pointer}
.modal .body{padding:24px}
.modal input,.modal select{width:100%;height:46px;border:2px solid #777;border-radius:12px;padding:0 14px;margin:6px 0 12px;font-size:1rem}
.modal .btn{width:100%;height:52px;background:#e30613;color:#fff;border:0;border-radius:14px;font-size:1.1rem;font-weight:bold;cursor:pointer;margin-top:8px}
.confirm{text-align:center;color:#fff;font-size:1.5rem;font-weight:bold}
.confirm .row{display:flex;gap:24px;justify-content:center;margin-top:20px}
.confirm button{background:#e30613;color:#fff;border:0;border-radius:16px;padding:14px 0;width:260px;font-size:1.2rem;font-weight:bold;cursor:pointer}
.toast{position:fixed;top:40%;left:50%;transform:translateX(-50%);background:#e30613;color:#fff;font-weight:bold;font-size:1.2rem;padding:18px 50px;border-radius:16px;display:none;z-index:20;text-align:center}
</style>
</head>
<body>
<header><span>Auria Books</span><a href="{{ url('/dashboard') }}">Dashboard</a><a class="on" href="{{ url('/gerenciamento-acervo') }}">Acervo</a><a href="{{ url('/gerenciamento-usuarios') }}">Usuários</a><a href="{{ url('/relatorios') }}">Relatórios</a><a href="{{ url('/perfil') }}">Ver perfil</a></header>
<div class="wrap">
<h1>Gerenciamento de acervo</h1><hr>
<div class="top"><button class="fbtn" onclick="alert('Filtro: Todos / Disponível / Emprestado / Renovado')">Filtro ▽</button><button class="nbtn" onclick="novo()">+ Novo livro</button></div>
<table id="tab"></table>
</div>

<div class="overlay" id="ovEdit"><div class="modal">
<div class="top2"><span>Detalhes do livro</span><span><button onclick="fechar('ovEdit')">✕</button></span></div>
<div class="body">
<input id="f_tit" placeholder="Título"><input id="f_aut" placeholder="Autor"><input id="f_ed" placeholder="Editora">
<input id="f_ano" placeholder="Ano"><input id="f_gen" placeholder="Gênero">
<select id="f_st"><option>Disponível</option><option>Emprestado</option><option>Renovado</option></select>
<button class="btn" onclick="pedirSalvar()">Salvar alterações</button>
</div></div></div>

<div class="overlay" id="ovConf"><div class="confirm">
<p>DESEJA SALVAR A ALTERAÇÃO?</p>
<div class="row"><button onclick="salvar()">SIM</button><button onclick="fechar('ovConf')">NÃO</button></div>
</div></div>

<div class="toast" id="tOk">ALTERAÇÃO SALVA COM SUCESSO!<br><small style="font-weight:normal">(Clique em qualquer canto da tela)</small></div>

<script>
var livros=[
{t:'Diario de Um Banana',a:'Jeff Kinney',e:'HarperCollins',y:'14 de mar. ...',g:'Comédia',s:'Disponível'},
{t:'Instalações Elétricas',a:'Júlio N. & Archib',e:'Érica',y:'26 de jun. d...',g:'Técnico',s:'Renovado'},
{t:'A autobiografia de Martin',a:'Martin Luther King',e:'Zahar',y:'2 de out. de...',g:'Autobiografia',s:'Disponível'},
{t:'O Hobbit',a:'J. R. R. Tolkien',e:'Vergara e Riba',y:'15 de jul. de...',g:'Fantasia',s:'Emprestado'}];
var editIdx=-1;
function cls(s){return s==='Disponível'?'st-d':(s==='Renovado'?'st-r':'st-e');}
function render(){
var tb=document.getElementById('tab');
tb.innerHTML='<tr><th>Título</th><th>Autor</th><th>Editora</th><th>Ano</th><th>Gênero</th><th>Status</th><th>Ação</th></tr>';
livros.forEach(function(l,i){
var tr=document.createElement('tr');
tr.innerHTML='<td>'+l.t+'</td><td>'+l.a+'</td><td>'+l.e+'</td><td><span class="pill">'+l.y+'</span></td><td>'+l.g+'</td><td class="'+cls(l.s)+'">• '+l.s+'</td><td><span class="ic" onclick="editar(event,'+i+')">✎</span><span class="ic del" onclick="excluir(event,'+i+')">🗑</span></td>';
tb.appendChild(tr);});
}
function editar(ev,i){ev.stopPropagation();editIdx=i;var l=livros[i];
f_tit.value=l.t;f_aut.value=l.a;f_ed.value=l.e;f_ano.value=l.y;f_gen.value=l.g;f_st.value=l.s;
document.getElementById('ovEdit').style.display='flex';}
function novo(){editIdx=-1;f_tit.value='';f_aut.value='';f_ed.value='';f_ano.value='';f_gen.value='';f_st.value='Disponível';
document.getElementById('ovEdit').style.display='flex';}
function pedirSalvar(){fechar('ovEdit');document.getElementById('ovConf').style.display='flex';}
function salvar(){var l={t:f_tit.value||'Sem título',a:f_aut.value||'—',e:f_ed.value||'—',y:f_ano.value||'—',g:f_gen.value||'—',s:f_st.value};
if(editIdx>=0)livros[editIdx]=l;else livros.push(l);
render();fechar('ovConf');var t=document.getElementById('tOk');t.style.display='block';t.onclick=function(){t.style.display='none';};}
function excluir(ev,i){ev.stopPropagation();if(confirm('Remover "'+livros[i].t+'" do acervo?')){livros.splice(i,1);render();}}
function fechar(id){document.getElementById(id).style.display='none';}
render();
</script>
</body>
</html>
