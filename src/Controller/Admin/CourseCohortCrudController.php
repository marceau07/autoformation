<?php

namespace App\Controller\Admin;

use App\Entity\Cohort;
use App\Entity\Course;
use App\Entity\CourseCohort;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CourseCohortCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CourseCohort::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('course')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Course::class)->findAll()
            ),
            AssociationField::new('cohort')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Cohort::class)->findAll()
            ),
            BooleanField::new('active'),
        ];
    }
}
