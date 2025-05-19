<?php

namespace App\Controller\Admin;

use App\Entity\Cohort;
use App\Entity\CohortInternship;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class CohortInternshipCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return CohortInternship::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.cohorts_internships.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('label', new TranslatableMessage('admin.pages.cohorts_internships.columns.label', domain: $this->getTranslationDomain())),
            DateField::new('start_date', new TranslatableMessage('admin.pages.cohorts_internships.columns.start_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy'),
            DateField::new('finish_date', new TranslatableMessage('admin.pages.cohorts_internships.columns.finish_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy'),
            IntegerField::new('duration', new TranslatableMessage('admin.pages.cohorts_internships.columns.duration', domain: $this->getTranslationDomain())),
            AssociationField::new('cohort', new TranslatableMessage('admin.pages.cohorts_internships.columns.cohort', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Cohort::class)->findAll()
                ),
            TextField::new('uuid', new TranslatableMessage('admin.pages.cohorts_internships.columns.uuid', domain: $this->getTranslationDomain())),
        ];
    }
}
