<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\EventListener;

use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: 'monsieurbiz_blog.article.initialize_update', method: 'initializeUpdateArticle')]
#[AsEventListener(event: 'monsieurbiz_blog.case_study.initialize_update', method: 'initializeUpdateCaseStudy')]
class ArticleListener
{
    public function initializeUpdateArticle(ResourceControllerEvent $event): void
    {
        $article = $event->getSubject();

        if (!$article instanceof ArticleInterface) {
            return;
        }

        if (ArticleInterface::BLOG_TYPE === $article->getType()) {
            return;
        }

        throw new NotFoundHttpException('The article has not been found');
    }

    public function initializeUpdateCaseStudy(ResourceControllerEvent $event): void
    {
        $article = $event->getSubject();

        if (!$article instanceof ArticleInterface) {
            return;
        }

        if (ArticleInterface::CASE_STUDY_TYPE === $article->getType()) {
            return;
        }

        throw new NotFoundHttpException('The article has not been found');
    }
}
