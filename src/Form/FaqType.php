<?php

namespace App\Form;

use App\Entity\Faq;
use App\Entity\Sector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('visibility', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('priority', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('sector', EntityType::class, [
                'class' => Sector::class,
                'choice_label' => 'label',
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
        ]);
    }
}
