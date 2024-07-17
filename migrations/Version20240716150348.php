<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716150348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prospect (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, siren VARCHAR(9) NOT NULL, nic VARCHAR(5) NOT NULL, street VARCHAR(100) NOT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(50) NOT NULL, country VARCHAR(25) NOT NULL, email VARCHAR(100) NOT NULL, phone_number VARCHAR(10) NOT NULL, phone_number_bis VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trainee CHANGE diploma diploma SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE prospect');
        $this->addSql('ALTER TABLE trainee CHANGE diploma diploma SMALLINT DEFAULT NULL COMMENT \'NULL: Pas de diplôme; 0: Non diplômé·e ; -1: Diplômé·e partiellement (CCP1); -2: Diplômé·e partiellement (CCP2); -3: Diplômé·e partiellement (CCP3); -4: Ne s\'\'est pas présenté·e ; 1: Diplômé·e	\'');
    }
}
