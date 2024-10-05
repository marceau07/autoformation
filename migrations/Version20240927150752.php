<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927150752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD original_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3ECD64BD FOREIGN KEY (original_message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F3ECD64BD ON message (original_message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F3ECD64BD');
        $this->addSql('DROP INDEX IDX_B6BD307F3ECD64BD ON message');
        $this->addSql('ALTER TABLE message DROP original_message_id');
    }
}
