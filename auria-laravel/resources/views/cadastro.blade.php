<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auria Books - Cadastro</title>
<style>
*{box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif}
body{margin:0;display:flex;min-height:100vh}
.left{flex:1;background:#e30613;color:#fff;padding:48px;display:flex;flex-direction:column}
.left img{width:220px}
.left h2{font-size:2rem;margin:auto 0}
.left a{color:#fff;font-weight:bold}
.right{flex:1.2;background:#f5f5f5;display:flex;align-items:center;justify-content:center;padding:32px}
.card{width:min(560px,100%)}
.card h1{margin:0 0 8px}
label{font-weight:bold;display:block;margin-top:14px}
input{width:100%;height:52px;border:2px solid #777;border-radius:16px;padding:0 16px;margin-top:6px;font-size:1rem;background:#fff}
.row{display:flex;gap:12px}.row>div{flex:1}
button{width:100%;height:56px;background:#e30613;color:#fff;border:0;border-radius:16px;font-size:1.2rem;font-weight:bold;margin-top:24px;cursor:pointer}
.err{color:#e30613;font-weight:bold;display:none}
@media(max-width:900px){body{flex-direction:column}.left h2{font-size:1.4rem}}
</style>
</head>
<body>
<div class="left">
<img src="{{ asset('assets/logo-senai-grande-invertida.png') }}" alt="SENAI" onerror="this.style.display='none'">
<h2>Crie sua conta e acesse<br>o acervo digital</h2>
<p>Já tem uma conta?<br><a href="{{ url('/login') }}">Fazer login</a></p>
</div>
<div class="right">
<div class="card">
<h1>Cadastro</h1>
<p class="err" id="err">As senhas não conferem.</p>
<form id="form" method="GET" action="{{ url('/verificacao-email') }}">
@csrf
<label>Nome completo</label>
<input required placeholder="Digite seu nome completo">
<label>E-mail educacional</label>
<input type="email" required placeholder="nome@aluno.senai.br">
<div class="row">
<div><label>Turma</label><input required placeholder="Ex: ADM 1"></div>
<div><label>Telefone</label><input required placeholder="(00) 00000-0000"></div>
</div>
<label>Senha</label>
<input type="password" id="s1" required placeholder="Digite sua senha">
<label>Confirmar senha</label>
<input type="password" id="s2" required placeholder="Repita sua senha">
<button type="submit">Cadastrar</button>
</form>
</div>
</div>
<script>
document.getElementById('form').addEventListener('submit',function(e){
var a=document.getElementById('s1').value,b=document.getElementById('s2').value;
if(a!==b){e.preventDefault();document.getElementById('err').style.display='block';}
});
</script>
</body>
</html>
