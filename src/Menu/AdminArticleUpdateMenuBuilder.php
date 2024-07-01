<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class AdminArticleUpdateMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private StateMachineFactoryInterface $stateMachineFactory,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $article = $options['article'] ?? null;
        if (!$article instanceof ArticleInterface) {
            return $menu;
        }

        $stateMachine = $this->stateMachineFactory->get($article, ArticleInterface::GRAPH);
        if ($stateMachine->can(ArticleInterface::TRANSITION_PUBLISH)) {
            $menu
                ->addChild('publish', [
                    'route' => 'monsieurbiz_blog_admin_article_update_state',
                    'routeParameters' => [
                        'id' => $article->getId(),
                        'state' => ArticleInterface::TRANSITION_PUBLISH,
                        '_csrf_token' => $this->csrfTokenManager->getToken((string) $article->getId())->getValue(),
                    ],
                ])
                ->setAttribute('type', 'transition')
                ->setLabel('monsieurbiz_blog.ui.publish')
                ->setLabelAttribute('icon', 'check')
                ->setLabelAttribute('color', 'green')
            ;
        }

        return $menu;
    }
}
