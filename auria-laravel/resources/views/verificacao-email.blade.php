<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auria Books - Verifique seu e-mail</title>
<style>
*{box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif}
body{margin:0;display:flex;min-height:100vh}
.left{flex:1;background:#e30613;color:#fff;padding:48px;display:flex;flex-direction:column}
.left img{width:220px}
.left h2{font-size:1.8rem;margin:auto 0}
.right{flex:1.4;background:#f5f5f5;display:flex;align-items:center;justify-content:center;text-align:center;padding:32px}
.icon{width:140px;height:140px;background:#e30613;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:4rem;margin:0 auto;color:#fff}
.btn{display:inline-block;background:#e30613;color:#fff;padding:14px 48px;border-radius:16px;text-decoration:none;font-weight:bold;margin-top:16px}
a.red{color:#e30613;font-weight:bold;text-decoration:none}
#toast{display:none;background:#e30613;color:#fff;padding:12px 28px;border-radius:12px;margin-top:12px;font-weight:bold}
@media(max-width:900px){body{flex-direction:column}}
</style>
</head>
<body>
<div class="left">
<img src="{{ asset('assets/logo-senai-grande-invertida.png') }}" alt="SENAI" onerror="this.style.display='none'">
<h2>Entre no acervo digital<br>da sua escola já!</h2>
</div>
<div class="right">
<div>
<div class="icon">✉️</div>
<h1>Verifique seu e-mail</h1>
<p>Enviamos um link de confirmação para<br><b>nome@aluno.senai.br</b></p>
<p>Acesse sua caixa de entrada e clique no<br>link para ativar sua conta. Verifique<br>também a pasta de spam.</p>
<a class="btn" href="{{ url('/login') }}">Voltar ao login</a>
<p>Não recebeu o e-mail? <a class="red" href="#" id="re">Reenviar</a></p>
<div id="toast">E-mail de confirmação reenviado!</div>
</div>
</div>
<script>
document.getElementById('re').addEventListener('click',function(e){e.preventDefault();document.getElementById('toast').style.display='block';});
</script>
</body>
</html>
