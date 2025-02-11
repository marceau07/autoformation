<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210100339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sandbox (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, author_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, data LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', uuid LONGTEXT NOT NULL, slide INT NOT NULL, INDEX IDX_E6EAF167591CC992 (course_id), INDEX IDX_E6EAF167F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sandbox ADD CONSTRAINT FK_E6EAF167591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE sandbox ADD CONSTRAINT FK_E6EAF167F675F31B FOREIGN KEY (author_id) REFERENCES trainer (id)');
        $this->addSql('ALTER TABLE canvas DROP FOREIGN KEY FK_A59F6C18591CC992');
        $this->addSql('ALTER TABLE canvas DROP FOREIGN KEY FK_A59F6C18F675F31B');
        $this->addSql('DROP TABLE canvas');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canvas (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, author_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, data LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', uuid LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slide INT NOT NULL, INDEX IDX_A59F6C18F675F31B (author_id), INDEX IDX_A59F6C18591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE canvas ADD CONSTRAINT FK_A59F6C18591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE canvas ADD CONSTRAINT FK_A59F6C18F675F31B FOREIGN KEY (author_id) REFERENCES trainer (id)');
        $this->addSql('ALTER TABLE sandbox DROP FOREIGN KEY FK_E6EAF167591CC992');
        $this->addSql('ALTER TABLE sandbox DROP FOREIGN KEY FK_E6EAF167F675F31B');
        $this->addSql('DROP TABLE sandbox');
    }
}
