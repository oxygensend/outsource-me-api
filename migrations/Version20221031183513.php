<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031183513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE salary_range (id INT AUTO_INCREMENT NOT NULL, down_range DOUBLE PRECISION NOT NULL, up_range DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(3) NOT NULL, type VARCHAR(8) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_offer ADD salary_range_id INT DEFAULT NULL, ADD experience VARCHAR(20) DEFAULT NULL, ADD valid_to DATETIME DEFAULT NULL, DROP salary_range');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4EB8C75AB0 FOREIGN KEY (salary_range_id) REFERENCES salary_range (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_288A3A4EB8C75AB0 ON job_offer (salary_range_id)');
        $this->addSql('ALTER TABLE job_position ADD start_date DATETIME DEFAULT NULL, ADD end_date DATETIME DEFAULT NULL, DROP valid_from, DROP valid_to');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4EB8C75AB0');
        $this->addSql('DROP TABLE salary_range');
        $this->addSql('DROP INDEX UNIQ_288A3A4EB8C75AB0 ON job_offer');
        $this->addSql('ALTER TABLE job_offer ADD salary_range VARCHAR(255) DEFAULT NULL, DROP salary_range_id, DROP experience, DROP valid_to');
        $this->addSql('ALTER TABLE job_position ADD valid_from DATETIME DEFAULT NULL, ADD valid_to DATETIME DEFAULT NULL, DROP start_date, DROP end_date');
    }
}
