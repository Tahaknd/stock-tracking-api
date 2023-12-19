<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219132950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE material_stock_warehouse DROP FOREIGN KEY FK_3FDDDB0AA76ED395');
        $this->addSql('DROP INDEX IDX_3FDDDB0AA76ED395 ON material_stock_warehouse');
        $this->addSql('DROP INDEX `primary` ON material_stock_warehouse');
        $this->addSql('ALTER TABLE material_stock_warehouse DROP user_id');
        $this->addSql('ALTER TABLE material_stock_warehouse ADD PRIMARY KEY (material_id, warehouse_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON material_stock_warehouse');
        $this->addSql('ALTER TABLE material_stock_warehouse ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE material_stock_warehouse ADD CONSTRAINT FK_3FDDDB0AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3FDDDB0AA76ED395 ON material_stock_warehouse (user_id)');
        $this->addSql('ALTER TABLE material_stock_warehouse ADD PRIMARY KEY (material_id, warehouse_id, user_id)');
    }
}
