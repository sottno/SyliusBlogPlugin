<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Factory;

use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

#[AsDecorator(decorates: 'monsieurbiz_blog.factory.article')]
final class ArticleFactory implements ArticleFactoryInterface
{
    public function __construct(
        #[AutowireDecorated]
        private readonly FactoryInterface $originalArticleFactory
    ) {
    }

    public function createNew(): ArticleInterface
    {
        /** @var ArticleInterface */
        return $this->originalArticleFactory->createNew();
    }

    public function createNewWithType(string $type): ArticleInterface
    {
        $article = $this->createNew();
        $article->setType($type);

        return $article;
    }
}
