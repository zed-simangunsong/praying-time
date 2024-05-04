<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504084715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subscriber_box ADD INDEX UX_subscriber_box__box_id (box_id)');
        $this->addSql('ALTER TABLE subscriber_box ADD INDEX UX_subscriber_box__subscribe_id (subscriber_id)');

        $this->addSql('ALTER TABLE subscriber_box ADD CONSTRAINT FK_subscriber_box__box '
            . 'FOREIGN KEY (box_id) REFERENCES box(box_id)');

        $this->addSql('ALTER TABLE subscriber_box ADD CONSTRAINT FK_subscriber_box__subscriber '
            . 'FOREIGN KEY (subscriber_id) REFERENCES subscriber(subscriber_id)');


        $this->addSql('ALTER TABLE box_song ADD INDEX UX_box_song__box_id (box_id)');

        $this->addSql('ALTER TABLE box_song ADD CONSTRAINT FK_box_song__box '
            . 'FOREIGN KEY (box_id) REFERENCES box(box_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE box_song DROP FOREIGN KEY FK_box_song__box');
        $this->addSql('ALTER TABLE box_song DROP INDEX UX_box_song__box_id');


        $this->addSql('ALTER TABLE subscriber_box DROP FOREIGN KEY FK_subscriber_box__subscriber');
        $this->addSql('ALTER TABLE subscriber_box DROP FOREIGN KEY FK_subscriber_box__box');
        $this->addSql('ALTER TABLE subscriber_box DROP INDEX UX_subscriber_box__subscribe_id');
        $this->addSql('ALTER TABLE subscriber_box DROP INDEX UX_subscriber_box__box_id');
    }
}
