<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102240000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove veterinarian, adoption, donation and map related tables';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les tables liées aux vétérinaires
        $this->addSql('DROP TABLE IF EXISTS vet_profile');
        
        // Supprimer les tables liées aux adoptions
        $this->addSql('DROP TABLE IF EXISTS adoption_request');
        $this->addSql('DROP TABLE IF EXISTS animal_photo');
        $this->addSql('DROP TABLE IF EXISTS animal');
        
        // Supprimer les tables liées aux dons
        $this->addSql('DROP TABLE IF EXISTS donation');
        $this->addSql('DROP TABLE IF EXISTS in_kind_donation');
        
        // Supprimer les colonnes liées aux entités supprimées dans la table user
        $this->addSql('ALTER TABLE "user" DROP COLUMN IF EXISTS vet_profile_id');
        
        // Supprimer les colonnes liées aux entités supprimées dans la table association
        $this->addSql('ALTER TABLE association DROP COLUMN IF EXISTS animals_id');
        $this->addSql('ALTER TABLE association DROP COLUMN IF EXISTS donations_id');
        $this->addSql('ALTER TABLE association DROP COLUMN IF EXISTS in_kind_donations_id');
    }

    public function down(Schema $schema): void
    {
        // Cette migration ne peut pas être annulée car nous supprimons des données
        $this->addSql('-- Migration cannot be reversed as it removes data');
    }
}
