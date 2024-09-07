<?php

namespace App\Form;

use App\Entity\Cohort;
use App\Entity\Internship;
use App\Entity\Trainer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CohortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'], 
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('acronym', TextType::class, [
                'attr' => ['class' => 'form-control'], 
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('shield', FileType::class, [
                'attr' => ['class' => 'form-control'], 
                'mapped' => false,
                'required' => false,
            ])
            ->add('documents_label', TextType::class, [
                'attr' => ['class' => 'form-control'], 
                'mapped' => false,
                'required' => false,
            ])
            ->add('documents_start_date', DateType::class, [
                'attr' => ['class' => 'form-control'], 
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('documents_finish_date', DateType::class, [
                'attr' => ['class' => 'form-control'], 
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('documents_duration', IntegerType::class, [
                'attr' => ['class' => 'form-control'], 
                'mapped' => false,
                'required' => false,
            ])
            ->add('startDate', DateType::class, [
                'attr' => ['class' => 'form-control'], 
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('finishDate', DateType::class, [
                'attr' => ['class' => 'form-control'], 
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('trainer', EntityType::class, [
                'attr' => ['class' => 'form-control'], 
                'class' => Trainer::class,
                'choice_label' => function (Trainer $trainer) {
                    return $trainer->getLastName() . ' ' . $trainer->getFirstName();
                },
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cohort::class,
        ]);
    }
}
