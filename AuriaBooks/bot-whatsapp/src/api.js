import dotenv from 'dotenv';
dotenv.config();

const API_URL = (process.env.API_URL || 'http://127.0.0.1:8001').replace(/\/$/, '');
const API_TOKEN = process.env.API_TOKEN || '';

function headers() {
  return { 'X-API-Token': API_TOKEN, 'Accept': 'application/json' };
}

async function get(path) {
  const r = await fetch(`${API_URL}/api${path}`, { headers: headers() });
  if (!r.ok) throw new Error(`API ${path} -> ${r.status}`);
  return r.json();
}

export const getAtrasados = () => get('/atrasados');
export const getLembretes = () => get('/lembretes');
