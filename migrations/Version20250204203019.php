<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204203019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canvas ADD course_id INT NOT NULL, ADD slide INT NOT NULL');
        $this->addSql('ALTER TABLE canvas ADD CONSTRAINT FK_A59F6C18591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_A59F6C18591CC992 ON canvas (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canvas DROP FOREIGN KEY FK_A59F6C18591CC992');
        $this->addSql('DROP INDEX IDX_A59F6C18591CC992 ON canvas');
        $this->addSql('ALTER TABLE canvas DROP course_id, DROP slide');
    }
}
