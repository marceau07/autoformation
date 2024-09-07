<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716210350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE internship (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, prospect_id INT NOT NULL, INDEX IDX_10D1B00C36C682D0 (trainee_id), INDEX IDX_10D1B00CD182060A (prospect_id), UNIQUE INDEX unique_trainee_prospect (trainee_id, prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE internship ADD CONSTRAINT FK_10D1B00C36C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)');
        $this->addSql('ALTER TABLE internship ADD CONSTRAINT FK_10D1B00CD182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE internship DROP FOREIGN KEY FK_10D1B00C36C682D0');
        $this->addSql('ALTER TABLE internship DROP FOREIGN KEY FK_10D1B00CD182060A');
        $this->addSql('DROP TABLE internship');
    }
}
