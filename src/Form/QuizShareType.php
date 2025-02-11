<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\QuizShare;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizShareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('finish_date', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'required' => true,
                'choice_label' => 'title',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizShare::class,
        ]);
    }
}
