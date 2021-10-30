<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211030161558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE phinxlog');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_1');
        $this->addSql('ALTER TABLE orders CHANGE name name VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE5E237E06 FOREIGN KEY (name) REFERENCES hirsch (slug)');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY payhistory_ibfk_1');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT FK_57125D87FD7D48D0 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE phinxlog (version BIGINT NOT NULL, migration_name VARCHAR(100) CHARACTER SET utf8 DEFAULT \'NULL\' COLLATE `utf8_general_ci`, start_time DATETIME DEFAULT \'NULL\', end_time DATETIME DEFAULT \'NULL\', breakpoint TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE5E237E06');
        $this->addSql('ALTER TABLE orders CHANGE name name VARCHAR(191) CHARACTER SET utf8mb4 DEFAULT \'\'\'\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT orders_ibfk_1 FOREIGN KEY (name) REFERENCES hirsch (slug) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY FK_57125D87FD7D48D0');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT NOT NULL');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT payhistory_ibfk_1 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
