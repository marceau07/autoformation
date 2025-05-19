<?php

namespace App\Controller\Admin;

use App\Config\QuizType;
use App\Entity\Quiz;
use App\Entity\QuizRow;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class QuizRowCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return QuizRow::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.quizzes_rows.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('quiz', new TranslatableMessage('admin.pages.quizzes_rows.columns.quiz', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Quiz::class)->findAll()
                ),
            TextField::new('uuid', new TranslatableMessage('admin.pages.quizzes_rows.columns.uuid', domain: $this->getTranslationDomain())),
            TextField::new('question', new TranslatableMessage('admin.pages.quizzes_rows.columns.question', domain: $this->getTranslationDomain())),
            ChoiceField::new('quiz_type', new TranslatableMessage('admin.pages.quizzes_rows.columns.quiz_type.label', domain: $this->getTranslationDomain()))
                ->setChoices(array_combine(
                    array_map(fn(QuizType $type) => $this->translator->trans('admin.pages.quizzes_rows.columns.quiz_type.options.' . $type->value, domain: $this->getTranslationDomain()), QuizType::cases()),
                    QuizType::cases()
                ))
                ->setTemplatePath('admin/fields/choice_enum_quiz_rows.html.twig')
                ->renderExpanded(false),
            IntegerField::new('timer', new TranslatableMessage('admin.pages.quizzes_rows.columns.timer', domain: $this->getTranslationDomain())),
            IntegerField::new('score', new TranslatableMessage('admin.pages.quizzes_rows.columns.score', domain: $this->getTranslationDomain())),
            TextField::new('hint', new TranslatableMessage('admin.pages.quizzes_rows.columns.hint', domain: $this->getTranslationDomain())),
            TextField::new('answer_explanation', new TranslatableMessage('admin.pages.quizzes_rows.columns.answer_explanation', domain: $this->getTranslationDomain())),
            TextField::new('option1', new TranslatableMessage('admin.pages.quizzes_rows.columns.options.1', domain: $this->getTranslationDomain())),
            TextField::new('option2', new TranslatableMessage('admin.pages.quizzes_rows.columns.options.2', domain: $this->getTranslationDomain())),
            TextField::new('option3', new TranslatableMessage('admin.pages.quizzes_rows.columns.options.3', domain: $this->getTranslationDomain())),
            TextField::new('option4', new TranslatableMessage('admin.pages.quizzes_rows.columns.options.4', domain: $this->getTranslationDomain())),
            TextField::new('answer', new TranslatableMessage('admin.pages.quizzes_rows.columns.answer', domain: $this->getTranslationDomain())),
        ];
    }
}
