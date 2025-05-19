<?php

namespace App\Controller\Admin;

use App\Entity\SiteSettings;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class SiteSettingsCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return SiteSettings::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.site_settings.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            BooleanField::new('maintenance_mode', new TranslatableMessage('admin.pages.site_settings.columns.maintenance_mode', domain: $this->getTranslationDomain())),
            ImageField::new('logo_path', new TranslatableMessage('admin.pages.site_settings.columns.logo_path', domain: $this->getTranslationDomain()))
                ->setBasePath('website')
                // Chemin physique où sera uploadé le fichier
                ->setUploadDir('/public/website')
                // Permet par exemple de renommer le fichier de manière unique
                ->setUploadedFileNamePattern('logo.[extension]')
                ->setRequired(false),
            TextField::new('logo_name', new TranslatableMessage('admin.pages.site_settings.columns.logo_name', domain: $this->getTranslationDomain())),
            TextField::new('platform_name', new TranslatableMessage('admin.pages.site_settings.columns.platform_name', domain: $this->getTranslationDomain())),
            ColorField::new('primary_color', new TranslatableMessage('admin.pages.site_settings.columns.primary_color', domain: $this->getTranslationDomain())),
            ColorField::new('secondary_color', new TranslatableMessage('admin.pages.site_settings.columns.secondary_color', domain: $this->getTranslationDomain())),
            ColorField::new('tertiary_color', new TranslatableMessage('admin.pages.site_settings.columns.tertiary_color', domain: $this->getTranslationDomain())),
            ColorField::new('quaternary_color', new TranslatableMessage('admin.pages.site_settings.columns.quaternary_color', domain: $this->getTranslationDomain())),
            ColorField::new('lighten_color', new TranslatableMessage('admin.pages.site_settings.columns.lighten_color', domain: $this->getTranslationDomain())),
            ColorField::new('darken_color', new TranslatableMessage('admin.pages.site_settings.columns.darken_color', domain: $this->getTranslationDomain())),
        ];
    }
}
