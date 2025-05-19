<?php

namespace App\Controller\Admin;

use App\Entity\Cohort;
use App\Entity\Course;
use App\Entity\CourseCohort;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class CourseCohortCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return CourseCohort::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.courses_shared.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('course', new TranslatableMessage('admin.pages.courses_shared.columns.course', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Course::class)->findAll()
                ),
            AssociationField::new('cohort', new TranslatableMessage('admin.pages.courses_shared.columns.cohort', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Cohort::class)->findAll()
                ),
            BooleanField::new('active', new TranslatableMessage('admin.pages.courses_shared.columns.active', domain: $this->getTranslationDomain())),
        ];
    }
}
