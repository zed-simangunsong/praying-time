<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503045600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Table box.
        $box = $schema->createTable('box');
        $box->addColumn('box_id', Types::INTEGER)->setAutoincrement(true)->setNotnull(true);
        $box->addColumn('box_name', Types::STRING)->setLength(50)->setNotnull(true);
        $box->addColumn('prayer_zone', Types::STRING)->setLength(5)->setNotnull(true);
        $box->addColumn('prayer_time_option', Types::BOOLEAN)->setDefault(1)->setNotnull(true);
        $box->addColumn('last_update', Types::DATETIME_MUTABLE)->setDefault('CURRENT_TIMESTAMP')->setNotnull(true);
        $box->setPrimaryKey(['box_id']);


        // Table subscriber.
        $subscriber = $schema->createTable('subscriber');
        $subscriber->addColumn('subscriber_id', Types::INTEGER)->setAutoincrement(true)->setNotnull(true);
        $subscriber->addColumn('subscriber_name', Types::STRING)->setLength(50)->setDefault(null);
        $subscriber->addColumn('password', Types::STRING)->setLength(150)->setDefault(null);
        $subscriber->addColumn('last_update', Types::DATETIME_MUTABLE)->setDefault('CURRENT_TIMESTAMP')->setNotnull(true);
        $subscriber->setPrimaryKey(['subscriber_id']);


        // Table subscriber_box.
        $subscriberBox = $schema->createTable('subscriber_box');
        $subscriberBox->addColumn('subscriber_id', Types::INTEGER)->setNotnull(true);
        $subscriberBox->addColumn('box_id', Types::INTEGER)->setNotnull(true);
        $subscriberBox->addColumn('last_update', Types::DATETIME_MUTABLE)->setDefault('CURRENT_TIMESTAMP')->setNotnull(true);
        $subscriberBox->setPrimaryKey(['subscriber_id', 'box_id']);


        // Table box_song.
        $boxSong = $schema->createTable('box_song');
        $boxSong->addColumn('box_song_id', Types::INTEGER)->setAutoincrement(true)->setNotnull(true);
        $boxSong->addColumn('box_id', Types::INTEGER)->setNotnull(true);
        $boxSong->addColumn('song_title', Types::STRING)->setLength(100)->setNotnull(true);
        $boxSong->addColumn('prayer_date', Types::DATE_MUTABLE)->setNotnull(true);
        $boxSong->addColumn('prayer_time', Types::TIME_MUTABLE)->setNotnull(true);
        $boxSong->addColumn('prayer_time_seq', Types::BIGINT)->setNotnull(true);
        $boxSong->addColumn('audio_file_path', Types::STRING)->setLength(200)->setNotnull(true);
        $boxSong->addColumn('last_update', Types::DATETIME_MUTABLE)->setDefault('CURRENT_TIMESTAMP')->setNotnull(true);
        $boxSong->setPrimaryKey(['box_song_id']);


        // Table cron.
        $cron = $schema->createTable('cron');
        $cron->addColumn('start_date', Types::DATE_MUTABLE)->setNotnull(true);
        $cron->addColumn('end_date', Types::DATE_MUTABLE)->setNotnull(true);
        $cron->addColumn('box_id', Types::INTEGER)->setNotnull(true);
        $cron->addColumn('prayer_zone', Types::STRING)->setLength(5)->setNotnull(true);
        $cron->addColumn('last_update', Types::DATETIME_MUTABLE)->setDefault('CURRENT_TIMESTAMP')->setNotnull(true);
        $cron->setPrimaryKey(['start_date', 'end_date', 'box_id', 'prayer_zone']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('cron');
        $schema->dropTable('subscriber_box');
        $schema->dropTable('box_song');
        $schema->dropTable('subscriber');
        $schema->dropTable('box');
    }
}
