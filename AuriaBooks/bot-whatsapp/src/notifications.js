import fs from 'fs';
import { getAtrasados, getLembretes } from './api.js';
import { sendOverdueNotification, sendReminderNotification, getConnectionStatus } from './whatsapp.js';

const SENT_FILE = './enviadas.json';

function lidas() {
  try {
    return new Set(JSON.parse(fs.readFileSync(SENT_FILE, 'utf8')));
  } catch {
    return new Set();
  }
}

function marcar(chave) {
  const s = lidas();
  s.add(chave);
  fs.writeFileSync(SENT_FILE, JSON.stringify([...s]));
}

export function formatPhone(phone) {
  if (!phone) return null;
  let n = String(phone).replace(/\D/g, '');
  if (n.length === 10 || n.length === 11) n = '55' + n;
  return n.length >= 12 ? n : null;
}

export async function processOverdueNotifications() {
  console.log('🔍 Buscando atrasados na API...');
  if (!getConnectionStatus()) {
    console.log('⚠️ WhatsApp não conectado.');
    return;
  }
  try {
    const lista = await getAtrasados();
    console.log(`📋 ${lista.length} em atraso`);
    for (const l of lista) {
      const chave = `${l.emprestimo_id}:atraso`;
      if (lidas().has(chave)) continue;
      const fone = formatPhone(l.telefone);
      if (!fone) {
        console.log(`⚠️ Telefone inválido: ${l.usuario}`);
        continue;
      }
      await sendOverdueNotification(fone, l.usuario, l.livro, l.dias_atraso);
      marcar(chave);
      console.log(`✅ Notificado: ${l.usuario} - ${l.livro}`);
    }
  } catch (e) {
    console.error('❌ Erro nos atrasados:', e.message);
  }
}

export async function processReminderNotifications() {
  console.log('🔍 Buscando lembretes na API...');
  if (!getConnectionStatus()) {
    console.log('⚠️ WhatsApp não conectado.');
    return;
  }
  try {
    const lista = await getLembretes();
    console.log(`📋 ${lista.length} vencendo amanhã`);
    for (const l of lista) {
      const chave = `${l.emprestimo_id}:lembrete`;
      if (lidas().has(chave)) continue;
      const fone = formatPhone(l.telefone);
      if (!fone) {
        console.log(`⚠️ Telefone inválido: ${l.usuario}`);
        continue;
      }
      await sendReminderNotification(fone, l.usuario, l.livro);
      marcar(chave);
      console.log(`✅ Lembrete: ${l.usuario} - ${l.livro}`);
    }
  } catch (e) {
    console.error('❌ Erro nos lembretes:', e.message);
  }
}
