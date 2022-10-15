<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221015111815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE education CHANGE field_of_study field_of_study VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE job_position ADD company_id INT DEFAULT NULL, DROP company_name');
        $this->addSql('ALTER TABLE job_position ADD CONSTRAINT FK_216B418E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_216B418E979B1AD6 ON job_position (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_position DROP FOREIGN KEY FK_216B418E979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE education CHANGE field_of_study field_of_study VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_216B418E979B1AD6 ON job_position');
        $this->addSql('ALTER TABLE job_position ADD company_name VARCHAR(255) NOT NULL, DROP company_id');
    }
}
