<?php

namespace App\Form;

use App\Entity\Avatar;
use App\Entity\Sector;
use App\Entity\Trainer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainerType extends AbstractType
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
            ->add('role')
            ->add('phoneNumber')
            ->add('entranceCode')
            ->add('entranceCodeDate', null, [
                'widget' => 'single_text',
            ])
            ->add('avatar', EntityType::class, [
                'class' => Avatar::class,
                'choice_label' => 'id',
            ])
            ->add('sector', EntityType::class, [
                'class' => Sector::class,
                'choice_label' => 'id',
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
