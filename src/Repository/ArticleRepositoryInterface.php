<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\AuthorInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\TagInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ArticleInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ArticleRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder;

    public function createShopListQueryBuilder(string $localeCode, ChannelInterface $channel, ?TagInterface $tag): QueryBuilder;

    /**
     * @return ArticleInterface[]
     */
    public function findAllEnabledAndPublishedByTag(string $localeCode, ChannelInterface $channel, TagInterface $tag, int $limit): array;

    public function findOneBySlug(string $slug, string $localeCode): ?ArticleInterface;

    public function findOnePublishedBySlug(string $slug, string $localeCode, ChannelInterface $channel): ?ArticleInterface;

    public function findAllEnabledAndPublishedByAuthor(string $localeCode, ChannelInterface $channel, AuthorInterface $author, int $limit): array;
}
