CREATE TABLE rodadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partida_id INT NOT NULL,
    numero_rodada INT NOT NULL,
    municipio_id INT NOT NULL,
    resposta_usuario VARCHAR(50),
    acertou BOOLEAN,
    FOREIGN KEY (partida_id) REFERENCES partidas(id) ON DELETE CASCADE,
    FOREIGN KEY (municipio_id) REFERENCES municipios(id)
);
