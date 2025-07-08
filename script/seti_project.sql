-- Création de la base
CREATE DATABASE IF NOT EXISTS kjykmrds_seti CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE kjykmrds_seti;

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE, -- Email unique de l'admin
  password_hash VARCHAR(255) NOT NULL, -- Mot de passe hashé
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des planètes
CREATE TABLE IF NOT EXISTS planete (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL UNIQUE, -- Nom unique de la planète
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des messages liés à une planète
CREATE TABLE IF NOT EXISTS message (
  id INT AUTO_INCREMENT PRIMARY KEY,
  planete_id INT NOT NULL, -- Clé étrangère vers la planète
  contenu TEXT NOT NULL, -- Message envoyé depuis la planète
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (planete_id) REFERENCES planete(id) ON DELETE CASCADE
) ENGINE=InnoDB;
