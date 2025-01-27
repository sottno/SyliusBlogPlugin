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

namespace MonsieurBiz\SyliusBlogPlugin\UiElement;

use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use MonsieurBiz\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use MonsieurBiz\SyliusRichEditorPlugin\UiElement\UiElementInterface;
use MonsieurBiz\SyliusRichEditorPlugin\UiElement\UiElementTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ArticlesSelectionUiElement implements UiElementInterface
{
    use UiElementTrait;

    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly LocaleContextInterface $localeContext,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return ArticleInterface[]
     */
    public function getArticles(array $element): array
    {
        // An article in array contains `article` (the ID) and `position` keys
        $articlesArray = $element['articles'] ?? [];

        // List the IDs to retrieve from repository
        $articleIds = array_map(function ($article) {
            return $article['article'];
        }, $articlesArray);

        // Prepare sorting
        usort($articlesArray, function ($articleA, $articleB) {
            return $articleA['position'] <=> $articleB['position'];
        });

        $result = [];
        // Retrieve articles objects
        if (\count($articlesArray) > 0 && \count($articleIds) > 0) {
            $articles = $this->articleRepository->findEnabledAndPublishedByIds(
                $articleIds,
                $this->localeContext->getLocaleCode(),
                ArticleInterface::BLOG_TYPE,
                $this->channelContext->getChannel()
            );
            foreach ($articlesArray as $articleArray) {
                foreach ($articles as $article) {
                    if ($article->getId() === $articleArray['article']) {
                        $result[] = $article;
                    }
                }
            }
        }

        return $result;
    }
}
