<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130215525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cohort_internship ADD cohort_id INT NOT NULL');
        $this->addSql('ALTER TABLE cohort_internship ADD CONSTRAINT FK_EC3C27ED35983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)');
        $this->addSql('CREATE INDEX IDX_EC3C27ED35983C93 ON cohort_internship (cohort_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cohort_internship DROP FOREIGN KEY FK_EC3C27ED35983C93');
        $this->addSql('DROP INDEX IDX_EC3C27ED35983C93 ON cohort_internship');
        $this->addSql('ALTER TABLE cohort_internship DROP cohort_id');
    }
}
