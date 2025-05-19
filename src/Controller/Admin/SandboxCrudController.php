<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\Sandbox;
use App\Entity\User;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Translation\TranslatableMessage;

class SandboxCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Sandbox::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.sandboxes.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('course', new TranslatableMessage('admin.pages.sandboxes.columns.course', domain: $this->getTranslationDomain()))->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Course::class)->findAll()
            ),
            AssociationField::new('author', new TranslatableMessage('admin.pages.sandboxes.columns.author', domain: $this->getTranslationDomain()))->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(User::class)->findAll()
            ),
            TextField::new('title', new TranslatableMessage('admin.pages.sandboxes.columns.title', domain: $this->getTranslationDomain())),
            TextEditorField::new('data', new TranslatableMessage('admin.pages.sandboxes.columns.data', domain: $this->getTranslationDomain())),
            DateTimeField::new('created_at', new TranslatableMessage('admin.pages.sandboxes.columns.created_at', domain: $this->getTranslationDomain()))
                ->setFormat('dd/MM/yyyy HH:mm:ss')
                ->setRequired(false),
            TextField::new('uuid', new TranslatableMessage('admin.pages.sandboxes.columns.uuid', domain: $this->getTranslationDomain())),
            IntegerField::new('slide', new TranslatableMessage('admin.pages.sandboxes.columns.slide', domain: $this->getTranslationDomain()))
                ->setRequired(false)
        ];
    }
}
