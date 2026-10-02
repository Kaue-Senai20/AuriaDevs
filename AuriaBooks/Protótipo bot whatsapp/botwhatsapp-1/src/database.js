import pg from 'pg';
import dotenv from 'dotenv';

dotenv.config();

const { Pool } = pg;

const pool = new Pool({
  host: process.env.DB_HOST,
  port: parseInt(process.env.DB_PORT) || 5432,
  database: process.env.DB_NAME,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  max: 10,
  idleTimeoutMillis: 30000,
  connectionTimeoutMillis: 5000,
});

pool.on('error', (err) => {
  console.error('Erro inesperado no pool de conexões:', err);
});

export async function getOverdueLoans() {
  const query = `
    SELECT 
      e.id as emprestimo_id,
      u.nome as usuario_nome,
      u.telefone as usuario_telefone,
      l.titulo as livro_titulo,
      e.data_devolucao_prevista,
      CURRENT_DATE - e.data_devolucao_prevista as dias_atraso
    FROM emprestimos e
    JOIN usuarios u ON e.usuario_id = u.id
    JOIN livros l ON e.livro_id = l.id
    WHERE e.devolvido = false
    AND e.data_devolucao_prevista < CURRENT_DATE
    AND u.telefone IS NOT NULL
    AND u.telefone != ''
  `;
  const result = await pool.query(query);
  return result.rows;
}

export async function getDueSoonLoans(days = 1) {
  const query = `
    SELECT 
      e.id as emprestimo_id,
      u.nome as usuario_nome,
      u.telefone as usuario_telefone,
      l.titulo as livro_titulo,
      e.data_devolucao_prevista,
      e.data_devolucao_prevista - CURRENT_DATE as dias_restantes
    FROM emprestimos e
    JOIN usuarios u ON e.usuario_id = u.id
    JOIN livros l ON e.livro_id = l.id
    WHERE e.devolvido = false
    AND e.data_devolucao_prevista = CURRENT_DATE + $1
    AND u.telefone IS NOT NULL
    AND u.telefone != ''
  `;
  const result = await pool.query(query, [days]);
  return result.rows;
}

export async function markNotificationSent(emprestimoId, tipo) {
  const query = `
    INSERT INTO notificacoes_enviadas (emprestimo_id, tipo, enviada_em)
    VALUES ($1, $2, NOW())
    ON CONFLICT (emprestimo_id, tipo) DO NOTHING
  `;
  await pool.query(query, [emprestimoId, tipo]);
}

export async function wasNotificationSent(emprestimoId, tipo) {
  const query = `
    SELECT 1 FROM notificacoes_enviadas 
    WHERE emprestimo_id = $1 AND tipo = $2
  `;
  const result = await pool.query(query, [emprestimoId, tipo]);
  return result.rows.length > 0;
}

export async function testConnection() {
  try {
    await pool.query('SELECT 1');
    console.log('✅ Conexão com banco de dados OK');
    return true;
  } catch (err) {
    console.error('❌ Erro ao conectar no banco:', err.message);
    return false;
  }
}

export default pool;