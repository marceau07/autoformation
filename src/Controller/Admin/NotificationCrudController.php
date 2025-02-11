<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('origin')
            ->setDisabled(true);
        yield TextField::new('message');
        yield UrlField::new('link');
        yield DateField::new('date');
        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices([
                'Devoirs à rendre' => 'homework_to_do',
                'Nouveau message' => 'new_message',
                'Nouveau cours' => 'new_course',
                'Nouveau document de stage' => 'new_internship',
                'Nouvelle version' => 'new_features',
            ]);
        // yield ArrayField::new('user ', 'Utilisateurs');
    }
}
