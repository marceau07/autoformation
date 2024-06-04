<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604100136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX unique_course_trainee ON course_trainee (course_id, trainee_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_survey_trainee ON survey_trainee (survey_id, trainee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_survey_trainee ON survey_trainee');
        $this->addSql('DROP INDEX unique_course_trainee ON course_trainee');
    }
}
