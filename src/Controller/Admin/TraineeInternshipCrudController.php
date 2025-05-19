<?php

namespace App\Controller\Admin;

use App\Entity\Trainee;
use App\Entity\Cohort;
use App\Entity\CohortInternship;
use App\Entity\Internship;
use App\Entity\TraineeInternship;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class TraineeInternshipCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return TraineeInternship::class;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Permet d'appeler la méthode stockée dans l'entité plutôt que de se baser que sur le changement de valeur de l'entité
        if ($entityInstance->getCohortInternship() instanceof CohortInternship) {
            if ($entityInstance->isEvaluation() === true) {
                $entityInstance->setEvaluation(true);
            } elseif ($entityInstance->isEvaluation() === false) {
                $entityInstance->setEvaluation(null);
            }

            if ($entityInstance->isCertificate() === true) {
                $entityInstance->setCertificate(true);
            } elseif ($entityInstance->isCertificate() === false) {
                $entityInstance->setCertificate(null);
            }

            if ($entityInstance->isAgreement() === true) {
                $entityInstance->setAgreement(true);
            } elseif ($entityInstance->isAgreement() === false) {
                $entityInstance->setAgreement(null);
            }

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        // $customButton = Action::new('customAction', 'Mon Bouton')->linkToRoute('app_home'); // false pour le rendre spécifique à la vue détail

        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
        // ->add(Crud::PAGE_DETAIL, $customButton);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', new TranslatableMessage('admin.pages.trainees_internships.columns.id', domain: $this->getTranslationDomain()))
            ->hideOnForm();
        yield AssociationField::new('internship', new TranslatableMessage('admin.pages.trainees_internships.columns.internship', domain: $this->getTranslationDomain()))
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Internship::class)->findAll()
            );
        yield AssociationField::new('trainee', new TranslatableMessage('admin.pages.trainees_internships.columns.trainee', domain: $this->getTranslationDomain()))
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainee::class)->findAll()
            );
        yield AssociationField::new('cohort_internship', new TranslatableMessage('admin.pages.trainees_internships.columns.cohort_internship', domain: $this->getTranslationDomain()))
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(CohortInternship::class)->findAll()
            );
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ agreement pour les pages de détail et d'édition
            yield BooleanField::new('agreement', new TranslatableMessage('admin.pages.trainees_internships.columns.agreement', domain: $this->getTranslationDomain()))
                ->addCssClass('non-visible');
            yield TextField::new('send_email_btn')
                ->setLabel("Demande de documents de stage")
                ->hideOnForm()
                ->setTemplatePath('admin/fields/internship.html.twig');
        } else {
            yield BooleanField::new('agreement', new TranslatableMessage('admin.pages.trainees_internships.columns.agreement', domain: $this->getTranslationDomain()));
        }
        yield TextField::new('agreement_link', new TranslatableMessage('admin.pages.trainees_internships.columns.agreement_link', domain: $this->getTranslationDomain()))
            ->hideOnForm()
            ->setCustomOption('file', 'agreement')
            ->setTemplatePath('admin/fields/document_link.html.twig');
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ certificate pour les pages de détail et d'édition
            yield BooleanField::new('certificate', new TranslatableMessage('admin.pages.trainees_internships.columns.certificate', domain: $this->getTranslationDomain()))
                ->addCssClass('non-visible');
        } else {
            yield BooleanField::new('certificate', new TranslatableMessage('admin.pages.trainees_internships.columns.certificate', domain: $this->getTranslationDomain()));
        }
        yield TextField::new('certificate_link', new TranslatableMessage('admin.pages.trainees_internships.columns.certificate_link', domain: $this->getTranslationDomain()))
            ->hideOnForm()
            ->setCustomOption('file', 'certificate')
            ->setTemplatePath('admin/fields/document_link.html.twig');
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ evaluation pour les pages de détail et d'édition
            yield BooleanField::new('evaluation', new TranslatableMessage('admin.pages.trainees_internships.columns.evaluation', domain: $this->getTranslationDomain()))
                ->addCssClass('non-visible');
        } else {
            yield BooleanField::new('evaluation', new TranslatableMessage('admin.pages.trainees_internships.columns.evaluation', domain: $this->getTranslationDomain()));
        }
        yield TextField::new('evaluation_link', new TranslatableMessage('admin.pages.trainees_internships.columns.evaluation_link', domain: $this->getTranslationDomain()))
            ->hideOnForm()
            ->setCustomOption('file', 'evaluation')
            ->setTemplatePath('admin/fields/document_link.html.twig');
    }
}
