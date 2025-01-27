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

namespace MonsieurBiz\SyliusBlogPlugin\EventListener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $blogMenu = $menu
            ->addChild('monsieurbiz-blog')
            ->setLabel('monsieurbiz_blog.ui.menu_blog')
        ;

        $blogMenu->addChild('monsieurbiz-blog-tags', ['route' => 'monsieurbiz_blog_admin_tag_index'])
            ->setLabel('monsieurbiz_blog.ui.tags')
            ->setLabelAttribute('icon', 'grid layout')
        ;

        $blogMenu->addChild('monsieurbiz-blog-articles-blog', ['route' => 'monsieurbiz_blog_admin_article_index'])
            ->setLabel('monsieurbiz_blog.ui.articles')
            ->setLabelAttribute('icon', 'newspaper')
        ;

        $blogMenu->addChild('monsieurbiz-blog-articles-case-study', ['route' => 'monsieurbiz_blog_admin_case_study_index'])
            ->setLabel('monsieurbiz_blog.ui.case_studies')
            ->setLabelAttribute('icon', 'crosshairs')
        ;

        $blogMenu->addChild('monsieurbiz-blog-authors', ['route' => 'monsieurbiz_blog_admin_author_index'])
            ->setLabel('monsieurbiz_blog.ui.authors')
            ->setLabelAttribute('icon', 'user')
        ;
    }
}
