<?php

namespace App\Form;

use App\Entity\Internship;
use App\Entity\Prospect;
use App\Entity\Trainee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('prospect', EntityType::class, [
                'class' => Prospect::class,
                'choice_label' => function (Prospect $prospect) {
                    return $prospect->getName() . ' (' . $prospect->getSiren() . ' ' . $prospect->getNic() . ')';
                },
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tutorLastName', TextType::class, [
                'label' => 'Nom du tuteur',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tutorFirstName', TextType::class, [
                'label' => 'Prénom du tuteur',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tutorEmail', EmailType::class, [
                'label' => 'Email du tuteur',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tutorPhoneNumber', TextType::class, [
                'label' => 'Téléphone du tuteur',
                'attr' => [
                    'placeholder' => '0123456789',
                    'class' => 'form-control',
                    'pattern' => '0[1-9][0-9]{8}',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Internship::class,
        ]);
    }
}
