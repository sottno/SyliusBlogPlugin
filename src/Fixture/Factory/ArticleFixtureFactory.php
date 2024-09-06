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
use DateTime;
use DateTimeInterface;
use Faker\Factory;
use Faker\Generator;
use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleTranslationInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\AuthorInterface;
use MonsieurBiz\SyliusBlogPlugin\Entity\TagInterface;
use MonsieurBiz\SyliusBlogPlugin\Repository\TagRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadCurrentFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArticleFixtureFactory extends AbstractExampleFactory
{
    private OptionsResolver $optionsResolver;

    private OptionsResolver $translationOptionsResolver;

    private Generator $faker;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     *
     * @param FactoryInterface<ArticleInterface> $articleFactory
     * @param FactoryInterface<ArticleTranslationInterface> $articleTranslationFactory
     * @param TagRepositoryInterface<TagInterface> $tagRepository
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param RepositoryInterface<AuthorInterface> $authorRepository
     */
    public function __construct(
        private FactoryInterface $articleFactory,
        private FactoryInterface $articleTranslationFactory,
        private TagRepositoryInterface $tagRepository,
        private StateMachineFactoryInterface $stateMachineFactory,
        private RepositoryInterface $localeRepository,
        private ChannelRepositoryInterface $channelRepository,
        private RepositoryInterface $authorRepository,
        private FileLocatorInterface $fileLocator,
        private FileHelperInterface $fileHelper,
        private string $defaultLocaleCode,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);

        $this->translationOptionsResolver = new OptionsResolver();
        $this->configureTranslationOptions($this->translationOptionsResolver);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create(array $options = []): ArticleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ArticleInterface $article */
        $article = $this->articleFactory->createNew();
        $article->setEnabled($options['enabled']);
        $article->setType($options['type']);
        foreach ($options['tags'] as $tag) {
            $article->addTag($tag);
        }
        $article->setImage($options['image']);
        $article->setVideo($options['video']);
        $channels = $this->channelRepository->findAll();
        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $article->addChannel($channel);
        }
        $this->addAuthors($article, $options);
        $this->createTranslations($article, $options);

        if ($options['is_published']) {
            $this->applyTransition($article, ArticleInterface::TRANSITION_PUBLISH);
        }
        if (ArticleInterface::STATE_PUBLISHED === $article->getState() && null !== $options['publish_date']) {
            $article->setPublishedAt($options['publish_date']);
        }

        return $article;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('enabled', function (Options $options): bool {
                return $this->faker->boolean(80);
            })

            ->setDefault('type', ArticleInterface::BLOG_TYPE)
            ->setAllowedTypes('type', ['string'])

            ->setDefault('image', $this->lazyImageDefault(80))
            ->setAllowedTypes('image', ['string', 'null'])
            ->setNormalizer('image', function (Options $options, $previousValue): ?string {
                return $this->getFilePath($previousValue, 'images');
            })

            ->setDefault('video', null)
            ->setAllowedTypes('video', ['string', 'null'])
            ->setNormalizer('video', function (Options $options, $previousValue): ?string {
                return $this->getFilePath($previousValue, 'videos');
            })

            ->setDefault('thumbnailImage', $this->lazyImageDefault(10))
            ->setAllowedTypes('thumbnailImage', ['string', 'null'])
            ->setNormalizer('thumbnailImage', function (Options $options, $previousValue): ?string {
                return $this->getFilePath($previousValue, 'images');
            })

            ->setDefault('tags', LazyOption::randomOnes($this->tagRepository, 2))
            ->setAllowedTypes('tags', ['array'])
            ->setNormalizer('tags', function (Options $options, $previousValue): array {
                if (null === $previousValue || 0 === \count($previousValue)) {
                    return [];
                }

                $result = [];
                foreach ($previousValue as $tag) {
                    if (!\is_object($tag)) {
                        $tag = $this->tagRepository->findOneByName($tag, $this->defaultLocaleCode);
                    }
                    if (null !== $tag) {
                        $result[] = $tag;
                    }
                }

                return $result;
            })

            ->setDefault('authors', LazyOption::randomOnes($this->authorRepository, 2))
            ->setAllowedTypes('authors', ['array'])
            ->setNormalizer('authors', function (Options $options, $previousValue): array {
                if (null === $previousValue || 0 === \count($previousValue)) {
                    return [];
                }

                $result = [];
                foreach ($previousValue as $author) {
                    if (!\is_object($author)) {
                        $author = $this->authorRepository->findOneBy(['name' => $author]);
                    }
                    if (null !== $author) {
                        $result[] = $author;
                    }
                }

                return $result;
            })

            ->setDefault('translations', [])
            ->setAllowedTypes('translations', ['array'])

            ->setDefault('is_published', fn (Options $options): bool => $this->faker->boolean(80))
            ->setAllowedTypes('is_published', ['bool'])

            ->setDefault('publish_date', fn (Options $options): DateTimeInterface => $this->faker->dateTimeBetween('-1 years', 'now'))
            ->setAllowedTypes('publish_date', ['null', 'string', DateTime::class])
            ->setNormalizer('publish_date', function (Options $options, $previousValue): DateTime {
                if (\is_string($previousValue)) {
                    return new DateTime($previousValue);
                }

                return $previousValue;
            })
        ;
    }

    private function addAuthors(ArticleInterface $article, array $options): void
    {
        foreach ($options['authors'] as $author) {
            if (null !== $author) {
                $article->addAuthor($author);
            }
        }
    }

    private function createTranslations(ArticleInterface $article, array $options): void
    {
        // add translation for each defined locales
        foreach ($this->getLocales() as $localeCode) {
            $translation = $options['translations'][$localeCode] ?? [];
            $translation = $this->translationOptionsResolver->resolve($translation);
            /** @var ArticleTranslationInterface $articleTranslation */
            $articleTranslation = $this->articleTranslationFactory->createNew();
            $articleTranslation->setLocale($localeCode);
            $articleTranslation->setTitle($translation['title']);
            $articleTranslation->setSlug($translation['slug'] ?? StringInflector::nameToCode($translation['title']));
            $articleTranslation->setDescription($translation['description']);
            $articleTranslation->setContent($translation['content']);

            $article->addTranslation($articleTranslation);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function configureTranslationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('title', fn (Options $options): string => /** @phpstan-ignore-line */ $this->faker->words(3, true))
            ->setDefault('slug', null)
            ->setDefault('description', fn (Options $options): string => $this->faker->paragraph)
            ->setDefault('content', fn (Options $options): string => $this->faker->paragraph)
        ;
    }

    private function applyTransition(ArticleInterface $article, string $transition): void
    {
        $this->stateMachineFactory->get($article, ArticleInterface::GRAPH)->apply($transition);
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
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

            return sprintf('@MonsieurBizSyliusBlogPlugin/Resources/fixtures/article-%d.jpg', $random);
        };
    }

    private function getFilePath(?string $imagePath, string $folder): ?string
    {
        if (null === $imagePath) {
            return null;
        }

        $sourcePath = $this->fileLocator->locate($imagePath);
        $existingImage = $this->findExistingFile(basename($sourcePath), $folder);
        if (null !== $existingImage) {
            return $existingImage;
        }

        $file = new UploadedFile($sourcePath, basename($sourcePath));
        $filename = $this->fileHelper->upload($file, 'blog', 'gallery/' . $folder);

        return 'gallery/' . $folder . '/blog/' . $filename;
    }

    private function findExistingFile(string $filename, string $folder): ?string
    {
        try {
            $files = $this->fileHelper->list('blog', 'gallery/' . $folder);
        } catch (CannotReadCurrentFolderException) {
            $this->fileHelper->createFolder('blog', '', 'gallery/' . $folder); // Create the folder if it does not exist
            $files = [];
        }

        /** @var File $file */
        foreach ($files as $file) {
            if ($filename === $file->getName()) {
                return 'gallery/' . $folder . '/' . $file->getPath();
            }
        }

        return null;
    }
}
