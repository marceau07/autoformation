<?php

namespace App\Controller\Admin;

use App\Entity\Survey;
use App\Entity\SurveyTrainee;
use App\Entity\Trainee;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class SurveyTraineeCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return SurveyTrainee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.surveys_trainees.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('survey')->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Survey::class)->findAll()
            ),
            AssociationField::new('trainee')->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainee::class)->findAll()
            ),
            IntegerField::new('rate', new TranslatableMessage('admin.pages.surveys_trainees.columns.rate', domain: $this->getTranslationDomain()))
                ->setCustomOptions([
                    'min' => 0,
                    'max' => 5,
                ]),
            TextEditorField::new('answer', new TranslatableMessage('admin.pages.surveys_trainees.columns.answer', domain: $this->getTranslationDomain())),
        ];
    }
}
