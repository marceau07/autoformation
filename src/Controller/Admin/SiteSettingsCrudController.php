<?php

namespace App\Controller\Admin;

use App\Entity\SiteSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SiteSettingsCrudController extends AbstractCrudController
{
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
            BooleanField::new('maintenance_mode', 'Mode maintenance'),
            ImageField::new('logo_path', 'Logo')
                ->setBasePath('website')
                // Chemin physique où sera uploadé le fichier
                ->setUploadDir('/public/website')
                // Permet par exemple de renommer le fichier de manière unique
                ->setUploadedFileNamePattern('logo.[extension]')
                ->setRequired(false),
            TextField::new('logo_name', "Nom du logo"),
            TextField::new('platform_name', "Nom de la plateforme"),
            ColorField::new('primary_color', "Couleure principale"),
            ColorField::new('secondary_color', "Couleure secondaire"),
            ColorField::new('tertiary_color', "Couleure tertiaire"),
            ColorField::new('quaternary_color', "Couleure quaternaire"),
            ColorField::new('lighten_color', "Couleure claire"),
            ColorField::new('darken_color', "Couleure foncée"),
        ];
    }
}
