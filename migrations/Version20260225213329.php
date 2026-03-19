<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225213329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, statut TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE compte_seance (compte_id INT NOT NULL, seance_id INT NOT NULL, INDEX IDX_5E4A439F2C56620 (compte_id), INDEX IDX_5E4A439E3797A94 (seance_id), PRIMARY KEY (compte_id, seance_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE film (id INT AUTO_INCREMENT NOT NULL, affiche VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, genre VARCHAR(255) NOT NULL, est_dispo TINYINT NOT NULL, date_sortie DATE NOT NULL, duree INT NOT NULL, note DOUBLE PRECISION DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, nb_places INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, date_diffusion DATE NOT NULL, nb_place_reservees INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, film_id INT NOT NULL, salle_id INT NOT NULL, INDEX IDX_DF7DFD0E567F5183 (film_id), INDEX IDX_DF7DFD0EDC304035 (salle_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE compte_seance ADD CONSTRAINT FK_5E4A439F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compte_seance ADD CONSTRAINT FK_5E4A439E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E567F5183 FOREIGN KEY (film_id) REFERENCES film (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0EDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_seance DROP FOREIGN KEY FK_5E4A439F2C56620');
        $this->addSql('ALTER TABLE compte_seance DROP FOREIGN KEY FK_5E4A439E3797A94');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E567F5183');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0EDC304035');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE compte_seance');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
