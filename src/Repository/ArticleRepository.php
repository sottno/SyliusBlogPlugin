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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @template T of ArticleInterface
 *
 * @implements ArticleRepositoryInterface<T>
 */
final class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    public function createListQueryBuilderByType(string $localeCode, string $type): QueryBuilder
    {
        return $this->createQueryBuilder('ba')
            ->addSelect('translation')
            ->leftJoin('ba.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
            ->andWhere('ba.type = :type')
            ->setParameter('type', $type)
        ;
    }

    public function createShopListQueryBuilderByType(string $localeCode, string $type, ChannelInterface $channel, ?TagInterface $tag): QueryBuilder
    {
        $queryBuilder = $this->createListQueryBuilderByType($localeCode, $type)
            ->andWhere(':channel MEMBER OF ba.channels')
            ->andWhere('ba.enabled = true')
            ->andWhere('ba.state = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', ArticleInterface::STATE_PUBLISHED)
        ;

        if (null !== $tag) {
            $queryBuilder
                ->andWhere(':tag MEMBER OF ba.tags')
                ->setParameter('tag', $tag)
            ;
        }

        return $queryBuilder;
    }

    public function findAllEnabledAndPublishedByTag(string $localeCode, string $type, ChannelInterface $channel, TagInterface $tag, int $limit): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createShopListQueryBuilderByType($localeCode, $type, $channel, $tag)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOnePublishedBySlug(string $slug, string $localeCode, string $type, ChannelInterface $channel): ?ArticleInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilderByType($localeCode, $type)
            ->andWhere('translation.slug = :slug')
            ->andWhere(':channel MEMBER OF ba.channels')
            ->andWhere('ba.enabled = true')
            ->andWhere('ba.state = :state')
            ->setParameter('slug', $slug)
            ->setParameter('channel', $channel)
            ->setParameter('state', ArticleInterface::STATE_PUBLISHED)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllEnabledAndPublishedByAuthor(string $localeCode, string $type, ChannelInterface $channel, AuthorInterface $author, int $limit): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilderByType($localeCode, $type)
            ->andWhere(':channel MEMBER OF ba.channels')
            ->andWhere('ba.enabled = true')
            ->andWhere('ba.state = :state')
            ->andWhere(':author MEMBER OF ba.authors')
            ->setParameter('channel', $channel)
            ->setParameter('state', ArticleInterface::STATE_PUBLISHED)
            ->setParameter('author', $author)
            ->addOrderBy('ba.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
