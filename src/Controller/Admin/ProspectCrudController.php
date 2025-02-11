<?php

namespace App\Controller\Admin;

use App\Entity\Prospect;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ProspectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prospect::class;
    }

    // public function configureFields(string $pageName): iterable
    // {
    //     // Return super() to keep the default fields and add a new tab
    //     return [
    //         FormField::addTab('Informations'),
    //         IdField::new('id')->hideOnForm(),
    //         TextField::new('email', 'Email'),

    //         FormField::addTab('Message'),
    //     ];
    // }
}
