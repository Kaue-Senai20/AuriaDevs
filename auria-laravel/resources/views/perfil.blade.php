<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil - Auria Books</title>
<style>
*{box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif}
body{margin:0;background:#f2f2f2}
header{background:#e30613;color:#fff;padding:18px 40px;display:flex;justify-content:space-between;font-size:1.4rem;font-weight:bold}
header a{color:#fff}
.card{background:#fff;max-width:820px;margin:32px auto;padding:40px;border-radius:20px;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.top{display:flex;align-items:center;gap:20px}
.avatar{width:110px;height:110px;border-radius:50%;background:#e30613;color:#fff;display:flex;align-items:center;justify-content:center;font-size:2.4rem;font-weight:bold}
table{width:100%;border-collapse:collapse;margin-top:20px}
td{border:1px solid #ddd;padding:12px}
td.k{background:#eee;font-weight:bold;width:130px}
.edit{cursor:pointer;float:right}
h3{text-align:center;margin-top:32px}
.dev{color:green;font-weight:bold}.atr{color:red;font-weight:bold}.ren{color:#1976d2;font-weight:bold}.emp{color:#999;font-weight:bold}.inte{color:#8e24aa;font-weight:bold}
</style>
</head>
<body>
<header><span>Auria Books</span><a href="{{ url('/perfil') }}">Ver perfil</a></header>
<div class="card">
<div class="top"><div class="avatar">AL</div><h2>Aluno Exemplo</h2></div>
<table>
<tr><td class="k">E-mail</td><td>aluno@aluno.senai.br</td></tr>
<tr><td class="k">Turma</td><td>DEV 2</td></tr>
<tr><td class="k">Telefone</td><td><span id="tel">+55 19 90000-0000</span><span class="edit" onclick="editar('tel')">✎</span></td></tr>
<tr><td class="k">Senha</td><td>****** <span class="edit" onclick="location.href='{{ url('/redefinir-senha') }}'">✎</span></td></tr>
</table>
<h3>Histórico de empréstimos</h3>
<table>
<tr style="background:#eee;font-weight:bold"><td>Livro</td><td>Data</td><td>Status</td></tr>
<tr><td>Diario de Um Banana</td><td>2026-07-21 12:49:23</td><td class="dev">• Devolvido</td></tr>
<tr><td>Diario de Um Banana</td><td>2026-08-20 10:02:51</td><td class="atr">• Atrasado</td></tr>
<tr><td>Diario de Um Banana</td><td>2026-08-06 10:02:51</td><td class="ren">• Renovado</td></tr>
<tr><td>Diario de Um Banana</td><td>2026-07-23 09:32:47</td><td class="emp">• Emprestado</td></tr>
<tr><td>Diario de Um Banana</td><td>2026-07-23 08:15:36</td><td class="inte">• Interesse</td></tr>
</table>
</div>
<script>
function editar(id){var v=prompt('Novo valor:',document.getElementById(id).textContent);if(v)document.getElementById(id).textContent=v;}
</script>
</body>
</html>
