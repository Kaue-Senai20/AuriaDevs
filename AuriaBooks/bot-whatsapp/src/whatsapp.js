import makeWASocket, { useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import qrcode from 'qrcode-terminal';
import dotenv from 'dotenv';
dotenv.config();

const SESSION_NAME = process.env.SESSION_NAME || 'auria-bot';

let sock = null;
let isConnected = false;

export async function initWhatsApp() {
  const { state, saveCreds } = await useMultiFileAuthState(`./sessions/${SESSION_NAME}`);
  const { version } = await fetchLatestBaileysVersion();

  sock = makeWASocket({
    version,
    auth: state,
    printQRInTerminal: false,
    browser: ['Auria Bot', 'Chrome', '1.0.0'],
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
      const expulso = lastDisconnect?.error?.output?.statusCode === DisconnectReason.loggedOut;
      console.log('❌ Conexão fechada.');
      if (!expulso) {
        console.log('🔄 Reconectando em 5s...');
        setTimeout(initWhatsApp, 5000);
      } else {
        console.log('🔐 Sessão expirada. Apague a pasta sessions/ e reinicie.');
      }
    }
  });

  sock.ev.on('creds.update', saveCreds);
  return sock;
}

export function getConnectionStatus() {
  return isConnected;
}

export async function sendMessage(phoneNumber, message) {
  if (!sock || !isConnected) throw new Error('WhatsApp não conectado');
  const jid = phoneNumber.includes('@') ? phoneNumber : `${phoneNumber}@s.whatsapp.net`;
  await sock.sendMessage(jid, { text: message });
  return true;
}

export async function sendOverdueNotification(phoneNumber, userName, bookTitle, daysOverdue) {
  const message = `📚 *Auria Books - Aviso de Atraso*\n\nOlá, ${userName}!\n\nO livro *"${bookTitle}"* está com *${daysOverdue} dia(s) de atraso* para devolução.\n\nPor favor, dirija-se à biblioteca o quanto antes.\n\nAtenciosamente,\nEquipe Auria Books`;
  return sendMessage(phoneNumber, message);
}

export async function sendReminderNotification(phoneNumber, userName, bookTitle) {
  const message = `📚 *Auria Books - Lembrete de Devolução*\n\nOlá, ${userName}!\n\nFalta *1 dia* para a devolução do livro *"${bookTitle}"*.\n\nLembre-se de devolver ou renovar na biblioteca.\n\nAtenciosamente,\nEquipe Auria Books`;
  return sendMessage(phoneNumber, message);
}
