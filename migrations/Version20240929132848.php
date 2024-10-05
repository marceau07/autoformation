<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240929132848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, theme_id INT DEFAULT NULL, deadline DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A412FA9259027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_row (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', question VARCHAR(255) NOT NULL, answer1 VARCHAR(75) DEFAULT NULL, answer2 VARCHAR(75) DEFAULT NULL, answer3 VARCHAR(75) DEFAULT NULL, answer4 VARCHAR(75) DEFAULT NULL, answer_short_text VARCHAR(50) DEFAULT NULL, answer_long_text LONGTEXT DEFAULT NULL, quiz_type VARCHAR(255) NOT NULL, timer INT NOT NULL, score INT NOT NULL, hint VARCHAR(255) DEFAULT NULL, answer_explanation LONGTEXT DEFAULT NULL, INDEX IDX_CF0E272A853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_theme (id INT AUTO_INCREMENT NOT NULL, illustration VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9259027487 FOREIGN KEY (theme_id) REFERENCES quiz_theme (id)');
        $this->addSql('ALTER TABLE quiz_row ADD CONSTRAINT FK_CF0E272A853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9259027487');
        $this->addSql('ALTER TABLE quiz_row DROP FOREIGN KEY FK_CF0E272A853CD175');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_row');
        $this->addSql('DROP TABLE quiz_theme');
    }
}
