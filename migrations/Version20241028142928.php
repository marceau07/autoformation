<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028142928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_quiz (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quiz_row_id INT NOT NULL, answer JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_DE93B65BA76ED395 (user_id), INDEX IDX_DE93B65B6EE699AB (quiz_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65B6EE699AB FOREIGN KEY (quiz_row_id) REFERENCES quiz_row (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_quiz DROP FOREIGN KEY FK_DE93B65BA76ED395');
        $this->addSql('ALTER TABLE user_quiz DROP FOREIGN KEY FK_DE93B65B6EE699AB');
        $this->addSql('DROP TABLE user_quiz');
    }
}
