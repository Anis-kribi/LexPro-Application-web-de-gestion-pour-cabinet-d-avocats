<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260406111311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_facture (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, quantite NUMERIC(8, 2) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, facture_id INT NOT NULL, INDEX IDX_135420A77F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, nom_entreprise VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(100) DEFAULT NULL, telephone VARCHAR(30) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, adresse VARCHAR(500) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, remarques LONGTEXT DEFAULT NULL, statuts VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, chemin_fichier VARCHAR(500) NOT NULL, nom_original VARCHAR(255) DEFAULT NULL, type VARCHAR(100) NOT NULL, confidentialite VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, dossier_id INT NOT NULL, telechargepar_id INT DEFAULT NULL, INDEX IDX_D8698A76611C0C56 (dossier_id), INDEX IDX_D8698A7682EC989B (telechargepar_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dossier (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, numero_reference VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, type_cas VARCHAR(100) DEFAULT NULL, statuts VARCHAR(50) NOT NULL, priorite VARCHAR(50) NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, nom_tribunal VARCHAR(255) DEFAULT NULL, nom_adversaire VARCHAR(255) DEFAULT NULL, client_id INT NOT NULL, avocat_id INT DEFAULT NULL, INDEX IDX_3D48E03719EB6921 (client_id), INDEX IDX_3D48E037EDBF2DB2 (avocat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE entree_de_temps (id INT AUTO_INCREMENT NOT NULL, heures NUMERIC(5, 2) NOT NULL, date DATE NOT NULL, description LONGTEXT DEFAULT NULL, facturable TINYINT NOT NULL, user_id INT NOT NULL, dossier_id INT NOT NULL, INDEX IDX_3ED767AAA76ED395 (user_id), INDEX IDX_3ED767AA611C0C56 (dossier_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE factures (id INT AUTO_INCREMENT NOT NULL, numero_facture VARCHAR(100) NOT NULL, montant_ht NUMERIC(10, 2) NOT NULL, tva NUMERIC(5, 2) NOT NULL, montant_ttc NUMERIC(10, 2) NOT NULL, status VARCHAR(50) NOT NULL, date_emission DATE NOT NULL, date_echeance DATE DEFAULT NULL, client_id INT NOT NULL, dossier_id INT DEFAULT NULL, INDEX IDX_647590B19EB6921 (client_id), INDEX IDX_647590B611C0C56 (dossier_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, client_id INT NOT NULL, INDEX IDX_65E8AA0A19EB6921 (client_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_echeance DATE DEFAULT NULL, status VARCHAR(50) NOT NULL, priorite VARCHAR(50) NOT NULL, dossier_id INT NOT NULL, assigne_a_id INT DEFAULT NULL, INDEX IDX_93872075611C0C56 (dossier_id), INDEX IDX_93872075BB1B0F33 (assigne_a_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(100) DEFAULT NULL, nom VARCHAR(100) DEFAULT NULL, telephone VARCHAR(30) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article_facture ADD CONSTRAINT FK_135420A77F2DEE08 FOREIGN KEY (facture_id) REFERENCES factures (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7682EC989B FOREIGN KEY (telechargepar_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E03719EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037EDBF2DB2 FOREIGN KEY (avocat_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entree_de_temps ADD CONSTRAINT FK_3ED767AAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entree_de_temps ADD CONSTRAINT FK_3ED767AA611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE factures ADD CONSTRAINT FK_647590B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE factures ADD CONSTRAINT FK_647590B611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075BB1B0F33 FOREIGN KEY (assigne_a_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_facture DROP FOREIGN KEY FK_135420A77F2DEE08');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76611C0C56');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7682EC989B');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E03719EB6921');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E037EDBF2DB2');
        $this->addSql('ALTER TABLE entree_de_temps DROP FOREIGN KEY FK_3ED767AAA76ED395');
        $this->addSql('ALTER TABLE entree_de_temps DROP FOREIGN KEY FK_3ED767AA611C0C56');
        $this->addSql('ALTER TABLE factures DROP FOREIGN KEY FK_647590B19EB6921');
        $this->addSql('ALTER TABLE factures DROP FOREIGN KEY FK_647590B611C0C56');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A19EB6921');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075611C0C56');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075BB1B0F33');
        $this->addSql('DROP TABLE article_facture');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE dossier');
        $this->addSql('DROP TABLE entree_de_temps');
        $this->addSql('DROP TABLE factures');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
