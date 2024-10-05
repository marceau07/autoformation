<?php

namespace App\Form;

use App\Entity\Sector;
use App\Entity\Trainer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control',
                ],
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('activated', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('role', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phoneNumber', TextType::class, [
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
            'data_class' => Trainer::class,
        ]);
    }
}
