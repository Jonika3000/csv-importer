<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412193554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE tblProductData (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, added_at DATETIME DEFAULT NULL, discontinued_at DATETIME DEFAULT NULL, timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_2C11248677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE tblProductData
        SQL);
    }
}
