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

-- Table commandes
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    details TEXT NOT NULL,
    paiement VARCHAR(50) NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'en_attente',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id)
);

-- Table factures
CREATE TABLE factures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_facturation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
);


CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    materiel_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('en_attente', 'active', 'terminee') DEFAULT 'en_attente',
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (materiel_id) REFERENCES materiels(id)
);


CREATE TABLE contrats_location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    remise FLOAT DEFAULT 0,
    tarif_total FLOAT NOT NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);


CREATE TABLE retours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    date_retour DATETIME NOT NULL,
    etat_retour VARCHAR(255),
    FOREIGN KEY (location_id) REFERENCES locations(id)
);






