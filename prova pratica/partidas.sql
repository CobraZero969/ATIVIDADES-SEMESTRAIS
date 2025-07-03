CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_jogador VARCHAR(100) NOT NULL,
    pontuacao_final INT DEFAULT 0,
    status ENUM('incompleta', 'completa') DEFAULT 'incompleta',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);
