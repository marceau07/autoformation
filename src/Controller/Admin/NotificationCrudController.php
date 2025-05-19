<?php

namespace App\Controller\Admin;

use App\Config\NotificationCategoryType;
use App\Entity\Notification;
use App\Entity\User;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class NotificationCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return Notification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.notifications.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('origin', new TranslatableMessage('admin.pages.notifications.columns.origin', domain: $this->getTranslationDomain()))
                ->setDisabled(true),
            TextField::new('message', new TranslatableMessage('admin.pages.notifications.columns.message', domain: $this->getTranslationDomain())),
            UrlField::new('link', new TranslatableMessage('admin.pages.notifications.columns.link', domain: $this->getTranslationDomain())),
            DateTimeField::new('date', new TranslatableMessage('admin.pages.notifications.columns.date', domain: $this->getTranslationDomain())),
            ChoiceField::new('category', new TranslatableMessage('admin.pages.notifications.columns.category.label', domain: $this->getTranslationDomain()))
                ->setChoices(array_combine(
                    array_map(fn(NotificationCategoryType $type) => $this->translator->trans('admin.pages.notifications.columns.category.options.' . $type->value, domain: $this->getTranslationDomain()), NotificationCategoryType::cases()),
                    NotificationCategoryType::cases()
                ))
                ->setTemplatePath('admin/fields/choice_enum_notification.html.twig')
                ->renderExpanded(false),
            AssociationField::new('user', new TranslatableMessage('admin.pages.notifications.columns.user', domain: $this->getTranslationDomain()))
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(User::class)->findAll()
                )
        ];
    }
}
