import { getOverdueLoans, getDueSoonLoans, markNotificationSent, wasNotificationSent } from './database.js';
import { sendOverdueNotification, sendReminderNotification, getConnectionStatus } from './whatsapp.js';

export async function processOverdueNotifications() {
  console.log('🔍 Verificando empréstimos em atraso...');
  
  if (!getConnectionStatus()) {
    console.log('⚠️ WhatsApp não conectado, pulando verificação de atrasados');
    return;
  }

  try {
    const overdueLoans = await getOverdueLoans();
    console.log(`📋 Encontrados ${overdueLoans.length} empréstimos em atraso`);

    for (const loan of overdueLoans) {
      const alreadySent = await wasNotificationSent(loan.emprestimo_id, 'atraso');
      if (alreadySent) continue;

      const phone = formatPhone(loan.usuario_telefone);
      if (!phone) {
        console.log(`⚠️ Telefone inválido para ${loan.usuario_nome}: ${loan.usuario_telefone}`);
        continue;
      }

      const sent = await sendOverdueNotification(
        phone,
        loan.usuario_nome,
        loan.livro_titulo,
        loan.dias_atraso
      );

      if (sent) {
        await markNotificationSent(loan.emprestimo_id, 'atraso');
        console.log(`✅ Atraso notificado: ${loan.usuario_nome} - ${loan.livro_titulo}`);
      } else {
        console.log(`❌ Falha ao notificar: ${loan.usuario_nome}`);
      }
    }
  } catch (err) {
    console.error('❌ Erro ao processar atrasados:', err.message);
  }
}

export async function processReminderNotifications(days = 1) {
  console.log(`🔍 Verificando empréstimos com ${days} dia(s) para vencer...`);
  
  if (!getConnectionStatus()) {
    console.log('⚠️ WhatsApp não conectado, pulando verificação de lembretes');
    return;
  }

  try {
    const dueSoonLoans = await getDueSoonLoans(days);
    console.log(`📋 Encontrados ${dueSoonLoans.length} empréstimos para vencer em ${days} dia(s)`);

    for (const loan of dueSoonLoans) {
      const alreadySent = await wasNotificationSent(loan.emprestimo_id, `lembrete_${days}d`);
      if (alreadySent) continue;

      const phone = formatPhone(loan.usuario_telefone);
      if (!phone) {
        console.log(`⚠️ Telefone inválido para ${loan.usuario_nome}: ${loan.usuario_telefone}`);
        continue;
      }

      const sent = await sendReminderNotification(
        phone,
        loan.usuario_nome,
        loan.livro_titulo,
        loan.dias_restantes
      );

      if (sent) {
        await markNotificationSent(loan.emprestimo_id, `lembrete_${days}d`);
        console.log(`✅ Lembrete enviado: ${loan.usuario_nome} - ${loan.livro_titulo}`);
      } else {
        console.log(`❌ Falha ao enviar lembrete: ${loan.usuario_nome}`);
      }
    }
  } catch (err) {
    console.error('❌ Erro ao processar lembretes:', err.message);
  }
}

export function formatPhone(phone) {
  if (!phone) return null;
  
  let cleaned = phone.replace(/\D/g, '');
  
  if (cleaned.length === 10) {
    cleaned = '55' + cleaned;
  } else if (cleaned.length === 11 && cleaned.startsWith('0')) {
    cleaned = '55' + cleaned.substring(1);
  } else if (cleaned.length === 12 && cleaned.startsWith('55')) {
    // já está correto
  } else if (cleaned.length === 13 && cleaned.startsWith('55')) {
    // já está correto com 9 dígitos
  }
  
  return cleaned.length >= 12 ? cleaned : null;
}

export async function runManualCheck() {
  console.log('\n🔄 Executando verificação manual...\n');
  await processOverdueNotifications();
  await processReminderNotifications(1);
  console.log('\n✅ Verificação manual concluída\n');
}