<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use MonsieurBiz\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AuthorFixtureFactory extends AbstractExampleFactory
{
    private OptionsResolver $optionsResolver;

    private Generator $faker;

    /**
     * @param FactoryInterface<AuthorInterface> $authorFactory
     */
    public function __construct(
        private FactoryInterface $authorFactory,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): AuthorInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var AuthorInterface $author */
        $author = $this->authorFactory->createNew();
        $author->setName($options['name']);

        return $author;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options) {
                return $this->faker->name;
            })
            ->setAllowedTypes('name', 'string')
        ;
    }
}
