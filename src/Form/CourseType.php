<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseModule;
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
            ->add('title')
            ->add('synopsis')
            ->add('keywords')
            ->add('link')
            ->add('position')
            ->add('visitors')
            ->add('module', EntityType::class, [
                'class' => CourseModule::class,
                'choice_label' => 'id',
            ])
            ->add('trainer', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getLastName() . ' ' . $user->getFirstName();
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
