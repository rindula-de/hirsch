<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104135414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TABLE IF EXISTS phinxlog;");
        $this->addSql("TRUNCATE TABLE orders;");
        $this->addSql("ALTER TABLE `orders` ADD COLUMN `hirsch_id` INT NOT NULL AFTER `id`, DROP COLUMN `name`, DROP FOREIGN KEY `orders_ibfk_1`;");
        $this->addSql("ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`hirsch_id`) REFERENCES `hirsch` (`id`) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE `orders` CHANGE COLUMN `for` `for_date` DATE NOT NULL AFTER `note`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->throwIrreversibleMigrationException("Hier gehts nicht weiter zurück!");
    }
}
