<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129225918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_settings ADD primary_color VARCHAR(7) NOT NULL, ADD secondary_color VARCHAR(7) NOT NULL, ADD tertiary_color VARCHAR(7) NOT NULL, ADD quaternary_color VARCHAR(7) NOT NULL, ADD lighten_color VARCHAR(7) NOT NULL, ADD darken_color VARCHAR(7) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_settings DROP primary_color, DROP secondary_color, DROP tertiary_color, DROP quaternary_color, DROP lighten_color, DROP darken_color');
    }
}
