<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\CourseModule;
use App\Entity\Trainer;
use App\Form\DataTransformer\SemicolonStringToArrayTransformer;
use App\Repository\CourseRepository;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class CourseCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    private CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id', new TranslatableMessage('admin.pages.courses.columns.id', domain: $this->getTranslationDomain()))
            ->hideOnForm();
        yield TextField::new('title', new TranslatableMessage('admin.pages.courses.columns.title', domain: $this->getTranslationDomain()));
        yield TextField::new('synopsis', new TranslatableMessage('admin.pages.courses.columns.synopsis', domain: $this->getTranslationDomain()));
        // TODO: Explode each keyword separated by a ;
        yield TextField::new('keywords', new TranslatableMessage('admin.pages.courses.columns.keywords', domain: $this->getTranslationDomain()));
        // // Récupère les mots-clés existants depuis le repo
        // $allKeywords = $this->courseRepository->findAllUniqueKeywords();
        // yield ChoiceField::new('keywords')
        //     ->setChoices(array_combine($allKeywords, $allKeywords))
        //     ->allowMultipleChoices()
        //     ->setFormTypeOptions([
        //         'mapped' => false,
        //         'required' => false,
        //         'data' => function ($entity) {
        //             // transforme "stage;html;" => ['stage', 'html']
        //             return array_filter(explode(';', rtrim($entity->getKeywords() ?? '', ';')));
        //         },
        //     ])
        //     ->setLabel('Mots-clés');
        yield TextField::new('link', new TranslatableMessage('admin.pages.courses.columns.link', domain: $this->getTranslationDomain()));
        yield IdField::new('position', new TranslatableMessage('admin.pages.courses.columns.position', domain: $this->getTranslationDomain()));
        yield AssociationField::new('module', new TranslatableMessage('admin.pages.courses.columns.module', domain: $this->getTranslationDomain()))
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(CourseModule::class)->findAll()
            );
        yield AssociationField::new('trainer', new TranslatableMessage('admin.pages.courses.columns.trainer', domain: $this->getTranslationDomain()))
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainer::class)->findAll()
            );
        yield IdField::new('visitors', new TranslatableMessage('admin.pages.courses.columns.visitors', domain: $this->getTranslationDomain()));
    }
}
