<?php

namespace App\Controller\Admin;

use App\Entity\Survey;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class SurveyCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Survey::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.surveys.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('question', new TranslatableMessage('admin.pages.surveys.columns.question', domain: $this->getTranslationDomain())),
            TextField::new('resume', new TranslatableMessage('admin.pages.surveys.columns.resume', domain: $this->getTranslationDomain())),
        ];
    }
}
