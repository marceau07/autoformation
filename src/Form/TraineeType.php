<?php

namespace App\Form;

use App\Entity\Avatar;
use App\Entity\Cohort;
use App\Entity\Trainee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraineeType extends AbstractType
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
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('activated', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('documents', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('diploma', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cohort', EntityType::class, [
                'class' => Cohort::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'], 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
        ]);
    }
}
