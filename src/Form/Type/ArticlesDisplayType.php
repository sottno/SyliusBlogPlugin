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

namespace MonsieurBiz\SyliusBlogPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticlesDisplayType extends AbstractType
{
    public const MULTIPLE_WITH_IMAGE = 'multiple_with_image';

    public const MULTIPLE_WITHOUT_IMAGE = 'multiple_without_image';

    public const SINGLE = 'single';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('display', ChoiceType::class, [
                'label' => 'monsieurbiz_blog.articles.display.label',
                'required' => true,
                'choices' => [
                    'monsieurbiz_blog.articles.display.choices.multiple_with_image' => self::MULTIPLE_WITH_IMAGE,
                    'monsieurbiz_blog.articles.display.choices.multiple_without_image' => self::MULTIPLE_WITHOUT_IMAGE,
                    'monsieurbiz_blog.articles.display.choices.single' => self::SINGLE,
                ],
            ])
        ;
    }
}
