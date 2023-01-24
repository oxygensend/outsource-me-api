<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221205074221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX archived_popularity_idx ON job_offer');
        $this->addSql('CREATE INDEX user_archived_popularity_idx ON job_offer (user_id, archived, popularity_order)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX user_archived_popularity_idx ON job_offer');
        $this->addSql('CREATE INDEX archived_popularity_idx ON job_offer (archived, popularity_order)');
    }
}
