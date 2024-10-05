<?php

namespace App\Form;

use App\Entity\Prospect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProspectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('siren', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 9],
            ])
            ->add('nic', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 5],
            ])
            ->add('number', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 10],
            ])
            ->add('street', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('additional_address', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('postal_code', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 5],
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('country', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phone_number', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 10],
            ])
            ->add('phone_number_bis', TextType::class, [
                'attr' => ['class' => 'form-control', 'max' => 10],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prospect::class,
        ]);
    }
}
