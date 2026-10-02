# AuriaDevs — Auria Books (Biblioteca Virtual SENAI)

Sistema de biblioteca virtual da escola: acervo digital com login por
e-mail educacional, área do aluno e área do administrador (bibliotecária).

## Estrutura

- `AuriaBooks/Views HTML/` — protótipo navegável em HTML puro (11 telas fiéis ao Canva)
- `AuriaBooks/Documentos/` — documentação do projeto
- `AuriaBooks/Protótipo bot whatsapp/` — protótipo (arquivado) de bot de avisos (fora do escopo atual)
- `auria-laravel/` — projeto Laravel 12 com as telas em Blade (`resources/views`)

## Telas / rotas

`/login` `/cadastro` `/verificacao-email` `/esqueceu-senha`
`/redefinir-senha` `/home` `/perfil` `/dashboard`
`/gerenciamento-acervo` `/gerenciamento-usuarios` `/relatorios`

## Rodando (XAMPP + MySQL porta 3307)

```bash
# PHP do XAMPP + Composer
C:\xampp\php\php.exe C:\xampp\php\composer.phar install

# Banco: criar `auria_books` no MySQL da porta 3307
cp .env.example .env   # ajustar DB_HOST=127.0.0.1 DB_PORT=3307 DB_DATABASE=auria_books DB_USERNAME=root DB_PASSWORD=
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate
C:\xampp\php\php.exe artisan serve --port=8001
```

Abrir `http://127.0.0.1:8001/login`.
