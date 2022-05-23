<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220523172919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE multimedia ADD usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE multimedia ADD CONSTRAINT FK_61312863DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_61312863DB38439E ON multimedia (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE multimedia DROP FOREIGN KEY FK_61312863DB38439E');
        $this->addSql('DROP INDEX IDX_61312863DB38439E ON multimedia');
        $this->addSql('ALTER TABLE multimedia DROP usuario_id');
    }
}
