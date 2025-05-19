<?php

namespace App\Controller\Admin;

use App\Entity\QuizRow;
use App\Entity\User;
use App\Entity\UserQuiz;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class UserQuizCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return UserQuiz::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.quizzes_users.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('user', new TranslatableMessage('admin.pages.quizzes_users.columns.user', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(User::class)->findAll()
                ),
            AssociationField::new('quiz_row', new TranslatableMessage('admin.pages.quizzes_users.columns.quiz_row', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(QuizRow::class)->findAll()
                ),
            TextField::new('quiz_row.answer', new TranslatableMessage('admin.pages.quizzes_users.columns.real_answer', domain: $this->getTranslationDomain()))
                ->setFormTypeOption('disabled', true),
            TextField::new('answer', new TranslatableMessage('admin.pages.quizzes_users.columns.answer', domain: $this->getTranslationDomain())),
            DateTimeField::new('finish_date', new TranslatableMessage('admin.pages.quizzes_users.columns.finish_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy HH:mm:ss'),
        ];
    }
}
