<?php

declare(strict_types=1);

/*
 * (c) Sven Nolting, 2023
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710191147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $paypalmes = $this->connection->fetchAllAssociative('SELECT * FROM paypalmes');
        foreach ($paypalmes as $paypalme) {
            $uuid = Uuid::v7()->toBinary();
            $this->addSql('UPDATE paypalmes SET id = :newid WHERE id = :id', ['id' => $paypalme['id'], 'newid' => $uuid]);
            $this->addSql('UPDATE payhistory SET paypalme_id = :newid WHERE paypalme_id = :id', ['id' => $paypalme['id'], 'newid' => $uuid]);
        }
    }

    public function down(Schema $schema): void
    {
        throw new \RuntimeException('No way to go down!');
    }
}
