<?php

namespace App\Controller\Admin;

use App\Entity\QuizTheme;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class QuizThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuizTheme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('illustration', 'Illustration du thème')
            ->setBasePath('quiz-themes')
            ->setUploadDir('public/quiz-themes')
            ->setUploadedFileNamePattern('[name].[extension]')
            ->setRequired(false);
        yield ColorField::new('color', "Couleure du thème");
        yield TextField::new('name', "Nom du thème");
    }
}
