<?php

namespace App\Form;

use App\Entity\CourseModule;
use App\Entity\Quiz;
use App\Entity\QuizTheme;
use App\Entity\Trainer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('theme', EntityType::class, [
                'label' => 'Theme',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'class' => QuizTheme::class,
                'choice_label' => 'name',
            ])
            ->add('trainer', EntityType::class, [
                'label' => 'Trainer',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'class' => Trainer::class,
                'choice_label' => function (Trainer $trainer) {
                    return $trainer->getLastName() . ' ' . $trainer->getFirstName();
                },
            ])
            ->add('module', EntityType::class, [
                'label' => 'Module',
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'class' => CourseModule::class,
                'choice_label' => 'label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
