<?php

namespace App\Form;

use App\Config\QuizType;
use App\Entity\Quiz;
use App\Entity\QuizRow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => 'Question',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('answer1', TextType::class, [
                'label' => 'Answer 1',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice unique_choice multiple_choice'],
            ])
            ->add('answer2', TextType::class, [
                'label' => 'Answer 2',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice unique_choice multiple_choice'],
            ])
            ->add('answer3', TextType::class, [
                'label' => 'Answer 3',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice unique_choice multiple_choice'],
            ])
            ->add('answer4', TextType::class, [
                'label' => 'Answer 4',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice unique_choice multiple_choice'],
            ])
            ->add('answer_short_text', TextType::class, [
                'label' => 'Answer Short Text',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice short_answer'],
            ])
            ->add('answer_long_text', TextType::class, [
                'label' => 'Answer Long Text',
                'required' => false,
                'attr' => ['class' => 'form-control optionnal optionnal_choice long_answer'],
            ])
            ->add('quiz_type', EnumType::class, [
                'label' => 'Quiz Type',
                'required' => true,
                'class' => QuizType::class,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('timer', IntegerType::class, [
                'label' => 'Timer',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('score', IntegerType::class, [
                'label' => 'Score',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('hint', TextType::class, [
                'label' => 'Hint',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('answer_explanation', TextType::class, [
                'label' => 'Answer Explanation',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'title',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizRow::class,
        ]);
    }
}
