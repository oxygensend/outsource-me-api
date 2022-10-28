<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028150524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_position ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD active_job_position_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BED03397 FOREIGN KEY (active_job_position_id) REFERENCES job_position (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649BED03397 ON user (active_job_position_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_position DROP active');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649BED03397');
        $this->addSql('DROP INDEX UNIQ_8D93D649BED03397 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP active_job_position_id');
    }
}
