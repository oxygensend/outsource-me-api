<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920111806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, individual_id INT NOT NULL, job_offer_id INT NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A45BDDC1AE271C0D (individual_id), INDEX IDX_A45BDDC13481D195 (job_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, university_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATE DEFAULT NULL, field_of_study VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, grade DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DB0A5ED2309D1878 (university_id), INDEX IDX_DB0A5ED2AE271C0D (individual_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_of_employment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, form_of_employment_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, salary_range VARCHAR(255) DEFAULT NULL, redirect_count INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_288A3A4EFCEE64DE (form_of_employment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer_work_type (job_offer_id INT NOT NULL, work_type_id INT NOT NULL, INDEX IDX_3D44D4BA3481D195 (job_offer_id), INDEX IDX_3D44D4BA108734B1 (work_type_id), PRIMARY KEY(job_offer_id, work_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_position (id INT AUTO_INCREMENT NOT NULL, form_of_employment_id INT NOT NULL, individual_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_216B418EFCEE64DE (form_of_employment_id), INDEX IDX_216B418EAE271C0D (individual_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, individual_id INT DEFAULT NULL, name VARCHAR(3) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D4DB71B5AE271C0D (individual_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE opinion (id INT AUTO_INCREMENT NOT NULL, from_who_id INT NOT NULL, to_who_id INT NOT NULL, description LONGTEXT DEFAULT NULL, scale INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AB02B02779D320F1 (from_who_id), INDEX IDX_AB02B027D23057BC (to_who_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE university (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(9) DEFAULT NULL, description LONGTEXT DEFAULT NULL, github_url VARCHAR(255) DEFAULT NULL, linkedin_url VARCHAR(255) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, redirect_count INT NOT NULL, account_type INT NOT NULL, password VARCHAR(255) NOT NULL, email_confirmed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1AE271C0D FOREIGN KEY (individual_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC13481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2309D1878 FOREIGN KEY (university_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2AE271C0D FOREIGN KEY (individual_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4EFCEE64DE FOREIGN KEY (form_of_employment_id) REFERENCES form_of_employment (id)');
        $this->addSql('ALTER TABLE job_offer_work_type ADD CONSTRAINT FK_3D44D4BA3481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_work_type ADD CONSTRAINT FK_3D44D4BA108734B1 FOREIGN KEY (work_type_id) REFERENCES work_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_position ADD CONSTRAINT FK_216B418EFCEE64DE FOREIGN KEY (form_of_employment_id) REFERENCES form_of_employment (id)');
        $this->addSql('ALTER TABLE job_position ADD CONSTRAINT FK_216B418EAE271C0D FOREIGN KEY (individual_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE language ADD CONSTRAINT FK_D4DB71B5AE271C0D FOREIGN KEY (individual_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B02779D320F1 FOREIGN KEY (from_who_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027D23057BC FOREIGN KEY (to_who_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1AE271C0D');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC13481D195');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED2309D1878');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED2AE271C0D');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4EFCEE64DE');
        $this->addSql('ALTER TABLE job_offer_work_type DROP FOREIGN KEY FK_3D44D4BA3481D195');
        $this->addSql('ALTER TABLE job_offer_work_type DROP FOREIGN KEY FK_3D44D4BA108734B1');
        $this->addSql('ALTER TABLE job_position DROP FOREIGN KEY FK_216B418EFCEE64DE');
        $this->addSql('ALTER TABLE job_position DROP FOREIGN KEY FK_216B418EAE271C0D');
        $this->addSql('ALTER TABLE language DROP FOREIGN KEY FK_D4DB71B5AE271C0D');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B02779D320F1');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027D23057BC');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE form_of_employment');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('DROP TABLE job_offer_work_type');
        $this->addSql('DROP TABLE job_position');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE opinion');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE work_type');
    }
}
