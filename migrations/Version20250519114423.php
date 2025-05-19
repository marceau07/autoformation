<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519114423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, link VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, cohort_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', finish_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', description LONGTEXT DEFAULT NULL, event_type VARCHAR(255) NOT NULL, uuid LONGTEXT NOT NULL, INDEX IDX_6EA9A14635983C93 (cohort_id), INDEX IDX_6EA9A146A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cohort (id INT AUTO_INCREMENT NOT NULL, trainer_id INT NOT NULL, name VARCHAR(25) NOT NULL, acronym VARCHAR(50) NOT NULL, shield VARCHAR(255) DEFAULT NULL, documents LONGTEXT DEFAULT NULL, start_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', finish_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', uuid LONGTEXT NOT NULL, INDEX IDX_D3B8C16BFB08EDF6 (trainer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cohort_internship (id INT AUTO_INCREMENT NOT NULL, cohort_id INT NOT NULL, label VARCHAR(75) NOT NULL, start_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', finish_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', duration INT NOT NULL, uuid LONGTEXT NOT NULL, INDEX IDX_EC3C27ED35983C93 (cohort_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE coordinator (id INT NOT NULL, responsible_id INT DEFAULT NULL, role VARCHAR(50) NOT NULL, entrance_code VARCHAR(6) DEFAULT NULL, entrance_code_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_15FE0E6A602AD315 (responsible_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, trainer_id INT NOT NULL, title VARCHAR(50) NOT NULL, synopsis LONGTEXT NOT NULL, keywords LONGTEXT NOT NULL, link VARCHAR(255) NOT NULL, position INT NOT NULL, visitors INT NOT NULL, INDEX IDX_169E6FB9AFC2B591 (module_id), INDEX IDX_169E6FB9FB08EDF6 (trainer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course_cohort (id INT AUTO_INCREMENT NOT NULL, cohort_id INT NOT NULL, course_id INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_7D80002635983C93 (cohort_id), INDEX IDX_7D800026591CC992 (course_id), UNIQUE INDEX unique_course_cohort (course_id, cohort_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course_module (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, position INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', illustration VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course_resource (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, title VARCHAR(50) NOT NULL, resume LONGTEXT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, type VARCHAR(30) NOT NULL, record VARCHAR(100) DEFAULT NULL, record_link VARCHAR(255) DEFAULT NULL, INDEX IDX_15259940591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course_trainee (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, course_id INT NOT NULL, date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_4E05880B36C682D0 (trainee_id), INDEX IDX_4E05880B591CC992 (course_id), UNIQUE INDEX unique_course_trainee (course_id, trainee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE export_parameter (id INT AUTO_INCREMENT NOT NULL, dtype LONGTEXT NOT NULL, field LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, sector_id INT DEFAULT NULL, theme VARCHAR(25) NOT NULL, title VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, visibility TINYINT(1) NOT NULL, priority SMALLINT NOT NULL, INDEX IDX_E8FF75CCDE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, user_id INT NOT NULL, weight INT NOT NULL, annotation LONGTEXT NOT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_D229445812469DE2 (category_id), INDEX IDX_D2294458A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE feedback_category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE internship (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, prospect_id INT NOT NULL, tutor_last_name VARCHAR(75) NOT NULL, tutor_first_name VARCHAR(75) NOT NULL, tutor_email VARCHAR(255) NOT NULL, tutor_phone_number VARCHAR(10) NOT NULL, INDEX IDX_10D1B00C36C682D0 (trainee_id), INDEX IDX_10D1B00CD182060A (prospect_id), UNIQUE INDEX unique_trainee_prospect (trainee_id, prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, send_people_id INT DEFAULT NULL, people_id INT DEFAULT NULL, cohort_id INT DEFAULT NULL, original_message_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', readed TINYINT(1) NOT NULL, document VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(50) DEFAULT NULL, INDEX IDX_B6BD307FEA56DCE5 (send_people_id), INDEX IDX_B6BD307F3147C936 (people_id), INDEX IDX_B6BD307F35983C93 (cohort_id), INDEX IDX_B6BD307F3ECD64BD (original_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, origin VARCHAR(150) NOT NULL, message LONGTEXT NOT NULL, link VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', category VARCHAR(100) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE prospect (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, siren VARCHAR(9) NOT NULL, nic VARCHAR(5) NOT NULL, number INT DEFAULT NULL, street VARCHAR(100) NOT NULL, additional_address VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(5) NOT NULL, city VARCHAR(50) NOT NULL, country VARCHAR(25) NOT NULL, email VARCHAR(100) NOT NULL, phone_number VARCHAR(10) NOT NULL, phone_number_bis VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, theme_id INT DEFAULT NULL, trainer_id INT NOT NULL, module_id INT NOT NULL, title VARCHAR(255) NOT NULL, uuid LONGTEXT NOT NULL, INDEX IDX_A412FA9259027487 (theme_id), INDEX IDX_A412FA92FB08EDF6 (trainer_id), INDEX IDX_A412FA92AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz_row (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, uuid LONGTEXT NOT NULL, question VARCHAR(255) NOT NULL, option1 VARCHAR(75) DEFAULT NULL, option2 VARCHAR(75) DEFAULT NULL, option3 VARCHAR(75) DEFAULT NULL, option4 VARCHAR(75) DEFAULT NULL, quiz_type VARCHAR(255) NOT NULL, timer INT NOT NULL, score INT NOT NULL, hint VARCHAR(255) DEFAULT NULL, answer_explanation LONGTEXT DEFAULT NULL, answer VARCHAR(255) DEFAULT NULL, INDEX IDX_CF0E272A853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz_share (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, start_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', finish_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', uuid LONGTEXT NOT NULL, INDEX IDX_1375E4DA853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz_theme (id INT AUTO_INCREMENT NOT NULL, illustration VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE responsible (id INT NOT NULL, sector_id INT DEFAULT NULL, role VARCHAR(50) NOT NULL, entrance_code VARCHAR(6) DEFAULT NULL, entrance_code_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_97E625E8DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sandbox (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, author_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, data LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', uuid LONGTEXT NOT NULL, slide INT NOT NULL, INDEX IDX_E6EAF167591CC992 (course_id), INDEX IDX_E6EAF167F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, logo VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site_settings (id INT AUTO_INCREMENT NOT NULL, maintenance_mode TINYINT(1) NOT NULL, logo_path VARCHAR(255) NOT NULL, logo_name VARCHAR(75) NOT NULL, platform_name VARCHAR(75) NOT NULL, primary_color VARCHAR(7) NOT NULL, secondary_color VARCHAR(7) NOT NULL, tertiary_color VARCHAR(7) NOT NULL, quaternary_color VARCHAR(7) NOT NULL, lighten_color VARCHAR(7) NOT NULL, darken_color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(500) NOT NULL, resume VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE survey_trainee (id INT AUTO_INCREMENT NOT NULL, survey_id INT DEFAULT NULL, trainee_id INT NOT NULL, rate NUMERIC(5, 2) DEFAULT NULL, answer LONGTEXT DEFAULT NULL, INDEX IDX_2EAAE5CB3FE509D (survey_id), INDEX IDX_2EAAE5C36C682D0 (trainee_id), UNIQUE INDEX unique_survey_trainee (survey_id, trainee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainee (id INT NOT NULL, cohort_id INT NOT NULL, password_save VARCHAR(60) NOT NULL, documents LONGTEXT DEFAULT NULL, diploma SMALLINT DEFAULT NULL, tutorial_completed VARCHAR(255) NOT NULL, INDEX IDX_46C68DE735983C93 (cohort_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainee_course_favorite (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_B106030136C682D0 (trainee_id), INDEX IDX_B1060301591CC992 (course_id), UNIQUE INDEX unique_trainee_course (trainee_id, course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainee_internship (id INT AUTO_INCREMENT NOT NULL, internship_id INT DEFAULT NULL, trainee_id INT NOT NULL, cohort_internship_id INT NOT NULL, agreement TINYINT(1) DEFAULT NULL, agreement_link VARCHAR(255) DEFAULT NULL, certificate TINYINT(1) DEFAULT NULL, certificate_link VARCHAR(255) DEFAULT NULL, evaluation TINYINT(1) DEFAULT NULL, evaluation_link VARCHAR(255) DEFAULT NULL, INDEX IDX_4A40C5497A4A70BE (internship_id), INDEX IDX_4A40C54936C682D0 (trainee_id), INDEX IDX_4A40C54932D148CD (cohort_internship_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainee_resource (id INT AUTO_INCREMENT NOT NULL, trainee_id INT NOT NULL, course_resource_id INT NOT NULL, label VARCHAR(255) NOT NULL, INDEX IDX_72F48E9F36C682D0 (trainee_id), INDEX IDX_72F48E9FFD5CADF1 (course_resource_id), UNIQUE INDEX unique_trainee_resource (trainee_id, course_resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trainer (id INT NOT NULL, coordinator_id INT DEFAULT NULL, role VARCHAR(50) NOT NULL, entrance_code VARCHAR(6) DEFAULT NULL, entrance_code_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_C5150820E7877946 (coordinator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, avatar_id INT NOT NULL, username VARCHAR(50) NOT NULL, last_name VARCHAR(75) NOT NULL, first_name VARCHAR(75) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, activated TINYINT(1) NOT NULL, tmp_code VARCHAR(6) DEFAULT NULL, tmp_code_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', signature LONGTEXT DEFAULT NULL, uuid LONGTEXT NOT NULL, phone_number VARCHAR(10) DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_8D93D64986383B10 (avatar_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_quiz (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quiz_row_id INT NOT NULL, answer LONGTEXT NOT NULL, finish_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_DE93B65BA76ED395 (user_id), INDEX IDX_DE93B65B6EE699AB (quiz_row_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A14635983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cohort ADD CONSTRAINT FK_D3B8C16BFB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cohort_internship ADD CONSTRAINT FK_EC3C27ED35983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coordinator ADD CONSTRAINT FK_15FE0E6A602AD315 FOREIGN KEY (responsible_id) REFERENCES responsible (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coordinator ADD CONSTRAINT FK_15FE0E6ABF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course ADD CONSTRAINT FK_169E6FB9AFC2B591 FOREIGN KEY (module_id) REFERENCES course_module (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course ADD CONSTRAINT FK_169E6FB9FB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_cohort ADD CONSTRAINT FK_7D80002635983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_cohort ADD CONSTRAINT FK_7D800026591CC992 FOREIGN KEY (course_id) REFERENCES course (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_resource ADD CONSTRAINT FK_15259940591CC992 FOREIGN KEY (course_id) REFERENCES course (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_trainee ADD CONSTRAINT FK_4E05880B36C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_trainee ADD CONSTRAINT FK_4E05880B591CC992 FOREIGN KEY (course_id) REFERENCES course (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CCDE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D229445812469DE2 FOREIGN KEY (category_id) REFERENCES feedback_category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D2294458A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internship ADD CONSTRAINT FK_10D1B00C36C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internship ADD CONSTRAINT FK_10D1B00CD182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FEA56DCE5 FOREIGN KEY (send_people_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3147C936 FOREIGN KEY (people_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F35983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3ECD64BD FOREIGN KEY (original_message_id) REFERENCES message (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9259027487 FOREIGN KEY (theme_id) REFERENCES quiz_theme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92FB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92AFC2B591 FOREIGN KEY (module_id) REFERENCES course_module (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_row ADD CONSTRAINT FK_CF0E272A853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_share ADD CONSTRAINT FK_1375E4DA853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsible ADD CONSTRAINT FK_97E625E8DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsible ADD CONSTRAINT FK_97E625E8BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sandbox ADD CONSTRAINT FK_E6EAF167591CC992 FOREIGN KEY (course_id) REFERENCES course (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sandbox ADD CONSTRAINT FK_E6EAF167F675F31B FOREIGN KEY (author_id) REFERENCES trainer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_trainee ADD CONSTRAINT FK_2EAAE5CB3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_trainee ADD CONSTRAINT FK_2EAAE5C36C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee ADD CONSTRAINT FK_46C68DE735983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee ADD CONSTRAINT FK_46C68DE7BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_course_favorite ADD CONSTRAINT FK_B106030136C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_course_favorite ADD CONSTRAINT FK_B1060301591CC992 FOREIGN KEY (course_id) REFERENCES course (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C5497A4A70BE FOREIGN KEY (internship_id) REFERENCES internship (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C54936C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship ADD CONSTRAINT FK_4A40C54932D148CD FOREIGN KEY (cohort_internship_id) REFERENCES cohort_internship (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_resource ADD CONSTRAINT FK_72F48E9F36C682D0 FOREIGN KEY (trainee_id) REFERENCES trainee (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_resource ADD CONSTRAINT FK_72F48E9FFD5CADF1 FOREIGN KEY (course_resource_id) REFERENCES course_resource (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainer ADD CONSTRAINT FK_C5150820E7877946 FOREIGN KEY (coordinator_id) REFERENCES coordinator (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainer ADD CONSTRAINT FK_C5150820BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65B6EE699AB FOREIGN KEY (quiz_row_id) REFERENCES quiz_row (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A14635983C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cohort DROP FOREIGN KEY FK_D3B8C16BFB08EDF6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cohort_internship DROP FOREIGN KEY FK_EC3C27ED35983C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coordinator DROP FOREIGN KEY FK_15FE0E6A602AD315
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coordinator DROP FOREIGN KEY FK_15FE0E6ABF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9FB08EDF6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_cohort DROP FOREIGN KEY FK_7D80002635983C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_cohort DROP FOREIGN KEY FK_7D800026591CC992
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_resource DROP FOREIGN KEY FK_15259940591CC992
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_trainee DROP FOREIGN KEY FK_4E05880B36C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE course_trainee DROP FOREIGN KEY FK_4E05880B591CC992
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CCDE95C867
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D229445812469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internship DROP FOREIGN KEY FK_10D1B00C36C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE internship DROP FOREIGN KEY FK_10D1B00CD182060A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FEA56DCE5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F3147C936
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F35983C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F3ECD64BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9259027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92FB08EDF6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_row DROP FOREIGN KEY FK_CF0E272A853CD175
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_share DROP FOREIGN KEY FK_1375E4DA853CD175
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsible DROP FOREIGN KEY FK_97E625E8DE95C867
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsible DROP FOREIGN KEY FK_97E625E8BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sandbox DROP FOREIGN KEY FK_E6EAF167591CC992
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sandbox DROP FOREIGN KEY FK_E6EAF167F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_trainee DROP FOREIGN KEY FK_2EAAE5CB3FE509D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE survey_trainee DROP FOREIGN KEY FK_2EAAE5C36C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee DROP FOREIGN KEY FK_46C68DE735983C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee DROP FOREIGN KEY FK_46C68DE7BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_course_favorite DROP FOREIGN KEY FK_B106030136C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_course_favorite DROP FOREIGN KEY FK_B1060301591CC992
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C5497A4A70BE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C54936C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_internship DROP FOREIGN KEY FK_4A40C54932D148CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_resource DROP FOREIGN KEY FK_72F48E9F36C682D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainee_resource DROP FOREIGN KEY FK_72F48E9FFD5CADF1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainer DROP FOREIGN KEY FK_C5150820E7877946
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trainer DROP FOREIGN KEY FK_C5150820BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_quiz DROP FOREIGN KEY FK_DE93B65BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_quiz DROP FOREIGN KEY FK_DE93B65B6EE699AB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE avatar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cohort
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cohort_internship
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE coordinator
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course_cohort
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course_module
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course_resource
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course_trainee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE export_parameter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE faq
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE feedback
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE feedback_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE internship
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prospect
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz_row
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz_share
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz_theme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE responsible
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sandbox
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sector
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_settings
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE survey_trainee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainee_course_favorite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainee_internship
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainee_resource
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trainer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_quiz
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
