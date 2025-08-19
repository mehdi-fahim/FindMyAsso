<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250814165058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_comment (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , admin_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , entity_type VARCHAR(50) NOT NULL, entity_id VARCHAR(36) NOT NULL, comment TEXT NOT NULL, "action" VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_5048D0E5642B8210 FOREIGN KEY (admin_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5048D0E5642B8210 ON admin_comment (admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin_comment');
    }
}
