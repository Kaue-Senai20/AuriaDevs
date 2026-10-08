import cron from 'node-cron';
import dotenv from 'dotenv';
import { initWhatsApp, getConnectionStatus } from './whatsapp.js';
import { processOverdueNotifications, processReminderNotifications } from './notifications.js';

dotenv.config();

console.log('🚀 Iniciando Bot WhatsApp do Auria Books...\n');
console.log('Confira: o Laravel (php artisan serve) precisa estar rodando com a API.\n');

async function start() {
  await initWhatsApp();

  cron.schedule(process.env.CRON_ATRASADOS || '0 9 * * *', () => {
    console.log('\n⏰ [CRON] Atrasados');
    processOverdueNotifications();
  }, { timezone: 'America/Sao_Paulo' });

  cron.schedule(process.env.CRON_LEMBRETES || '0 10 * * *', () => {
    console.log('\n⏰ [CRON] Lembretes');
    processReminderNotifications();
  }, { timezone: 'America/Sao_Paulo' });

  console.log('💡 Comandos: check = verificação manual | sair = encerra\n');

  process.stdin.setEncoding('utf8');
  process.stdin.on('data', async (data) => {
    const cmd = data.toString().trim().toLowerCase();
    if (cmd === 'check') {
      await processOverdueNotifications();
      await processReminderNotifications();
      console.log('✅ Verificação manual concluída\n');
    } else if (cmd === 'sair' || cmd === 'exit') {
      console.log('👋 Encerrando...');
      process.exit(0);
    }
  });
}

start().catch(console.error);
