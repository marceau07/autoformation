<?php

namespace App\Form;

use App\Entity\Avatar;
use App\Entity\Cohort;
use App\Entity\Trainee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraineeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('lastName')
            ->add('firstName')
            ->add('roles', TextType::class, [
                'mapped' => false,
            ])
            ->add('password')
            ->add('email')
            ->add('activated')
            ->add('tmpCode')
            ->add('tmpCodeDate', null, [
                'widget' => 'single_text',
            ])
            ->add('signature')
            ->add('uuid')
            ->add('passwordSave')
            ->add('documents')
            ->add('diploma')
            ->add('avatar', EntityType::class, [
                'class' => Avatar::class,
                'choice_label' => 'label',
            ])
            ->add('cohort', EntityType::class, [
                'class' => Cohort::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
        ]);
    }
}
