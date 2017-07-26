<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170720103922 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Table for the scheduler Task';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('CREATE TABLE wwwision_eventscheduler_task (id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, payload LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id, type)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE wwwision_eventscheduler_task');
    }
}