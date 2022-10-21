<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020195050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_offer_technology (job_offer_id INT NOT NULL, technology_id INT NOT NULL, INDEX IDX_B52F711F3481D195 (job_offer_id), INDEX IDX_B52F711F4235D463 (technology_id), PRIMARY KEY(job_offer_id, technology_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_offer_technology ADD CONSTRAINT FK_B52F711F3481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_technology ADD CONSTRAINT FK_B52F711F4235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_offer_technology DROP FOREIGN KEY FK_B52F711F3481D195');
        $this->addSql('ALTER TABLE job_offer_technology DROP FOREIGN KEY FK_B52F711F4235D463');
        $this->addSql('DROP TABLE job_offer_technology');
    }
}
