CREATE DATABASE IF NOT EXISTS catalogo_reserva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE catalogo_reserva;

CREATE TABLE IF NOT EXISTS itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM('Livro', 'Jogo') NOT NULL,
    autor VARCHAR(150) NOT NULL,
    ano INT NOT NULL,
    disponivel TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    nome_usuario VARCHAR(150) NOT NULL,
    data_reserva DATE NOT NULL,
    status ENUM('ativa', 'cancelada') NOT NULL DEFAULT 'ativa',
    CONSTRAINT fk_reservas_itens FOREIGN KEY(item_id) REFERENCES itens(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO
    itens(titulo, tipo, autor, ano, disponivel)
VALUES
    ('Dom Casmurro', 'Livro', 'Machado de Assis', 1899, 1),
('The Legend of Zelda', 'Jogo', 'Nintendo', 1986, 1),
('O Hobbit', 'Livro', 'J. R. R. Tolkien', 1937, 1);