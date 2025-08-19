<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812210854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // 1) Base tables without FKs first
        $this->addSql('CREATE TABLE "user" (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , email VARCHAR(180) NOT NULL, roles JSON NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');

        $this->addSql('CREATE TABLE association (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , owner_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, email_public VARCHAR(180) NOT NULL, phone_public VARCHAR(20) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, description TEXT NOT NULL, species_supported JSON NOT NULL --(DC2Type:json)
        , region VARCHAR(100) NOT NULL, department VARCHAR(100) NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(10) NOT NULL, street VARCHAR(255) NOT NULL, lat NUMERIC(10, 8) DEFAULT NULL, lng NUMERIC(11, 8) DEFAULT NULL, is_approved BOOLEAN NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , logo VARCHAR(255) DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_FD8521CC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CC7E3C61F9 ON association (owner_id)');

        $this->addSql('CREATE TABLE animal (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , association_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(100) NOT NULL, species VARCHAR(20) NOT NULL, sex VARCHAR(10) NOT NULL, birth_date DATE DEFAULT NULL, size VARCHAR(20) NOT NULL, color VARCHAR(100) NOT NULL, sterilized BOOLEAN NOT NULL, vaccinated BOOLEAN NOT NULL, identified BOOLEAN NOT NULL, description TEXT NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_6AAB231FEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6AAB231FEFB9C8A5 ON animal (association_id)');

        $this->addSql('CREATE TABLE animal_photo (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , animal_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , path VARCHAR(255) NOT NULL, is_main BOOLEAN NOT NULL, sort_index INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_35445DEC8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_35445DEC8E962C16 ON animal_photo (animal_id)');

        $this->addSql('CREATE TABLE wishlist_item (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , association_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , label VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, quantity_needed VARCHAR(255) NOT NULL, urgency VARCHAR(20) NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , notes TEXT DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_6424F4E8EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6424F4E8EFB9C8A5 ON wishlist_item (association_id)');

        $this->addSql('CREATE TABLE shelter_capacity (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , association_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , species VARCHAR(20) NOT NULL, capacity_total INTEGER NOT NULL, capacity_available INTEGER NOT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_82C772D5EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_82C772D5EFB9C8A5 ON shelter_capacity (association_id)');

        $this->addSql('CREATE TABLE vet_profile (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , clinic_name VARCHAR(255) NOT NULL, rpps_or_license VARCHAR(255) NOT NULL, services JSON NOT NULL --(DC2Type:json)
        , free_care_slots INTEGER NOT NULL, region VARCHAR(100) NOT NULL, department VARCHAR(100) NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(10) NOT NULL, street VARCHAR(255) NOT NULL, lat NUMERIC(10, 8) DEFAULT NULL, lng NUMERIC(11, 8) DEFAULT NULL, is_approved BOOLEAN NOT NULL, notes TEXT DEFAULT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_77ACE0AAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_77ACE0AAA76ED395 ON vet_profile (user_id)');

        $this->addSql('CREATE TABLE donation (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , association_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , amount INTEGER NOT NULL, currency VARCHAR(3) NOT NULL, stripe_checkout_id VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , message TEXT DEFAULT NULL, stripe_payment_intent_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_31E581A0A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_31E581A0EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_31E581A0A76ED395 ON donation (user_id)');
        $this->addSql('CREATE INDEX IDX_31E581A0EFB9C8A5 ON donation (association_id)');

        $this->addSql('CREATE TABLE foster_profile (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , species_accepted JSON NOT NULL --(DC2Type:json)
        , max_animals INTEGER NOT NULL, has_garden BOOLEAN NOT NULL, children_at_home BOOLEAN NOT NULL, other_pets BOOLEAN NOT NULL, availability_from DATE DEFAULT NULL, availability_to DATE DEFAULT NULL, region VARCHAR(100) NOT NULL, department VARCHAR(100) NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(10) NOT NULL, street VARCHAR(255) NOT NULL, lat NUMERIC(10, 8) DEFAULT NULL, lng NUMERIC(11, 8) DEFAULT NULL, notes TEXT DEFAULT NULL, is_visible BOOLEAN NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_E8AE703AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E8AE703AA76ED395 ON foster_profile (user_id)');

        $this->addSql('CREATE TABLE in_kind_donation (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , association_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , type VARCHAR(20) NOT NULL, description TEXT NOT NULL, quantity VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , notes TEXT DEFAULT NULL, region VARCHAR(100) NOT NULL, city VARCHAR(100) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A91010CFA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A91010CFEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A91010CFA76ED395 ON in_kind_donation (user_id)');
        $this->addSql('CREATE INDEX IDX_A91010CFEFB9C8A5 ON in_kind_donation (association_id)');

        $this->addSql('CREATE TABLE report (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , reporter_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , target_type VARCHAR(20) NOT NULL, target_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , reason TEXT NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , status VARCHAR(20) NOT NULL, admin_notes TEXT DEFAULT NULL, reviewed_at TIMESTAMP(0) DEFAULT NULL --(DC2Type:datetime_immutable)
        , closed_at TIMESTAMP(0) DEFAULT NULL --(DC2Type:datetime_immutable)
        , admin_action VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C42F7784E1CFE6F5 ON report (reporter_id)');

        $this->addSql('CREATE TABLE adoption_request (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , animal_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , requester_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , message TEXT NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , admin_notes TEXT DEFAULT NULL, reviewed_at TIMESTAMP(0) DEFAULT NULL --(DC2Type:datetime_immutable)
        , responded_at TIMESTAMP(0) DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_410896EE8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_410896EEED442CF4 FOREIGN KEY (requester_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_410896EE8E962C16 ON adoption_request (animal_id)');
        $this->addSql('CREATE INDEX IDX_410896EEED442CF4 ON adoption_request (requester_id)');

        // messenger tables (no FKs)
        $this->addSql('CREATE TABLE messenger_messages (id SERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , available_at TIMESTAMP(0) NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at TIMESTAMP(0) DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE adoption_request');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE animal_photo');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE donation');
        $this->addSql('DROP TABLE foster_profile');
        $this->addSql('DROP TABLE in_kind_donation');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE shelter_capacity');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vet_profile');
        $this->addSql('DROP TABLE wishlist_item');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
