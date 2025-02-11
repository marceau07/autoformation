<?php

namespace App\Controller\Admin;

use App\Config\QuizType;
use App\Entity\QuizRow;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class QuizRowCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuizRow::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
            ChoiceField::new('quiz_type')
                ->setLabel('Quiz Type')
                ->setChoices([
                    'default' => QuizType::DEFAULT,
                    'long_answer' => QuizType::LONG_ANSWER,
                    'short_answer' => QuizType::SHORT_ANSWER,
                    'unique_choice' => QuizType::UNIQUE_CHOICE,
                ])
                ->renderExpanded(false)
        ];
    }
}
