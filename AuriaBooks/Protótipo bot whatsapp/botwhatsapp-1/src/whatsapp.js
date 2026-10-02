import makeWASocket, { useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import pino from 'pino';
import qrcode from 'qrcode-terminal';
import { SESSION_NAME } from './config.js';

let sock = null;
let isConnected = false;

export async function initWhatsApp() {
  const { state, saveCreds } = await useMultiFileAuthState(`./sessions/${SESSION_NAME}`);
  const { version } = await fetchLatestBaileysVersion();

  sock = makeWASocket({
    version,
    auth: state,
    printQRInTerminal: false,
    logger: pino({ level: 'silent' }),
    browser: ['Biblioteca Bot', 'Chrome', '1.0.0'],
  });

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      console.log('\n📱 Escaneie o QR Code com o WhatsApp:');
      qrcode.generate(qr, { small: true });
    }

    if (connection === 'open') {
      isConnected = true;
      console.log('✅ WhatsApp conectado!');
    }

    if (connection === 'close') {
      isConnected = false;
      const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
      console.log('❌ Conexão fechada:', lastDisconnect?.error?.message);
      if (shouldReconnect) {
        console.log('🔄 Reconectando em 5s...');
        setTimeout(initWhatsApp, 5000);
      } else {
        console.log('🔐 Sessão expirada. Delete a pasta sessions/ e reinicie.');
      }
    }
  });

  sock.ev.on('creds.update', saveCreds);

  return sock;
}

export function getSocket() {
  return sock;
}

export function getConnectionStatus() {
  return isConnected;
}

export async function sendMessage(phoneNumber, message) {
  if (!sock || !isConnected) {
    throw new Error('WhatsApp não conectado');
  }

  const jid = phoneNumber.includes('@') ? phoneNumber : `${phoneNumber}@s.whatsapp.net`;
  
  try {
    await sock.sendMessage(jid, { text: message });
    return true;
  } catch (err) {
    console.error(`Erro ao enviar para ${phoneNumber}:`, err.message);
    return false;
  }
}

export async function sendOverdueNotification(phoneNumber, userName, bookTitle, daysOverdue) {
  const message = `📚 *Biblioteca - Aviso de Atraso*

Olá, ${userName}!

O livro *"${bookTitle}"* está com *${daysOverdue} dia(s) de atraso* para devolução.

Por favor, dirija-se à biblioteca para realizar a devolução o quanto antes.

Atenciosamente,
Equipe da Biblioteca`;

  return sendMessage(phoneNumber, message);
}

export async function sendReminderNotification(phoneNumber, userName, bookTitle, daysUntilDue) {
  const message = `📚 *Biblioteca - Lembrete de Devolução*

Olá, ${userName}!

Faltam *${daysUntilDue} dia(s)* para a devolução do livro *"${bookTitle}"*.

Data prevista: ${new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })}.

Lembre-se de devolver ou renovar na biblioteca.

Atenciosamente,
Equipe da Biblioteca`;

  return sendMessage(phoneNumber, message);
}