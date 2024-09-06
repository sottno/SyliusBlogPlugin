<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface TagInterface extends ResourceInterface, ToggleableInterface, TranslatableInterface, SlugAwareInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    public function addArticle(ArticleInterface $article): void;

    public function removeArticle(ArticleInterface $article): void;

    public function hasArticle(ArticleInterface $article): bool;

    public function getArticles(): Collection;
}
