<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201093203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY payhistory_ibfk_1');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT DEFAULT NULL, CHANGE created created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT FK_57125D87FD7D48D0 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id)');
        $this->addSql('ALTER TABLE paypalmes CHANGE email email VARCHAR(255) DEFAULT \'NULL\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('ALTER TABLE orders CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
        $this->addSql('ALTER TABLE payhistory DROP FOREIGN KEY FK_57125D87FD7D48D0');
        $this->addSql('ALTER TABLE payhistory CHANGE paypalme_id paypalme_id INT NOT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE payhistory ADD CONSTRAINT payhistory_ibfk_1 FOREIGN KEY (paypalme_id) REFERENCES paypalmes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paypalmes CHANGE email email VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
    }
}
