-- role table 
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL
);

-- create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(250) UNIQUE NOT NULL,
    password VARCHAR(250) NOT NULL,
    role_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
)

-- create material etat table
CREATE TABLE etat_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

-- create material type table
CREATE TABLE types_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

-- create material table
CREATE TABLE materiels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    marque VARCHAR(100),
    modele VARCHAR(100),
    type_id INT NOT NULL,
    etat_id INT NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    caracteristiques TEXT,
    FOREIGN KEY (type_id) REFERENCES types_materiel(id),
    FOREIGN KEY (etat_id) REFERENCES etat_materiel(id)
);
