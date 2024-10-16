<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\UiElement;

use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use MonsieurBiz\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use MonsieurBiz\SyliusRichEditorPlugin\UiElement\UiElementInterface;
use MonsieurBiz\SyliusRichEditorPlugin\UiElement\UiElementTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ArticlesByTagsUiElement implements UiElementInterface
{
    use UiElementTrait;

    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly LocaleContextInterface $localeContext,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    public function getArticles(array $tags, int $limit): array
    {
        return $this->articleRepository->findAllEnabledAndPublishedByTags(
            $this->localeContext->getLocaleCode(),
            ArticleInterface::BLOG_TYPE,
            $this->channelContext->getChannel(),
            $tags,
            $limit
        );
    }
}
