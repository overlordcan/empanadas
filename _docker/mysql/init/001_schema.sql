-- Schema + seed para empanadas
CREATE DATABASE IF NOT EXISTS empanadas_db;
USE empanadas_db;

CREATE TABLE IF NOT EXISTS empanadas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  type VARCHAR(255) NOT NULL,
  filling TEXT,
  price DECIMAL(10,2),
  is_sold_out BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO empanadas (name, type, filling, price, is_sold_out) VALUES
('Pino', 'Horno', 'Carne, cebolla, aceituna, huevo', 2500, 0),
('Queso', 'Frita', 'Queso', 2000, 0),
('Vegetariana', 'Horno', 'Verduras salteadas', 2300, 0),
('Camarón queso', 'Frita', 'Camarón y queso', 2800, 0);
