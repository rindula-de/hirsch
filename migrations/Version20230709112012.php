<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230709112012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hirsch CHANGE slug slug VARCHAR(191) NOT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('ALTER TABLE orders CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE note note VARCHAR(1000) DEFAULT \'\' NOT NULL, CHANGE orderedby orderedby VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX fk_orders_hirsch ON orders');
        $this->addSql('CREATE INDEX IDX_E52FFDEE8157FCBC ON orders (hirsch_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY FK_57125D87FD7D48D0');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT NOT NULL');
        $this->addSql('DROP INDEX paypalme_id ON payhistory');
        $this->addSql('CREATE INDEX IDX_57125D87FD7D48D0 ON payhistory (paypalme_id)');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT FK_57125D87FD7D48D0 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id)');
        $this->addSql('ALTER TABLE paypalmes CHANGE link link VARCHAR(100) NOT NULL, CHANGE name name VARCHAR(100) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hirsch CHANGE slug slug VARCHAR(191) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY FK_57125D87FD7D48D0');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX idx_57125d87fd7d48d0 ON payhistory');
        $this->addSql('CREATE INDEX paypalme_id ON payhistory (paypalme_id)');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT FK_57125D87FD7D48D0 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id)');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('ALTER TABLE orders CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE note note VARCHAR(1000) DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE orderedby orderedby VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX idx_e52ffdee8157fcbc ON orders');
        $this->addSql('CREATE INDEX FK_orders_hirsch ON orders (hirsch_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
        $this->addSql('ALTER TABLE paypalmes CHANGE link link VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(100) DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
