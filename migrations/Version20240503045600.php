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
        $this->addSql('CREATE TABLE box (
              box_id bigint(20) NOT NULL AUTO_INCREMENT,
              box_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              prayer_zone char(5) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              prayer_time_option tinyint(1) DEFAULT \'1\',
              last_update datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (box_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci');

        $this->addSql('CREATE TABLE subscriber (
              subscriber_id bigint(20) NOT NULL AUTO_INCREMENT,
              subscriber_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              password varchar(150) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              last_update datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (subscriber_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci');

        $this->addSql('CREATE TABLE subscriber_box (
              subscriber_id bigint(20) NOT NULL,
              box_id bigint(20) NOT NULL,
              last_update datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (subscriber_id,box_id),
              KEY subscriber_box_ibfk_1 (subscriber_id),
              KEY subscriber_box_ibfk_2 (box_id),
              CONSTRAINT subscriber_box_ibfk_1 FOREIGN KEY (subscriber_id) REFERENCES subscriber (subscriber_id),
              CONSTRAINT subscriber_box_ibfk_2 FOREIGN KEY (box_id) REFERENCES box (box_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci');

        $this->addSql('CREATE TABLE box_song (
              box_song_id bigint(20) NOT NULL AUTO_INCREMENT,
              box_id bigint(20) NOT NULL,
              song_title varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
              prayer_date date NOT NULL,
              prayer_time time NOT NULL,
              prayer_time_seq smallint(6) NOT NULL,
              audio_file_path varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              last_update datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (box_song_id),
              KEY box_song_ibfk_1 (box_id),
              CONSTRAINT box_song_ibfk_1 FOREIGN KEY (box_id) REFERENCES box (box_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci');

        $this->addSql('CREATE TABLE cron (
              start_date date NOT NULL,
              end_date date NOT NULL,
              box_id bigint(20) NOT NULL,
              prayer_zone char(5) COLLATE utf8mb4_unicode_520_ci NOT NULL,
              last_update datetime DEFAULT NULL,
              PRIMARY KEY (start_date,end_date,box_id,prayer_zone)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE box');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE subscriber_box');
        $this->addSql('DROP TABLE box_song');
        $this->addSql('DROP TABLE cron');
    }
}
