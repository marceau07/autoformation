<?php

namespace App\Controller\Admin;

use App\Entity\Faq;
use App\Entity\Sector;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class FaqCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Faq::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.q_and_a.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            AssociationField::new('sector', new TranslatableMessage('admin.pages.q_and_a.columns.sector', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Sector::class)->findAll()
                ),
            TextField::new('theme', new TranslatableMessage('admin.pages.q_and_a.columns.theme', domain: $this->getTranslationDomain())),
            TextField::new('title', new TranslatableMessage('admin.pages.q_and_a.columns.title', domain: $this->getTranslationDomain())),
            TextEditorField::new('content', new TranslatableMessage('admin.pages.q_and_a.columns.content', domain: $this->getTranslationDomain())),
            IntegerField::new('priority', new TranslatableMessage('admin.pages.q_and_a.columns.priority', domain: $this->getTranslationDomain())),
        ];
    }
}
