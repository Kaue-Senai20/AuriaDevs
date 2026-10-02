import { getSocket, sendOverdueNotification, sendReminderNotification } from './whatsapp.js';
import { formatPhone } from './notifications.js';

export async function sendTestMessage(phoneNumber, type = 'atraso') {
  const sock = getSocket();
  if (!sock) {
    console.log('❌ WhatsApp não conectado');
    return false;
  }

  const phone = formatPhone(phoneNumber);
  if (!phone) {
    console.log('❌ Número inválido. Use formato: 5511999999999');
    return false;
  }

  const userName = 'Amigo Teste';
  const bookTitle = 'Livro de Teste';

  if (type === 'atraso') {
    return sendOverdueNotification(phone, userName, bookTitle, 3);
  } else {
    return sendReminderNotification(phone, userName, bookTitle, 1);
  }
}
