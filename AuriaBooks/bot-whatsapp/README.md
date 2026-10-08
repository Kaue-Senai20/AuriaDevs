# Bot WhatsApp — Auria Books (v2, via API)

Envia avisos de atraso e lembretes de devolução lendo a **API do Laravel**
— sem acesso direto ao banco.

## Rodando

```bash
# 1. API ligada (outro terminal)
cd auria-laravel
php artisan serve --port=8001

# 2. Bot (neste terminal)
cd bot-whatsapp
npm install
cp .env.example .env   # no Windows: copy .env.example .env
# edite o .env: API_URL + API_TOKEN (mesmo valor do .env do Laravel)
npm start
```

Escaneie o QR Code com o WhatsApp do celular da biblioteca.

## Testando sem multa de verdade

Para ver o bot funcionando, crie um empréstimo atrasado de teste:

```sql
INSERT INTO loans (book_id, user_id, loan_date, due_date, status)
VALUES (1, 1, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 2 DAY, 'borrowed');
```

Precisa existir ao menos 1 livro em `collection`. Depois digite `check`
no terminal do bot.

## Comandos

| Comando | Ação                        |
| ------- | --------------------------- |
| `check` | Verificação manual agora    |
| `sair`  | Encerra o bot               |

## Anti-duplicação

Notificações já enviadas ficam em `enviadas.json` (não commitar).
Apague o arquivo para reenviar em testes.
