<?php

namespace App\Controller\Admin;

use App\Entity\Sector;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class SectorCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Sector::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.sectors.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('label', new TranslatableMessage('admin.pages.sectors.columns.label', domain: $this->getTranslationDomain())),
            ImageField::new('logo', new TranslatableMessage('admin.pages.sectors.columns.logo', domain: $this->getTranslationDomain()))
                ->setBasePath('sectors')
                ->setUploadDir('public/sectors')
                ->setUploadedFileNamePattern('[name].[extension]')
                ->setRequired(true)
        ];
    }
}
