<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017132123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_row ADD option1 VARCHAR(75) DEFAULT NULL, ADD option2 VARCHAR(75) DEFAULT NULL, ADD option3 VARCHAR(75) DEFAULT NULL, ADD option4 VARCHAR(75) DEFAULT NULL, ADD answer VARCHAR(255) DEFAULT NULL, DROP answer1, DROP answer2, DROP answer3, DROP answer4, DROP answer_short_text, DROP answer_long_text');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_row ADD answer1 VARCHAR(75) DEFAULT NULL, ADD answer2 VARCHAR(75) DEFAULT NULL, ADD answer3 VARCHAR(75) DEFAULT NULL, ADD answer4 VARCHAR(75) DEFAULT NULL, ADD answer_short_text VARCHAR(50) DEFAULT NULL, ADD answer_long_text LONGTEXT DEFAULT NULL, DROP option1, DROP option2, DROP option3, DROP option4, DROP answer');
    }
}
