<?php

namespace App\Controller\Admin;

use App\Entity\CourseResource;
use App\Entity\Trainee;
use App\Entity\TraineeResource;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class TraineeResourceCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return TraineeResource::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.trainees_resources.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('trainee', new TranslatableMessage('admin.pages.trainees_resources.columns.trainee', domain: $this->getTranslationDomain()))->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainee::class)->findAll()
            ),
            AssociationField::new('courseResource', new TranslatableMessage('admin.pages.trainees_resources.columns.course_resource', domain: $this->getTranslationDomain()))->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(CourseResource::class)->findAll()
            ),
            TextField::new('label', new TranslatableMessage('admin.pages.trainees_resources.columns.label', domain: $this->getTranslationDomain())),
        ];
    }
}
