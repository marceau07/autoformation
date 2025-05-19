<?php

namespace App\Controller\Admin;

use App\Entity\Internship;
use App\Entity\Prospect;
use App\Entity\Trainee;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class InternshipCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Internship::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.internships.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('trainee', new TranslatableMessage('admin.pages.internships.columns.trainee', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainee::class)->findAll()
                ),
            AssociationField::new('prospect', new TranslatableMessage('admin.pages.internships.columns.prospect', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Prospect::class)->findAll()
                ),
            TextField::new('tutor_last_name', new TranslatableMessage('admin.pages.internships.columns.tutor_last_name', domain: $this->getTranslationDomain())),
            TextField::new('tutor_first_name', new TranslatableMessage('admin.pages.internships.columns.tutor_first_name', domain: $this->getTranslationDomain())),
            EmailField::new('tutor_email', new TranslatableMessage('admin.pages.internships.columns.tutor_email', domain: $this->getTranslationDomain()))
                ->setFormTypeOption('attr', [
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
                    'minlength' => 5,
                    'maxlength' => 50,
                ]),
            TextField::new('tutor_phone_number', new TranslatableMessage('admin.pages.internships.columns.tutor_phone_number', domain: $this->getTranslationDomain()))
                ->setFormTypeOption('attr', [
                    'pattern' => '\d{10}',
                    'minlength' => 10,
                    'maxlength' => 10,
                ]),
        ];
    }
}
