<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Form\AbstractType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190124175849 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE restaurant ADD user_id_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EB95123F9D86650F ON restaurant (user_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F9D86650F');
        $this->addSql('DROP INDEX IDX_EB95123F9D86650F ON restaurant');
        $this->addSql('ALTER TABLE restaurant ADD user_id INT NOT NULL, DROP user_id_id');
    }
}
