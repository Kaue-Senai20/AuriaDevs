import fs from 'fs';
import { getAtrasados, getLembretes } from './api.js';
import { sendOverdueNotification, sendReminderNotification, getConnectionStatus } from './whatsapp.js';

// Regras do Auria Books:
// - Lembrete: 2 dias antes do vencimento (a API já filtra).
// - Aviso: 1º dia de atraso e depois a cada 4 dias (dias 1, 5, 9...), até devolver.
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
      const dias = l.dias_atraso;
      if (dias < 1) continue;
      if ((dias - 1) % 4 !== 0) {
        console.log(`⏳ ${l.usuario}: dia ${dias}, próximo aviso no ciclo de 4 dias`);
        continue;
      }
      const ciclo = Math.floor((dias - 1) / 4);
      const chave = `${l.emprestimo_id}:atraso:c${ciclo}`;
      if (lidas().has(chave)) continue;
      const fone = formatPhone(l.telefone);
      if (!fone) {
        console.log(`⚠️ Telefone inválido: ${l.usuario}`);
        continue;
      }
      await sendOverdueNotification(fone, l.usuario, l.livro, dias);
      marcar(chave);
      console.log(`✅ Aviso dia ${dias}: ${l.usuario} - ${l.livro}`);
    }
  } catch (e) {
    console.error('❌ Erro nos atrasados:', e.message);
  }
}

export async function processReminderNotifications() {
  console.log('🔍 Buscando lembretes (2 dias) na API...');
  if (!getConnectionStatus()) {
    console.log('⚠️ WhatsApp não conectado.');
    return;
  }
  try {
    const lista = await getLembretes();
    console.log(`📋 ${lista.length} vencendo em 2 dias`);
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
