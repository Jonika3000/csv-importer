<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413145816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tblproductdata CHANGE added_at added_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE discontinued_at discontinued_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE timestamp timestamp INT NOT NULL, CHANGE stock_level stock_level INT UNSIGNED DEFAULT NULL, CHANGE price price NUMERIC(10, 2) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tblProductData CHANGE added_at added_at DATETIME DEFAULT NULL, CHANGE discontinued_at discontinued_at DATETIME DEFAULT NULL, CHANGE timestamp timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE stock_level stock_level INT UNSIGNED NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL
        SQL);
    }
}
