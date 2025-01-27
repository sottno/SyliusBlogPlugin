<?php

/*
 * This file is part of Monsieur Biz' Blog plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @method ArticleTranslationInterface doGetTranslation(?string $locale = null)
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Article implements ArticleInterface
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait {
        TranslatableTrait::__construct as private initializeTranslationsCollection;
        TranslatableTrait::getTranslation as private doGetTranslation;
    }

    private ?int $id = null;

    protected ?string $image = null;

    protected ?string $video = null;

    protected ?string $thumbnailImage = null;

    /** @var bool */
    protected $enabled = true;

    /** @var Collection<array-key, ChannelInterface> */
    protected Collection $channels;

    /** @var Collection<array-key, TagInterface> */
    protected Collection $tags;

    protected ?DateTimeInterface $publishedAt;

    /** @var Collection<array-key, AuthorInterface> */
    protected Collection $authors;

    protected string $type = ArticleInterface::BLOG_TYPE;

    protected string $state = ArticleInterface::STATE_DRAFT;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->channels = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->getTranslation()->setTitle($title);
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): void
    {
        $this->video = $video;
    }

    public function getThumbnailImage(): ?string
    {
        return $this->thumbnailImage;
    }

    public function setThumbnailImage(?string $thumbnailImage): void
    {
        $this->thumbnailImage = $thumbnailImage;
    }

    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    public function getContent(): ?string
    {
        return $this->getTranslation()->getContent();
    }

    public function setContent(string $content): void
    {
        $this->getTranslation()->setContent($content);
    }

    public function addTag(TagInterface $tag): void
    {
        if (!$this->hasTag($tag)) {
            $this->tags->add($tag);
            $tag->addArticle($this);
        }
    }

    public function removeTag(TagInterface $tag): void
    {
        if ($this->hasTag($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeArticle($this);
        }
    }

    public function hasTag(TagInterface $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!\in_array($type, ArticleInterface::TYPES, true)) {
            throw new InvalidArgumentException(\sprintf('Invalid type "%s". Available types are: %s', $type, implode(', ', ArticleInterface::TYPES)));
        }

        $this->type = $type;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function addAuthor(AuthorInterface $author): void
    {
        if (!$this->hasAuthor($author)) {
            $this->authors->add($author);
        }
    }

    public function removeAuthor(AuthorInterface $author): void
    {
        if ($this->hasAuthor($author)) {
            $this->authors->removeElement($author);
        }
    }

    public function hasAuthor(AuthorInterface $author): bool
    {
        return $this->authors->contains($author);
    }

    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function getMetaTitle(): ?string
    {
        return $this->getTranslation()->getMetaTitle();
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->getTranslation()->setMetaTitle($metaTitle);
    }

    public function getMetaDescription(): ?string
    {
        return $this->getTranslation()->getMetaDescription();
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
    }

    public function getMetaKeywords(): ?string
    {
        return $this->getTranslation()->getMetaKeywords();
    }

    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->getTranslation()->setMetaKeywords($metaKeywords);
    }

    public function publish(): void
    {
        $this->publishedAt = new DateTime();
    }

    public function getTranslation(?string $locale = null): ArticleTranslationInterface
    {
        return $this->doGetTranslation($locale);
    }

    protected function createTranslation(): ArticleTranslationInterface
    {
        return new ArticleTranslation();
    }
}
