<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Fixture\Factory;

use Closure;
use Faker\Factory;
use Faker\Generator;
use MonsieurBiz\SyliusBlogPlugin\Entity\AuthorInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadCurrentFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        private FileLocatorInterface $fileLocator,
        private FileHelperInterface $fileHelper,
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
        $author->setImage($options['image']);

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

            ->setDefault('image', $this->lazyImageDefault(80))
            ->setAllowedTypes('image', ['string', 'null'])
            ->setNormalizer('image', function (Options $options, $previousValue): ?string {
                return $this->getImagePath($previousValue);
            })
        ;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function lazyImageDefault(int $chanceOfRandomOne): Closure
    {
        return function (Options $options) use ($chanceOfRandomOne): ?string {
            if (random_int(1, 100) > $chanceOfRandomOne) {
                return null;
            }

            $random = random_int(1, 5);

            return sprintf('@MonsieurBizSyliusBlogPlugin/Resources/fixtures/author-%d.png', $random);
        };
    }

    private function getImagePath(?string $imagePath): ?string
    {
        if (null === $imagePath) {
            return null;
        }

        $sourcePath = $this->fileLocator->locate($imagePath);
        $existingImage = $this->findExistingImage(basename($sourcePath));
        if (null !== $existingImage) {
            return $existingImage;
        }

        $file = new UploadedFile($sourcePath, basename($sourcePath));
        $filename = $this->fileHelper->upload($file, 'author', 'gallery/images');

        return 'gallery/images/author/' . $filename;
    }

    private function findExistingImage(string $filename): ?string
    {
        try {
            $files = $this->fileHelper->list('author', 'gallery/images');
        } catch (CannotReadCurrentFolderException) {
            $this->fileHelper->createFolder('author', '', 'gallery/images'); // Create the folder if it does not exist
            $files = [];
        }

        /** @var File $file */
        foreach ($files as $file) {
            if ($filename === $file->getName()) {
                return 'gallery/images/' . $file->getPath();
            }
        }

        return null;
    }
}
