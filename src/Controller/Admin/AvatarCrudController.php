<?php

namespace App\Controller\Admin;

use App\Entity\Avatar;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class AvatarCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Avatar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.avatars.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('label', new TranslatableMessage('admin.pages.avatars.columns.label', domain: $this->getTranslationDomain())),
            ImageField::new('link', new TranslatableMessage('admin.pages.avatars.columns.link', domain: $this->getTranslationDomain()))
                ->setBasePath('avatars')
                ->setUploadDir('public/avatars')
                ->setUploadedFileNamePattern('[name].[extension]')
                ->setRequired(true),
        ];
    }
}
