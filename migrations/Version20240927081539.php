<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927081539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trainee_course_favorite (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_B106030136C682D0 (trainee_id), INDEX IDX_B1060301591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trainee_course_favorite ADD CONSTRAINT FK_B106030136C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)');
        $this->addSql('ALTER TABLE trainee_course_favorite ADD CONSTRAINT FK_B1060301591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trainee_course_favorite DROP FOREIGN KEY FK_B106030136C682D0');
        $this->addSql('ALTER TABLE trainee_course_favorite DROP FOREIGN KEY FK_B1060301591CC992');
        $this->addSql('DROP TABLE trainee_course_favorite');
    }
}
