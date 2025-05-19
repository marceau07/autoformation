<?php

namespace App\Controller\Admin;

use App\Config\CourseResourceType;
use App\Entity\Course;
use App\Entity\CourseResource;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class CourseResourceCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return CourseResource::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.courses_resources.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('course', new TranslatableMessage('admin.pages.courses_resources.columns.course', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Course::class)->findAll()
                ),
            TextField::new('title', new TranslatableMessage('admin.pages.courses_resources.columns.title', domain: $this->getTranslationDomain())),
            TextField::new('resume', new TranslatableMessage('admin.pages.courses_resources.columns.resume', domain: $this->getTranslationDomain())),
            UrlField::new('link', new TranslatableMessage('admin.pages.courses_resources.columns.link', domain: $this->getTranslationDomain())),
            ChoiceField::new('type', new TranslatableMessage('admin.pages.courses_resources.columns.type.label', domain: $this->getTranslationDomain()))
                ->setChoices(array_combine(
                    array_map(fn(CourseResourceType $type) => $this->translator->trans('admin.pages.courses_resources.columns.type.options.' . $type->value, domain: $this->getTranslationDomain()), CourseResourceType::cases()),
                    CourseResourceType::cases()
                ))
                ->setTemplatePath('admin/fields/choice_enum_course_resource.html.twig')
                ->renderExpanded(false),
            TextField::new('record_link', new TranslatableMessage('admin.pages.courses_resources.columns.record_link', domain: $this->getTranslationDomain()))
                ->hideOnForm()
                ->setCustomOption('filename', 'record')
                ->setTemplatePath('admin/fields/record_link.html.twig')
        ];
    }
}
