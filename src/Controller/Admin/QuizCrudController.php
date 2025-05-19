<?php

namespace App\Controller\Admin;

use App\Entity\CourseModule;
use App\Entity\Quiz;
use App\Entity\QuizTheme;
use App\Entity\Trainer;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class QuizCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Quiz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.quizzes.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('theme', new TranslatableMessage('admin.pages.quizzes.columns.theme', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(QuizTheme::class)->findAll()
                )->renderAsHtml()
                ->setTemplatePath('admin/fields/association_html.html.twig'),
            TextField::new('title', new TranslatableMessage('admin.pages.quizzes.columns.title', domain: $this->getTranslationDomain())),
            TextField::new('uuid', new TranslatableMessage('admin.pages.quizzes.columns.uuid', domain: $this->getTranslationDomain())),
            AssociationField::new('trainer', new TranslatableMessage('admin.pages.quizzes.columns.trainer', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainer::class)->findAll()
                ),
            AssociationField::new('module', new TranslatableMessage('admin.pages.quizzes.columns.module', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(CourseModule::class)->findAll()
                ),
        ];
    }
}
