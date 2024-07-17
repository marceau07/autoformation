<?php

namespace App\Form;

use App\Entity\Internship;
use App\Entity\Prospect;
use App\Entity\Trainee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trainee', EntityType::class, [
                'class' => Trainee::class,
                'choice_label' => function (Trainee $trainee) {
                    return $trainee->getLastName() . ' ' . $trainee->getFirstName();
                },
            ])
            ->add('prospect', EntityType::class, [
                'class' => Prospect::class,
                'choice_label' => function (Prospect $prospect) {
                    return $prospect->getName() . ' (' . $prospect->getSiren() . ' ' . $prospect->getNic() . ')';
                },
            ])
            ->add('tutorLastName')
            ->add('tutorFirstName')
            ->add('tutorEmail')
            ->add('tutorPhoneNumber')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Internship::class,
        ]);
    }
}
