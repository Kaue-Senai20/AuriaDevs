<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AuriaBooks - Login</title>
<style>
main{height:100vh;background-color:#e30613}
#background-login{background-image:url("{{ asset('assets/background-biblioteca.jpg') }}");background-size:cover;background-repeat:no-repeat;filter:brightness(50%);height:100%;width:100%}
#background-login-in{width:40%;position:absolute;top:45%;left:50px}
#background-login-in p{font-size:2.5rem;margin:0;color:white;font-weight:bold}
#box-login{width:30%;min-width:320px;border-radius:15px;background-color:white;position:absolute;top:15%;right:8%;padding:20px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center}
#box-login h1{font-size:3rem;margin:10px 0}
#box-login form{display:flex;flex-direction:column;width:85%}
form label{font-size:1.3rem;font-weight:bold}
form input,form button{width:100%;height:55px;border:2px solid #777;border-radius:15px;margin-top:10px;margin-bottom:20px}
form input{box-sizing:border-box;padding:10px;font-size:1.1rem}
form button{background-color:#e30613;font-size:1.4rem;color:white;font-weight:bold;cursor:pointer;border-color:#e30613}
form p{font-size:1.1rem;text-align:center}
form a{color:#e30613}
.linkvermelho{color:#e30613;text-decoration:none;text-align:center;display:block;margin-top:4px}
.logo-grande{height:110px;width:auto;position:absolute;left:50px;top:40px;z-index:1000}
.err{color:#e30613;font-weight:bold;text-align:center;display:none}
@media(max-width:900px){#background-login-in{display:none}#box-login{width:90%;right:5%;left:5%}.logo-grande{height:70px;left:20px;top:20px}}
</style>
</head>
<body style="margin:0;font-family:'Montserrat',Arial,sans-serif">
<main>
<img class="logo-grande" src="{{ asset('assets/logo-senai-grande.png') }}" alt="SENAI">
<div id="background-login"></div>
<div id="background-login-in">
<p>Bem-vindo à Biblioteca Virtual!</p>
<hr>
<p>O conhecimento é a semente para um futuro brilhante!</p>
</div>
<div id="box-login">
<h1>Login</h1>
<p class="err" id="err">Credenciais de login incorretas!</p>
<form method="GET" action="{{ url('/home') }}">
@csrf
<label for="email">E-mail educacional</label>
<input type="email" name="email" placeholder="Digite seu E-mail educacional" required>
<label for="senha">Senha</label>
<input type="password" name="senha" placeholder="Digite sua senha" required>
<a href="{{ url('/esqueceu-senha') }}" class="linkvermelho">Esqueci minha senha</a>
<button type="submit">Entrar</button>
<p>Não tem uma conta? <a href="{{ url('/cadastro') }}">Cadastre-se</a></p>
</form>
</div>
</main>
</body>
</html>
