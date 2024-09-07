<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseModule;
use App\Entity\Trainer;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [])
            ->add('synopsis')
            ->add('keywords')
            ->add('link')
            ->add('position')
            ->add('visitors')
            ->add('module', EntityType::class, [
                'class' => CourseModule::class,
                'choice_label' => 'label',
            ])
            ->add('trainer', EntityType::class, [
                'class' => Trainer::class,
                'choice_label' => function (Trainer $trainer) {
                    return $trainer->getLastName() . ' ' . $trainer->getFirstName();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
