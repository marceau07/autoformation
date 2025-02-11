<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202132450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trainee_internship ADD cohort_internship_id INT NOT NULL');
        $this->addSql('ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C54932D148CD FOREIGN KEY (cohort_internship_id) REFERENCES cohort_internship (id)');
        $this->addSql('CREATE INDEX IDX_4A40C54932D148CD ON trainee_internship (cohort_internship_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C54932D148CD');
        $this->addSql('DROP INDEX IDX_4A40C54932D148CD ON trainee_internship');
        $this->addSql('ALTER TABLE trainee_internship DROP cohort_internship_id');
    }
}
