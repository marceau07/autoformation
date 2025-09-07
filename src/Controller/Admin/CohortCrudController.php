<?php

namespace App\Controller\Admin;

use App\Entity\Cohort;
use App\Entity\Trainer;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class CohortCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Cohort::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // $export = Action::new('export', new TranslatableMessage('admin.pages.cohorts.export', domain: $this->getTranslationDomain()), 'fa fa-file-excel')
        //     ->linkToRoute('app_cohort_export')
        //     ->createAsGlobalAction();
        $export = Action::new('export', new TranslatableMessage('admin.pages.cohorts.export', domain: $this->getTranslationDomain()), 'fa fa-file-excel')
            ->displayAsLink()
            ->createAsGlobalAction()
            ->linkToUrl($this->generateUrl('app_cohort_export'))
            // ->linkToUrl('#')
            // ->setHtmlAttributes([
                // 'data-bs-toggle' => 'modal',
                // 'data-bs-target' => '#exampleModal',
                // 'href' => '#', // pour éviter la redirection
                // 'onclick' => 'fetchData("' . $this->generateUrl('app_cohort_export_content') . '")', // optionnel, pour charger du contenu dynamique
            // ])
        ;
        $actions->add(Crud::PAGE_INDEX, $export);

        return $actions;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplates([
                'crud/index' => 'admin/crud_index.html.twig',
            ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.cohorts.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('trainer', new TranslatableMessage('admin.pages.cohorts.columns.trainer', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainer::class)->findAll()
                ),
            TextField::new('name', new TranslatableMessage('admin.pages.cohorts.columns.name', domain: $this->getTranslationDomain())),
            TextField::new('acronym', new TranslatableMessage('admin.pages.cohorts.columns.acronym', domain: $this->getTranslationDomain())),
            ImageField::new('shield')
                ->setBasePath('/shields')
                ->setUploadDir('public/shields')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setLabel(new TranslatableMessage('admin.pages.cohorts.columns.shield', domain: $this->getTranslationDomain())),
            DateTimeField::new('start_date', new TranslatableMessage('admin.pages.cohorts.columns.start_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy'),
            DateTimeField::new('finish_date', new TranslatableMessage('admin.pages.cohorts.columns.finish_date', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy'),
            TextField::new('uuid', new TranslatableMessage('admin.pages.cohorts.columns.uuid', domain: $this->getTranslationDomain())),
        ];
    }
}
