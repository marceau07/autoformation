<?php

namespace App\Controller\Admin;

use App\Entity\Trainee;
use App\Entity\Cohort;
use App\Entity\CohortInternship;
use App\Entity\Internship;
use App\Entity\TraineeInternship;
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

#[IsGranted('ROLE_ADMIN')]
class TraineeInternshipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TraineeInternship::class;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Permet d'appeler la méthode stockée dans l'entité plutôt que de se baser que sur le changement de valeur de l'entité
        if($entityInstance->getCohortInternship() instanceof CohortInternship) {
            if($entityInstance->isEvaluation() === true) {
                $entityInstance->setEvaluation(true);
            } elseif($entityInstance->isEvaluation() === false) {
                $entityInstance->setEvaluation(null);
            }
            
            if($entityInstance->isCertificate() === true) {
                $entityInstance->setCertificate(true);
            } elseif($entityInstance->isCertificate() === false) {
                $entityInstance->setCertificate(null);
            } 
            
            if($entityInstance->isAgreement() === true) {
                $entityInstance->setAgreement(true);
            } elseif($entityInstance->isAgreement() === false) {
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
        yield FormField::addColumn(8);
        yield AssociationField::new('cohort_internship')->setQueryBuilder(
            fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(CohortInternship::class)->findAll()
        )
            ->setFormTypeOption('choice_label', 'label');
        yield AssociationField::new('trainee')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Trainee::class)->findAll()
            );
        yield AssociationField::new('internship')->setQueryBuilder(
            fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Internship::class)->findAll()
        )
            ->setFormTypeOption('choice_label', function ($internship) {
                return '(' . $internship->getProspect()->getSiren() . $internship->getProspect()->getNic() . ') ' . $internship->getProspect()->getName() . ' - ' . $internship->getTutorLastName() . ' ' . $internship->getTutorFirstName();
            });
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ agreement pour les pages de détail et d'édition
            yield BooleanField::new('agreement')
                ->addCssClass('non-visible');
            yield TextField::new('send_email_btn')
                ->setLabel("Demande de documents de stage")
                ->hideOnForm()
                ->setTemplatePath('admin/fields/internship.html.twig');
        } else {
            yield BooleanField::new('agreement');
        }
        yield TextField::new('agreement_link')
            ->hideOnForm()
            ->setCustomOption('file', 'agreement')
            ->setTemplatePath('admin/fields/document_link.html.twig');
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ certificate pour les pages de détail et d'édition
            yield BooleanField::new('certificate')
                ->addCssClass('non-visible');
        } else {
            yield BooleanField::new('certificate');
        }
        yield TextField::new('certificate_link')
            ->hideOnForm()
            ->setCustomOption('file', 'certificate')
            ->setTemplatePath('admin/fields/document_link.html.twig');
        if ($pageName == Crud::PAGE_DETAIL || $pageName == Crud::PAGE_EDIT) {
            // On cache le champ evaluation pour les pages de détail et d'édition
            yield BooleanField::new('evaluation')
                ->addCssClass('non-visible');
        } else {
            yield BooleanField::new('evaluation');
        }
        yield TextField::new('evaluation_link')
            ->hideOnForm()
            ->setCustomOption('file', 'evaluation')
            ->setTemplatePath('admin/fields/document_link.html.twig');
    }
}
