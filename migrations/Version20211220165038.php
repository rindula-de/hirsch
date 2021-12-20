<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220165038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('DROP INDEX orders_ibfk_1 ON orders');
        $this->addSql('CREATE INDEX FK_orders_hirsch ON orders (hirsch_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8157FCBC');
        $this->addSql('DROP INDEX fk_orders_hirsch ON orders');
        $this->addSql('CREATE INDEX orders_ibfk_1 ON orders (hirsch_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8157FCBC FOREIGN KEY (hirsch_id) REFERENCES hirsch (id)');
    }
}
