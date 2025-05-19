<?php

namespace App\Controller\Admin;

use App\Entity\QuizTheme;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class QuizThemeCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return QuizTheme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.quizzes_themes.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            ImageField::new('illustration', new TranslatableMessage('admin.pages.quizzes_themes.columns.illustration', domain: $this->getTranslationDomain()))
                ->setBasePath('/quizzes-themes')
                ->setUploadDir('public/quizzes-themes')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setTemplatePath('admin/fields/illustration_field.html.twig')
                ->setRequired(false),
            ColorField::new('color', new TranslatableMessage('admin.pages.quizzes_themes.columns.color', domain: $this->getTranslationDomain())),
            TextField::new('name', new TranslatableMessage('admin.pages.quizzes_themes.columns.name', domain: $this->getTranslationDomain())),
        ];
    }
}
