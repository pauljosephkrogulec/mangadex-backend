<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126132755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id CHAR(36) NOT NULL, name CLOB NOT NULL, image_url CLOB DEFAULT NULL, biography CLOB DEFAULT NULL, twitter CLOB DEFAULT NULL, pixiv CLOB DEFAULT NULL, melon_book CLOB DEFAULT NULL, fan_box CLOB DEFAULT NULL, booth CLOB DEFAULT NULL, nico_video CLOB DEFAULT NULL, skeb CLOB DEFAULT NULL, fantia CLOB DEFAULT NULL, tumblr CLOB DEFAULT NULL, youtube CLOB DEFAULT NULL, weibo CLOB DEFAULT NULL, naver CLOB DEFAULT NULL, website CLOB DEFAULT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE chapter (id CHAR(36) NOT NULL, title VARCHAR(255) DEFAULT NULL, volume VARCHAR(255) DEFAULT NULL, chapter VARCHAR(8) DEFAULT NULL, pages INTEGER NOT NULL, translated_language VARCHAR(10) NOT NULL, external_url VARCHAR(512) DEFAULT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, publish_at DATETIME DEFAULT NULL, readable_at DATETIME DEFAULT NULL, is_unavailable BOOLEAN NOT NULL, uploader_id CHAR(36) NOT NULL, manga_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_F981B52E16678C77 FOREIGN KEY (uploader_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F981B52E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F981B52E16678C77 ON chapter (uploader_id)');
        $this->addSql('CREATE INDEX IDX_F981B52E7B6461 ON chapter (manga_id)');
        $this->addSql('CREATE TABLE chapter_scanlation_groups (chapter_id CHAR(36) NOT NULL, scanlation_group_id CHAR(36) NOT NULL, PRIMARY KEY (chapter_id, scanlation_group_id), CONSTRAINT FK_F7D77842579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F7D7784226D00C4 FOREIGN KEY (scanlation_group_id) REFERENCES scanlation_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F7D77842579F4768 ON chapter_scanlation_groups (chapter_id)');
        $this->addSql('CREATE INDEX IDX_F7D7784226D00C4 ON chapter_scanlation_groups (scanlation_group_id)');
        $this->addSql('CREATE TABLE cover_art (id CHAR(36) NOT NULL, volume VARCHAR(255) DEFAULT NULL, file_name VARCHAR(512) NOT NULL, locale VARCHAR(50) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, manga_id CHAR(36) NOT NULL, uploader_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_4EA5C33D7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4EA5C33D16678C77 FOREIGN KEY (uploader_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4EA5C33D7B6461 ON cover_art (manga_id)');
        $this->addSql('CREATE INDEX IDX_4EA5C33D16678C77 ON cover_art (uploader_id)');
        $this->addSql('CREATE TABLE custom_list (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, visibility VARCHAR(20) NOT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_45BE30E57E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_45BE30E57E3C61F9 ON custom_list (owner_id)');
        $this->addSql('CREATE TABLE custom_list_manga (custom_list_id CHAR(36) NOT NULL, manga_id CHAR(36) NOT NULL, PRIMARY KEY (custom_list_id, manga_id), CONSTRAINT FK_903636AA3AF77F46 FOREIGN KEY (custom_list_id) REFERENCES custom_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_903636AA7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_903636AA3AF77F46 ON custom_list_manga (custom_list_id)');
        $this->addSql('CREATE INDEX IDX_903636AA7B6461 ON custom_list_manga (manga_id)');
        $this->addSql('CREATE TABLE manga (id CHAR(36) NOT NULL, title CLOB NOT NULL, alt_titles CLOB NOT NULL, description CLOB NOT NULL, is_locked BOOLEAN NOT NULL, links CLOB DEFAULT NULL, official_links CLOB DEFAULT NULL, original_language VARCHAR(10) NOT NULL, last_volume VARCHAR(255) DEFAULT NULL, last_chapter VARCHAR(255) DEFAULT NULL, publication_demographic VARCHAR(20) DEFAULT NULL, status VARCHAR(20) NOT NULL, year INTEGER DEFAULT NULL, content_rating VARCHAR(20) NOT NULL, chapter_numbers_reset_on_new_volume BOOLEAN NOT NULL, available_translated_languages CLOB NOT NULL, latest_uploaded_chapter CHAR(36) DEFAULT NULL, state VARCHAR(20) NOT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE manga_authors (manga_id CHAR(36) NOT NULL, author_id CHAR(36) NOT NULL, PRIMARY KEY (manga_id, author_id), CONSTRAINT FK_10BBFF507B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_10BBFF50F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_10BBFF507B6461 ON manga_authors (manga_id)');
        $this->addSql('CREATE INDEX IDX_10BBFF50F675F31B ON manga_authors (author_id)');
        $this->addSql('CREATE TABLE manga_artists (manga_id CHAR(36) NOT NULL, author_id CHAR(36) NOT NULL, PRIMARY KEY (manga_id, author_id), CONSTRAINT FK_F664551F7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F664551FF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F664551F7B6461 ON manga_artists (manga_id)');
        $this->addSql('CREATE INDEX IDX_F664551FF675F31B ON manga_artists (author_id)');
        $this->addSql('CREATE TABLE manga_tags (manga_id CHAR(36) NOT NULL, tag_id CHAR(36) NOT NULL, PRIMARY KEY (manga_id, tag_id), CONSTRAINT FK_30E87D6C7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_30E87D6CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_30E87D6C7B6461 ON manga_tags (manga_id)');
        $this->addSql('CREATE INDEX IDX_30E87D6CBAD26311 ON manga_tags (tag_id)');
        $this->addSql('CREATE TABLE manga_recommendation (id CHAR(36) NOT NULL, score DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, manga_id CHAR(36) NOT NULL, recommended_manga_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_58CEE0697B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_58CEE0698FCCE365 FOREIGN KEY (recommended_manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_58CEE0697B6461 ON manga_recommendation (manga_id)');
        $this->addSql('CREATE INDEX IDX_58CEE0698FCCE365 ON manga_recommendation (recommended_manga_id)');
        $this->addSql('CREATE TABLE manga_relation (id CHAR(36) NOT NULL, relation VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, manga_id CHAR(36) NOT NULL, target_manga_id CHAR(36) NOT NULL, source_manga_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_1510C00A7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1510C00ADAD0B408 FOREIGN KEY (target_manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1510C00A435B52B3 FOREIGN KEY (source_manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1510C00A7B6461 ON manga_relation (manga_id)');
        $this->addSql('CREATE INDEX IDX_1510C00ADAD0B408 ON manga_relation (target_manga_id)');
        $this->addSql('CREATE INDEX IDX_1510C00A435B52B3 ON manga_relation (source_manga_id)');
        $this->addSql('CREATE TABLE report (id CHAR(36) NOT NULL, details CLOB NOT NULL, object_id CHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, creator_id CHAR(36) NOT NULL, chapter_id CHAR(36) DEFAULT NULL, manga_id CHAR(36) DEFAULT NULL, author_id CHAR(36) DEFAULT NULL, scanlation_group_id CHAR(36) DEFAULT NULL, tag_id CHAR(36) DEFAULT NULL, cover_art_id CHAR(36) DEFAULT NULL, PRIMARY KEY (id), CONSTRAINT FK_C42F778461220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F77847B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784F675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F778426D00C4 FOREIGN KEY (scanlation_group_id) REFERENCES scanlation_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C42F7784C996057 FOREIGN KEY (cover_art_id) REFERENCES cover_art (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C42F778461220EA6 ON report (creator_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784579F4768 ON report (chapter_id)');
        $this->addSql('CREATE INDEX IDX_C42F77847B6461 ON report (manga_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784F675F31B ON report (author_id)');
        $this->addSql('CREATE INDEX IDX_C42F778426D00C4 ON report (scanlation_group_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784BAD26311 ON report (tag_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784C996057 ON report (cover_art_id)');
        $this->addSql('CREATE TABLE scanlation_group (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, alt_names CLOB NOT NULL, website VARCHAR(512) DEFAULT NULL, irc_server VARCHAR(255) DEFAULT NULL, irc_channel VARCHAR(255) DEFAULT NULL, discord VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, description CLOB DEFAULT NULL, twitter VARCHAR(512) DEFAULT NULL, manga_updates VARCHAR(128) DEFAULT NULL, focused_languages CLOB DEFAULT NULL, inactive BOOLEAN NOT NULL, locked BOOLEAN NOT NULL, official BOOLEAN NOT NULL, verified BOOLEAN NOT NULL, ex_licensed BOOLEAN NOT NULL, publish_delay VARCHAR(50) DEFAULT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, leader_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_9B892DAC73154ED4 FOREIGN KEY (leader_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9B892DAC73154ED4 ON scanlation_group (leader_id)');
        $this->addSql('CREATE TABLE tag (id CHAR(36) NOT NULL, name CLOB NOT NULL, description CLOB NOT NULL, "group" VARCHAR(20) NOT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL, username VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(1024) NOT NULL, roles CLOB NOT NULL, version INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE user_follows (user_source CHAR(36) NOT NULL, user_target CHAR(36) NOT NULL, PRIMARY KEY (user_source, user_target), CONSTRAINT FK_136E94793AD8644E FOREIGN KEY (user_source) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_136E9479233D34C1 FOREIGN KEY (user_target) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_136E94793AD8644E ON user_follows (user_source)');
        $this->addSql('CREATE INDEX IDX_136E9479233D34C1 ON user_follows (user_target)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE chapter_scanlation_groups');
        $this->addSql('DROP TABLE cover_art');
        $this->addSql('DROP TABLE custom_list');
        $this->addSql('DROP TABLE custom_list_manga');
        $this->addSql('DROP TABLE manga');
        $this->addSql('DROP TABLE manga_authors');
        $this->addSql('DROP TABLE manga_artists');
        $this->addSql('DROP TABLE manga_tags');
        $this->addSql('DROP TABLE manga_recommendation');
        $this->addSql('DROP TABLE manga_relation');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE scanlation_group');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_follows');
    }
}
