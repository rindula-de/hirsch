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
        return 'Initial Migration';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE TABLE IF NOT EXISTS `hirsch` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `slug` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
            `name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
            `display` TINYINT(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE INDEX `slug` (`slug`) USING BTREE
        )
        ;
        ");
        $this->addSql("CREATE TABLE `holidays` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `start` DATE NOT NULL,
            `end` DATE NOT NULL,
            PRIMARY KEY (`id`) USING BTREE
        )
        ;
        ");
        $this->addSql("CREATE TABLE `orders` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `hirsch_id` INT(11) NOT NULL,
            `note` VARCHAR(1000) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
            `for_date` DATE NOT NULL,
            `created` DATETIME NOT NULL DEFAULT current_timestamp(),
            `orderedby` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
            PRIMARY KEY (`id`) USING BTREE,
            INDEX `FK_orders_hirsch` (`hirsch_id`) USING BTREE
        )
        ;
        ");
        $this->addSql("CREATE TABLE `paypalmes` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `link` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
            `name` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_ci',
            `email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
            `bar` DATE NULL DEFAULT NULL,
            PRIMARY KEY (`id`) USING BTREE
        )
        ;
        ");
        $this->addSql("CREATE TABLE `payhistory` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `paypalme_id` INT(11) NOT NULL,
            `created` DATETIME NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`) USING BTREE,
            INDEX `paypalme_id` (`paypalme_id`) USING BTREE
        )
        ;
        ");
        $this->addSql('
        CREATE PROCEDURE `clear payhistory`()
        BEGIN
            DELETE FROM payhistory WHERE created <= NOW() - INTERVAL 1 DAY;
        END;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TABLE `holidays`");
        $this->addSql("DROP TABLE `orders`");
        $this->addSql("DROP TABLE `payhistory`");
        $this->addSql("DROP TABLE `paypalmes`");
        $this->addSql("DROP TABLE `hirsch`");
        $this->addSql("DROP PROCEDURE IF EXISTS `clear payhistory`");
    }
}
