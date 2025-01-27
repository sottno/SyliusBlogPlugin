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

namespace MonsieurBiz\SyliusBlogPlugin\Form\Type\UiElement;

use MonsieurBiz\SyliusBlogPlugin\Form\Type\CaseStudyElementType;
use MonsieurBiz\SyliusRichEditorPlugin\Attribute\AsUiElement;
use MonsieurBiz\SyliusRichEditorPlugin\Attribute\TemplatesUiElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsUiElement(
    code: 'monsieurbiz_blog.case_studies_ui_element',
    icon: 'crosshairs',
    title: 'monsieurbiz_blog.ui_element.case_studies_ui_element.title',
    description: 'monsieurbiz_blog.ui_element.case_studies_ui_element.description',
    uiElement: 'MonsieurBiz\SyliusBlogPlugin\UiElement\CaseStudiesUiElement',
    templates: new TemplatesUiElement(
        adminRender: '@MonsieurBizSyliusBlogPlugin/Admin/UiElement/case_studies.html.twig',
        frontRender: '@MonsieurBizSyliusBlogPlugin/Shop/UiElement/case_studies.html.twig',
    ),
    wireframe: 'case-studies',
    tags: [],
)]
class CaseStudiesUiElementType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'monsieurbiz_blog.ui_element.case_studies_ui_element.fields.title',
                'required' => false,
            ])
            ->add('case_studies', CollectionType::class, [
                'label' => 'monsieurbiz_blog.ui_element.case_studies_ui_element.fields.case_studies',
                'entry_type' => CaseStudyElementType::class,
                'prototype_name' => '__case_study__',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'attr' => [
                    'class' => 'ui segment secondary collection--flex',
                ],
                'constraints' => [
                    new Assert\Count(['min' => 1]),
                    new Assert\Valid(),
                ],
            ])
        ;
    }
}
