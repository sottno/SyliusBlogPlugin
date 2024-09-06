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

final class CaseStudiesUiElement implements UiElementInterface
{
    use UiElementTrait;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LocaleContextInterface $localeContext,
        private ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return ArticleInterface[]
     */
    public function getCaseStudies(array $element): array
    {
        // A case study in array contains `case_study` (the ID) and `position` keys
        $caseStudiesArray = $element['case_studies'] ?? [];

        // List the IDs to retrieve from repository
        $caseStudyIds = array_map(function ($caseStudy) {
            return $caseStudy['case_study'];
        }, $caseStudiesArray);

        // Prepare sorting
        usort($caseStudiesArray, function ($caseStudyA, $caseStudyB) {
            return $caseStudyA['position'] <=> $caseStudyB['position'];
        });

        $result = [];
        // Retrieve case studies objects
        if (\count($caseStudiesArray) > 0 && \count($caseStudyIds) > 0) {
            $caseStudies = $this->articleRepository->findEnabledAndPublishedByIds(
                $caseStudyIds,
                $this->localeContext->getLocaleCode(),
                ArticleInterface::CASE_STUDY_TYPE,
                $this->channelContext->getChannel()
            );
            foreach ($caseStudiesArray as $caseStudyArray) {
                foreach ($caseStudies as $caseStudy) {
                    if ($caseStudy->getId() === $caseStudyArray['case_study']) {
                        $result[] = $caseStudy;
                    }
                }
            }
        }

        return $result;
    }
}
