CREATE DATABASE IF NOT EXISTS mini_erp;
USE mini_erp;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL
);

CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    variacao VARCHAR(100),
    quantidade INT DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo ENUM('valor', 'porcentagem') NOT NULL DEFAULT 'valor',
    valor_desconto DECIMAL(10,2) NOT NULL,
    minimo DECIMAL(10,2) DEFAULT 0,
    teto_desconto DECIMAL(10,2) DEFAULT NULL,
    validade DATE NOT NULL,
    usos_maximos INT NOT NULL,
    usos_utilizados INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produtos TEXT,
    subtotal DECIMAL(10,2),
    frete DECIMAL(10,2),
    total DECIMAL(10,2),
    cep VARCHAR(9),
    endereco TEXT,
    email_cliente VARCHAR(255),
    status ENUM('pendente', 'finalizado', 'cancelado') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
