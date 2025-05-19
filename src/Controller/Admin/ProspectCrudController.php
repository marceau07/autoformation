<?php

namespace App\Controller\Admin;

use App\Entity\Prospect;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Regex;

#[IsGranted('ROLE_ADMIN')]
class ProspectCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Prospect::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.prospects_internships.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('name', new TranslatableMessage('admin.pages.prospects_internships.columns.name', domain: $this->getTranslationDomain())),
            TextField::new('siren', new TranslatableMessage('admin.pages.prospects_internships.columns.siren', domain: $this->getTranslationDomain())),
            // ->setFormTypeOption('attr', [
            //     'pattern' => '\d{9}',
            //     'minlength' => 9,
            //     'maxlength' => 9,
            // ]),
            TextField::new('nic', new TranslatableMessage('admin.pages.prospects_internships.columns.nic', domain: $this->getTranslationDomain())),
            // ->setFormTypeOption('attr', [
            //     'pattern' => '\d{5}',
            //     'minlength' => 5,
            //     'maxlength' => 5,
            // ]),
            IntegerField::new('number', new TranslatableMessage('admin.pages.prospects_internships.columns.number', domain: $this->getTranslationDomain())),
            TextField::new('street', new TranslatableMessage('admin.pages.prospects_internships.columns.street', domain: $this->getTranslationDomain())),
            TextField::new('postal_code', new TranslatableMessage('admin.pages.prospects_internships.columns.postal_code', domain: $this->getTranslationDomain())),
            // ->setFormTypeOption('attr', [
            //     'pattern' => '\d{5}',
            //     'minlength' => 5,
            //     'maxlength' => 5,
            // ]),
            TextField::new('city', new TranslatableMessage('admin.pages.prospects_internships.columns.city', domain: $this->getTranslationDomain())),
            TextField::new('country', new TranslatableMessage('admin.pages.prospects_internships.columns.country', domain: $this->getTranslationDomain())),
            TextField::new('additional_address', new TranslatableMessage('admin.pages.prospects_internships.columns.additional_address', domain: $this->getTranslationDomain())),
            EmailField::new('email', new TranslatableMessage('admin.pages.prospects_internships.columns.email', domain: $this->getTranslationDomain())),
            // ->setFormTypeOption('attr', [
            //     'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
            //     'minlength' => 5,
            //     'maxlength' => 50,
            // ]),
            TextField::new('phone_number', new TranslatableMessage('admin.pages.prospects_internships.columns.phone_number', domain: $this->getTranslationDomain())),
            // ->setFormTypeOption('attr', [
            //     'pattern' => '\d{10}',
            //     'minlength' => 10,
            //     'maxlength' => 10,
            // ]),
            TextField::new('phone_number_bis', new TranslatableMessage('admin.pages.prospects_internships.columns.phone_number_bis', domain: $this->getTranslationDomain()))
            // ->setFormTypeOption('attr', [
            //     'pattern' => '\d{10}',
            //     'minlength' => 10,
            //     'maxlength' => 10,
            // ]),
        ];
    }
}
