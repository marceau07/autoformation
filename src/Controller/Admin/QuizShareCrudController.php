<?php

namespace App\Controller\Admin;

use App\Entity\Quiz;
use App\Entity\QuizShare;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class QuizShareCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return QuizShare::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.quizzes_shared.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('quiz', new TranslatableMessage('admin.pages.quizzes_shared.columns.quiz', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Quiz::class)->findAll()
                ),
            DateTimeField::new('start_date', new TranslatableMessage('admin.pages.quizzes_shared.columns.start_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('finish_date', new TranslatableMessage('admin.pages.quizzes_shared.columns.finish_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy HH:mm'),
            TextField::new('uuid', new TranslatableMessage('admin.pages.quizzes_shared.columns.uuid', domain: $this->getTranslationDomain())),
        ];
    }
}
