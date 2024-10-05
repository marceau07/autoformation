<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\QuizShare;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizShareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', null, [
                'widget' => 'single_text',
            ])
            ->add('finish_date', null, [
                'widget' => 'single_text',
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'id',
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
