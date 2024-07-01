<?php

/*
 * This file is part of Monsieur Biz's Blog plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusBlogPlugin\Form\Type;

use MonsieurBiz\SyliusRichEditorPlugin\Form\Type\RichEditorType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTranslationType extends AbstractResourceType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'monsieurbiz_blog.form.article.title',
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'monsieurbiz_blog.form.article.slug',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'monsieurbiz_blog.form.article.description',
            ])
            ->add('content', RichEditorType::class, [
                'label' => 'monsieurbiz_blog.form.article.content',
            ])
        ;
    }
}
