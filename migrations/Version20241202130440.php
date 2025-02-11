<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202130440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trainee_internship (id INT AUTO_INCREMENT NOT NULL, internship_id INT NOT NULL, trainee_id INT NOT NULL, agreement TINYINT(1) NOT NULL, agreement_link VARCHAR(255) DEFAULT NULL, certificate TINYINT(1) NOT NULL, certificate_link VARCHAR(255) DEFAULT NULL, evaluation TINYINT(1) NOT NULL, evaluation_link VARCHAR(255) DEFAULT NULL, INDEX IDX_4A40C5497A4A70BE (internship_id), INDEX IDX_4A40C54936C682D0 (trainee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C5497A4A70BE FOREIGN KEY (internship_id) REFERENCES internship (id)');
        $this->addSql('ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C54936C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C5497A4A70BE');
        $this->addSql('ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C54936C682D0');
        $this->addSql('DROP TABLE trainee_internship');
    }
}
