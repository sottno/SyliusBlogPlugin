<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Repository;

use Doctrine\ORM\NonUniqueResultException;
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
    public function createListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createQueryBuilder('ba')
            ->addSelect('translation')
            ->leftJoin('ba.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;
    }

    public function createShopListQueryBuilder(string $localeCode, ChannelInterface $channel, ?TagInterface $tag): QueryBuilder
    {
        $queryBuilder = $this->createListQueryBuilder($localeCode)
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

    public function findAllEnabledAndPublishedByTag(string $localeCode, ChannelInterface $channel, TagInterface $tag, int $limit): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createShopListQueryBuilder($localeCode, $channel, $tag)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug, string $localeCode): ?ArticleInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
            ->andWhere('translation.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOnePublishedBySlug(string $slug, string $localeCode, ChannelInterface $channel): ?ArticleInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
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

    public function findAllEnabledAndPublishedByAuthor(string $localeCode, ChannelInterface $channel, AuthorInterface $author, int $limit): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
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
