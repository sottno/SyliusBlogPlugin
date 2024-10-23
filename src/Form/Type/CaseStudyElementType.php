<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Form\Type;

use MonsieurBiz\SyliusBlogPlugin\Entity\Article;
use MonsieurBiz\SyliusBlogPlugin\Entity\ArticleInterface;
use MonsieurBiz\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints as Assert;

final class CaseStudyElementType extends AbstractType
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('case_study', EntityType::class, [
                'class' => Article::class,
                'label' => 'monsieurbiz_blog.ui_element.case_studies_ui_element.fields.case_study',
                'choice_label' => fn (Article $caseStudy) => $caseStudy->getTitle(),
                'choice_value' => fn (?Article $caseStudy) => $caseStudy?->getId(),
                'required' => true,
                'query_builder' => function (ArticleRepositoryInterface $articleRepository) {
                    return $articleRepository->createShopListQueryBuilderByType(
                        $this->localeContext->getLocaleCode(),
                        ArticleInterface::CASE_STUDY_TYPE,
                        $this->channelContext->getChannel(),
                        null
                    )->orderBy('translation.title');
                },
            ])
            ->add('position', IntegerType::class, [
                'label' => 'monsieurbiz_blog.ui_element.case_studies_ui_element.fields.position',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ],
            ])
        ;

        $builder->get('case_study')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->articleRepository, 'id')),
        );
    }
}
