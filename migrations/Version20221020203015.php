<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020203015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE technology DROP FOREIGN KEY FK_F463524D8F8A14FA');
        $this->addSql('DROP INDEX IDX_F463524D8F8A14FA ON technology');
        $this->addSql('ALTER TABLE technology DROP technologies_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE technology ADD technologies_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE technology ADD CONSTRAINT FK_F463524D8F8A14FA FOREIGN KEY (technologies_id) REFERENCES job_offer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F463524D8F8A14FA ON technology (technologies_id)');
    }
}
