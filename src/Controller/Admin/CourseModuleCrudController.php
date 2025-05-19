<?php

namespace App\Controller\Admin;

use App\Entity\CourseModule;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class CourseModuleCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return CourseModule::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.modules.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('label', new TranslatableMessage('admin.pages.modules.columns.label', domain: $this->getTranslationDomain())),
            IntegerField::new('position', new TranslatableMessage('admin.pages.modules.columns.position', domain: $this->getTranslationDomain())),
            TextField::new('uuid', new TranslatableMessage('admin.pages.modules.columns.uuid', domain: $this->getTranslationDomain())),
            ImageField::new('illustration')
                ->setBasePath('/courses')
                ->setUploadDir('public/courses')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setLabel(new TranslatableMessage('admin.pages.modules.columns.illustration', domain: $this->getTranslationDomain())),
        ];
    }
}
