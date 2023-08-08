<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808124950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C14584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_64C19C14584665A ON category (product_id)');
        $this->addSql('ALTER TABLE eancode ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE eancode ADD CONSTRAINT FK_F719466B4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F719466B4584665A ON eancode (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C14584665A');
        $this->addSql('DROP INDEX IDX_64C19C14584665A');
        $this->addSql('ALTER TABLE category DROP product_id');
        $this->addSql('ALTER TABLE eancode DROP CONSTRAINT FK_F719466B4584665A');
        $this->addSql('DROP INDEX IDX_F719466B4584665A');
        $this->addSql('ALTER TABLE eancode DROP product_id');
    }
}
