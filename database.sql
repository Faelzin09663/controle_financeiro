CREATE DATABASE IF NOT EXISTS controle_gastos;
USE controle_gastos;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    tipo ENUM('receita', 'despesa') NOT NULL
);

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_movimentacao DATE NOT NULL,
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Insert default categories
INSERT INTO categorias (nome, tipo) VALUES 
('Salário', 'receita'),
('Freelance', 'receita'),
('Investimentos', 'receita'),
('Alimentação', 'despesa'),
('Transporte', 'despesa'),
('Moradia', 'despesa'),
('Lazer', 'despesa'),
('Saúde', 'despesa'),
('Educação', 'despesa'),
('Outros', 'despesa');
