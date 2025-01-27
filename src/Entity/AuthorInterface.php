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

namespace MonsieurBiz\SyliusBlogPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface AuthorInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getImage(): ?string;

    public function setImage(?string $image): void;
}
