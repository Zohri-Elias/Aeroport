<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404071926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD ref_vol_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EA329383 FOREIGN KEY (ref_vol_id) REFERENCES vol (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955EA329383 ON reservation (ref_vol_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EA329383
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C84955EA329383 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP ref_vol_id
        SQL);
    }
}
