<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522160801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE multimedia (id INT AUTO_INCREMENT NOT NULL, archivo LONGBLOB NOT NULL, formato VARCHAR(10) NOT NULL, nombre VARCHAR(50) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, likes INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario_multimedia (usuario_id INT NOT NULL, multimedia_id INT NOT NULL, INDEX IDX_920162C1DB38439E (usuario_id), INDEX IDX_920162C120531EB8 (multimedia_id), PRIMARY KEY(usuario_id, multimedia_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE usuario_multimedia ADD CONSTRAINT FK_920162C1DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usuario_multimedia ADD CONSTRAINT FK_920162C120531EB8 FOREIGN KEY (multimedia_id) REFERENCES multimedia (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_multimedia DROP FOREIGN KEY FK_920162C120531EB8');
        $this->addSql('DROP TABLE multimedia');
        $this->addSql('DROP TABLE usuario_multimedia');
    }
}
