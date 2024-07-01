<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702145615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE monsieurbiz_blog_article (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, image VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, publishedAt DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_article_tags (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_81F1F9E97294869C (article_id), INDEX IDX_81F1F9E9BAD26311 (tag_id), PRIMARY KEY(article_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_article_channels (article_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_9F50BAA27294869C (article_id), INDEX IDX_9F50BAA272F5A1AA (channel_id), PRIMARY KEY(article_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_article_authors (article_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_CCC1D3057294869C (article_id), INDEX IDX_CCC1D305F675F31B (author_id), PRIMARY KEY(article_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_article_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_AC951C7A2C2AC5D3 (translatable_id), UNIQUE INDEX monsieurbiz_blog_article_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_tag (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) DEFAULT 1 NOT NULL, position INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monsieurbiz_blog_tag_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_7BF826C2C2AC5D3 (translatable_id), UNIQUE INDEX monsieurbiz_blog_tag_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_tags ADD CONSTRAINT FK_81F1F9E97294869C FOREIGN KEY (article_id) REFERENCES monsieurbiz_blog_article (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_tags ADD CONSTRAINT FK_81F1F9E9BAD26311 FOREIGN KEY (tag_id) REFERENCES monsieurbiz_blog_tag (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_channels ADD CONSTRAINT FK_9F50BAA27294869C FOREIGN KEY (article_id) REFERENCES monsieurbiz_blog_article (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_channels ADD CONSTRAINT FK_9F50BAA272F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_authors ADD CONSTRAINT FK_CCC1D3057294869C FOREIGN KEY (article_id) REFERENCES monsieurbiz_blog_article (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_authors ADD CONSTRAINT FK_CCC1D305F675F31B FOREIGN KEY (author_id) REFERENCES monsieurbiz_blog_author (id)');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_translation ADD CONSTRAINT FK_AC951C7A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES monsieurbiz_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monsieurbiz_blog_tag_translation ADD CONSTRAINT FK_7BF826C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES monsieurbiz_blog_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_tags DROP FOREIGN KEY FK_81F1F9E97294869C');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_tags DROP FOREIGN KEY FK_81F1F9E9BAD26311');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_channels DROP FOREIGN KEY FK_9F50BAA27294869C');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_channels DROP FOREIGN KEY FK_9F50BAA272F5A1AA');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_authors DROP FOREIGN KEY FK_CCC1D3057294869C');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_authors DROP FOREIGN KEY FK_CCC1D305F675F31B');
        $this->addSql('ALTER TABLE monsieurbiz_blog_article_translation DROP FOREIGN KEY FK_AC951C7A2C2AC5D3');
        $this->addSql('ALTER TABLE monsieurbiz_blog_tag_translation DROP FOREIGN KEY FK_7BF826C2C2AC5D3');
        $this->addSql('DROP TABLE monsieurbiz_blog_article');
        $this->addSql('DROP TABLE monsieurbiz_blog_article_tags');
        $this->addSql('DROP TABLE monsieurbiz_blog_article_channels');
        $this->addSql('DROP TABLE monsieurbiz_blog_article_authors');
        $this->addSql('DROP TABLE monsieurbiz_blog_article_translation');
        $this->addSql('DROP TABLE monsieurbiz_blog_author');
        $this->addSql('DROP TABLE monsieurbiz_blog_tag');
        $this->addSql('DROP TABLE monsieurbiz_blog_tag_translation');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}
