-- Schema do banco de dados para o bot da biblioteca
-- Execute este script no PostgreSQL

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(255),
    criado_em TIMESTAMP DEFAULT NOW()
);

-- Tabela de livros
CREATE TABLE IF NOT EXISTS livros (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255),
    isbn VARCHAR(20),
    criado_em TIMESTAMP DEFAULT NOW()
);

-- Tabela de empréstimos
CREATE TABLE IF NOT EXISTS emprestimos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id),
    livro_id INTEGER REFERENCES livros(id),
    data_emprestimo DATE DEFAULT CURRENT_DATE,
    data_devolucao_prevista DATE NOT NULL,
    data_devolucao_real DATE,
    devolvido BOOLEAN DEFAULT FALSE,
    renovacoes INTEGER DEFAULT 0,
    criado_em TIMESTAMP DEFAULT NOW()
);

-- Tabela para controle de notificações enviadas (evita duplicatas)
CREATE TABLE IF NOT EXISTS notificacoes_enviadas (
    id SERIAL PRIMARY KEY,
    emprestimo_id INTEGER REFERENCES emprestimos(id),
    tipo VARCHAR(50) NOT NULL, -- 'atraso', 'lembrete_1d', 'lembrete_3d', etc
    enviada_em TIMESTAMP DEFAULT NOW(),
    UNIQUE(emprestimo_id, tipo)
);

-- Índices para performance
CREATE INDEX IF NOT EXISTS idx_emprestimos_devolvido_prevista 
ON emprestimos(devolvido, data_devolucao_prevista);

CREATE INDEX IF NOT EXISTS idx_usuarios_telefone 
ON usuarios(telefone) WHERE telefone IS NOT NULL AND telefone != '';

-- Dados de exemplo para teste
INSERT INTO usuarios (nome, telefone, email) VALUES
    ('João Silva', '5511999999999', 'joao@email.com'),
    ('Maria Santos', '5511888888888', 'maria@email.com'),
    ('Pedro Oliveira', '5511777777777', 'pedro@email.com')
ON CONFLICT DO NOTHING;

INSERT INTO livros (titulo, autor, isbn) VALUES
    ('Dom Casmurro', 'Machado de Assis', '9788535910663'),
    ('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', '9788579801234'),
    ('1984', 'George Orwell', '9788535914849')
ON CONFLICT DO NOTHING;

-- Empréstimo atrasado (para teste)
INSERT INTO emprestimos (usuario_id, livro_id, data_emprestimo, data_devolucao_prevista, devolvido) VALUES
    (1, 1, CURRENT_DATE - INTERVAL '20 days', CURRENT_DATE - INTERVAL '5 days', FALSE),
    (2, 2, CURRENT_DATE - INTERVAL '10 days', CURRENT_DATE + INTERVAL '1 day', FALSE),
    (3, 3, CURRENT_DATE - INTERVAL '5 days', CURRENT_DATE + INTERVAL '3 days', FALSE)
ON CONFLICT DO NOTHING;