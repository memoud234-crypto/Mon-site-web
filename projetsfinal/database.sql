CREATE DATABASE IF NOT EXISTS restaurant_taulissa CHARACTER SET utf8 COLLATE utf8_general_ci;
USE restaurant_taulissa;

DROP TABLE IF EXISTS FACTURE;
DROP TABLE IF EXISTS DETAIL_COMMANDE;
DROP TABLE IF EXISTS RESERVATION;
DROP TABLE IF EXISTS COMMANDE;
DROP TABLE IF EXISTS PLAT;
DROP TABLE IF EXISTS TABLE_RESTAURANT;
DROP TABLE IF EXISTS EMPLOYE;
DROP TABLE IF EXISTS CLIENT;

CREATE TABLE CLIENT (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse VARCHAR(100)
);

CREATE TABLE EMPLOYE (
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    poste VARCHAR(50) NOT NULL,
    salaire DECIMAL(10,2) DEFAULT 0,
    email VARCHAR(100),
    mot_de_passe VARCHAR(100),
    role VARCHAR(20) DEFAULT 'employe'
);

CREATE TABLE TABLE_RESTAURANT (
    id_table INT AUTO_INCREMENT PRIMARY KEY,
    numero_table INT NOT NULL,
    capacite INT NOT NULL
);

CREATE TABLE PLAT (
    id_plat INT AUTO_INCREMENT PRIMARY KEY,
    nom_plat VARCHAR(80) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    categorie VARCHAR(50) DEFAULT 'Plat principal',
    stock INT NOT NULL DEFAULT 0
);

CREATE TABLE COMMANDE (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATE NOT NULL,
    heure_commande TIME NOT NULL,
    statut_commande VARCHAR(30) DEFAULT 'En attente',
    id_client INT NOT NULL,
    id_employe INT NULL,
    FOREIGN KEY (id_client) REFERENCES CLIENT(id_client),
    FOREIGN KEY (id_employe) REFERENCES EMPLOYE(id_employe)
);

CREATE TABLE DETAIL_COMMANDE (
    id_commande INT NOT NULL,
    id_plat INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_commande, id_plat),
    FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande) ON DELETE CASCADE,
    FOREIGN KEY (id_plat) REFERENCES PLAT(id_plat)
);

CREATE TABLE FACTURE (
    id_facture INT AUTO_INCREMENT PRIMARY KEY,
    date_facture DATE NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,
    mode_paiement VARCHAR(30) NOT NULL DEFAULT 'Espèces',
    id_commande INT UNIQUE NOT NULL,
    FOREIGN KEY (id_commande) REFERENCES COMMANDE(id_commande)
);

CREATE TABLE RESERVATION (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date_reservation DATE NOT NULL,
    heure TIME NOT NULL,
    nombre_personne INT NOT NULL,
    id_client INT NOT NULL,
    id_table INT NOT NULL,
    id_employe INT NULL,
    FOREIGN KEY (id_client) REFERENCES CLIENT(id_client),
    FOREIGN KEY (id_table) REFERENCES TABLE_RESTAURANT(id_table),
    FOREIGN KEY (id_employe) REFERENCES EMPLOYE(id_employe)
);

INSERT INTO TABLE_RESTAURANT (numero_table, capacite) VALUES
(1,2),(2,2),(3,4),(4,4),(5,6),(6,6),(7,8),(8,10);

INSERT INTO EMPLOYE (nom, prenom, poste, salaire, email, mot_de_passe, role) VALUES
('Ahmed','Farah','Chef cuisinier',90000,'ahmed@taulissa.com','ahmed123','employe'),
('Hassan','Ali','Serveur',55000,'hassan@taulissa.com','hassan123','employe'),
('Mariam','Omar','Serveuse',55000,'mariam@taulissa.com','mariam123','employe'),
('abdo','MOhamed','Gerant',110000,'admin@taulissa.com','admin123','admin');

INSERT INTO PLAT (nom_plat, prix, categorie, stock) VALUES
('Saint-Jacques au Yuzu',3500,'Entrées',10),
('Steak Premium',5800,'Viandes',10),
('Burger Taulissa',2200,'Burgers',12),
('Lasagne Maison',2800,'Pates',10),
('Pizza Royale',2400,'Pizzas',15),
('Jus de Fruits Frais',800,'Boissons',25),
('Hosomaki Sushi',3200,'Sushis',8),
('Poulet Grille',2600,'Viandes',12),
('Crevettes a l Ail',3000,'Fruits de mer',9),
('Fondant au Chocolat',1500,'Desserts',20);
