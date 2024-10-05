<?php

namespace App\Form;

use App\Entity\QuizTheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuizThemeType extends AbstractType
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
            ->add('illustration', FileType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control', 'accept' => 'image/jpeg, image/png, image/gif, video/mp4'], 
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('color', ColorType::class, [
                'attr' => ['class' => 'form-control form-control-color'], 
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizTheme::class,
        ]);
    }
}
