<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902231600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add questionnaire answers and housing type to foster profile';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foster_profile ADD questionnaire_answers JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE foster_profile ADD housing_type VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foster_profile DROP questionnaire_answers');
        $this->addSql('ALTER TABLE foster_profile DROP housing_type');
    }
}
