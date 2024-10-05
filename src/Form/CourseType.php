<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseModule;
use App\Entity\Trainer;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => 'Synopsis',
                'required' => true,
                'attr' => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Mots-clés',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('link', TextType::class, [
                'label' => 'Lien',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('visitors', IntegerType::class, [
                'label' => 'Visiteurs',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('module', EntityType::class, [
                'class' => CourseModule::class,
                'choice_label' => 'label',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('trainer', EntityType::class, [
                'class' => Trainer::class,
                'choice_label' => function (Trainer $trainer) {
                    return $trainer->getLastName() . ' ' . $trainer->getFirstName();
                },
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
