<?php

namespace App\Form;

use App\Entity\Cohort;
use App\Entity\Trainer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CohortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('acronym')
            ->add('shield')
            ->add('documents')
            ->add('startDate', null, [
                'widget' => 'single_text',
            ])
            ->add('finishDate', null, [
                'widget' => 'single_text',
            ])
            ->add('uuid')
            ->add('trainer', EntityType::class, [
                'class' => Trainer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cohort::class,
        ]);
    }
}
