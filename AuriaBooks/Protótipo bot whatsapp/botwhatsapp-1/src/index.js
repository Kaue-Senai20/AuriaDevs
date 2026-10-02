import cron from 'node-cron';
import { testConnection } from './database.js';
import { initWhatsApp, getConnectionStatus } from './whatsapp.js';
import { processOverdueNotifications, processReminderNotifications, runManualCheck } from './notifications.js';
import { sendTestMessage } from './test-send.js';
import { CRON_SCHEDULE, CRON_REMINDER_SCHEDULE } from './config.js';

console.log('🚀 Iniciando Bot da Biblioteca...\n');

async function start() {
  const dbOk = await testConnection();
  if (!dbOk) {
    console.log('⚠️ Banco não configurado - modo teste (sem DB)');
    console.log('   Configure .env para usar banco real\n');
  }

  await initWhatsApp();

  cron.schedule(CRON_SCHEDULE, () => {
    console.log('\n⏰ [CRON] Verificação de atrasados iniciada');
    processOverdueNotifications();
  }, {
    timezone: 'America/Sao_Paulo'
  });

  cron.schedule(CRON_REMINDER_SCHEDULE, () => {
    console.log('\n⏰ [CRON] Verificação de lembretes (1 dia) iniciada');
    processReminderNotifications(1);
  }, {
    timezone: 'America/Sao_Paulo'
  });

  console.log(`\n📅 Agendamentos ativos:`);
  console.log(`   - Atrasados: ${CRON_SCHEDULE} (horário de Brasília)`);
  console.log(`   - Lembretes (1 dia): ${CRON_REMINDER_SCHEDULE} (horário de Brasília)`);
  console.log('\n💡 Comandos:');
  console.log('   check           - Verificação manual completa');
  console.log('   test 5511999999999  - Envia msg de ATRASO para o número');
  console.log('   lembrete 5511999999999 - Envia msg de LEMBRETE (1 dia)');
  console.log('   sair            - Encerra o bot\n');

  process.stdin.setEncoding('utf8');
  process.stdin.on('data', async (data) => {
    const input = data.toString().trim();
    const parts = input.split(' ');
    const cmd = parts[0].toLowerCase();
    const arg = parts[1];

    if (cmd === 'check') {
      runManualCheck();
    } else if (cmd === 'test' && arg) {
      if (!getConnectionStatus()) {
        console.log('❌ WhatsApp não conectado');
        return;
      }
      console.log(`📤 Enviando teste de ATRASO para ${arg}...`);
      const ok = await sendTestMessage(arg, 'atraso');
      console.log(ok ? '✅ Enviado!' : '❌ Falha');
    } else if (cmd === 'lembrete' && arg) {
      if (!getConnectionStatus()) {
        console.log('❌ WhatsApp não conectado');
        return;
      }
      console.log(`📤 Enviando teste de LEMBRETE para ${arg}...`);
      const ok = await sendTestMessage(arg, 'lembrete');
      console.log(ok ? '✅ Enviado!' : '❌ Falha');
    } else if (cmd === 'sair' || cmd === 'exit') {
      console.log('👋 Encerrando...');
      process.exit(0);
    }
  });
}

start().catch(console.error);