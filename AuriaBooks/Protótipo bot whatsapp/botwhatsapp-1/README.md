# Bot WhatsApp - Biblioteca

Bot para envio automático de notificações de empréstimos em atraso e lembretes de devolução.

## Funcionalidades

- 📚 **Atrasados**: Notifica usuários com livros vencidos
- ⏰ **Lembretes**: Avisa 1 dia antes da data de devolução
- 🔄 **Agendamento automático**: Roda via cron (configurável)
- 🛡 **Anti-duplicação**: Não envia mesma notificação duas vezes
- 📱 **Baileys**: Conexão WhatsApp Web sem Chrome/Puppeteer

## Pré-requisitos

- Node.js 18+
- PostgreSQL
- WhatsApp no celular para escanear QR Code

## Instalação

```bash
# 1. Instalar dependências
npm install

# 2. Configurar banco de dados
# Execute o arquivo database.sql no PostgreSQL

# 3. Configurar variáveis de ambiente
cp .env.example .env
# Edite .env com suas credenciais

# 4. Iniciar
npm start
```

## Configuração (.env)

```env
# Banco de Dados
DB_HOST=localhost
DB_PORT=5432
DB_NAME=biblioteca
DB_USER=postgres
DB_PASSWORD=sua_senha

# WhatsApp
SESSION_NAME=biblioteca-bot

# Agendamento (cron)
CRON_SCHEDULE=0 9 * * *        # Atrasados: todo dia 9h
CRON_REMINDER_SCHEDULE=0 10 * * *  # Lembretes: todo dia 10h
```

## Comandos do Terminal

| Comando | Ação |
|---------|------|
| `check` | Executa verificação manual agora |
| `sair` | Encerra o bot |

## Estrutura das Tabelas

O bot espera estas tabelas (criadas via `database.sql`):

- `usuarios` - nome, telefone, email
- `livros` - titulo, autor, isbn
- `emprestimos` - usuario_id, livro_id, data_devolucao_prevista, devolvido
- `notificacoes_enviadas` - controle de envios (evita spam)

## Personalização de Mensagens

Edite `src/whatsapp.js` nas funções:
- `sendOverdueNotification()` - mensagem de atraso
- `sendReminderNotification()` - mensagem de lembrete

## Produção

Para rodar em produção (Linux/VPS):

```bash
# Com PM2
npm install -g pm2
pm2 start src/index.js --name biblioteca-bot
pm2 save
pm2 startup
```

## Logs

Os logs aparecem no console. Para arquivo:
```bash
npm start > bot.log 2>&1
```

## Troubleshooting

**QR Code não aparece**: Delete a pasta `sessions/` e reinicie.

**Erro de conexão DB**: Verifique se o PostgreSQL está rodando e credenciais no `.env`.

**Mensagens não enviam**: Confirme se o número tem DDD + 9 dígitos (ex: 5511999999999).