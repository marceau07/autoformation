<?php

namespace App\Controller\Admin;

use App\Entity\Avatar;
use App\Entity\Cohort;
use App\Entity\Trainee;
use App\Trait\UseAdminTranslationDomainTrait;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class TraineeCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;

    public static function getEntityFqcn(): string
    {
        return Trainee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.users.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            TextField::new('username', new TranslatableMessage('admin.pages.users.columns.username', domain: $this->getTranslationDomain())),
            ArrayField::new('roles', new TranslatableMessage('admin.pages.users.columns.roles', domain: $this->getTranslationDomain())),
            TextField::new('password', new TranslatableMessage('admin.pages.users.columns.password', domain: $this->getTranslationDomain())),
            TextField::new('password_save', new TranslatableMessage('admin.pages.trainees.columns.password_save', domain: $this->getTranslationDomain())),
            TextField::new('last_name', new TranslatableMessage('admin.pages.users.columns.last_name', domain: $this->getTranslationDomain())),
            TextField::new('first_name', new TranslatableMessage('admin.pages.users.columns.first_name', domain: $this->getTranslationDomain())),
            TextField::new('email', new TranslatableMessage('admin.pages.users.columns.email', domain: $this->getTranslationDomain())),
            BooleanField::new('activated', new TranslatableMessage('admin.pages.users.columns.activated', domain: $this->getTranslationDomain())),
            IntegerField::new('tmp_code', new TranslatableMessage('admin.pages.users.columns.tmp_code', domain: $this->getTranslationDomain())),
            DateTimeField::new('tmp_code_date', new TranslatableMessage('admin.pages.users.columns.tmp_code_date', domain: $this->getTranslationDomain())),
            ImageField::new('avatar.link')
                ->setBasePath('/avatars')
                ->setUploadDir('public/avatars')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->setLabel(new TranslatableMessage('admin.pages.users.columns.avatar', domain: $this->getTranslationDomain())),
            TextField::new('signature', new TranslatableMessage('admin.pages.users.columns.signature', domain: $this->getTranslationDomain()))
                ->formatValue(function ($value) {
                    if (!$value) return null;
                    return sprintf('<img src="%s" class="img-fluid" style="max-height:150px;">', $value);
                })
                ->setRequired(false),
            AssociationField::new('cohort')->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Cohort::class)->findAll()
            )
                ->setFormTypeOption('choice_label', 'name'),
            ChoiceField::new('diploma', new TranslatableMessage('admin.pages.trainees.columns.diploma', domain: $this->getTranslationDomain()))
                ->setTranslatableChoices([
                    'null' => new TranslatableMessage('admin.choices.diploma.null', domain: $this->getTranslationDomain()),
                    0 => new TranslatableMessage('admin.choices.diploma.no_diploma', domain: $this->getTranslationDomain()),
                    -1 => new TranslatableMessage('admin.choices.diploma.partial_1', domain: $this->getTranslationDomain()),
                    -2 => new TranslatableMessage('admin.choices.diploma.partial_2', domain: $this->getTranslationDomain()),
                    -3 => new TranslatableMessage('admin.choices.diploma.partial_3', domain: $this->getTranslationDomain()),
                    -4 => new TranslatableMessage('admin.choices.diploma.didnt_show_up', domain: $this->getTranslationDomain()),
                    1 => new TranslatableMessage('admin.choices.diploma.graduated', domain: $this->getTranslationDomain())
                ]),
            TextField::new('tutorial_completed', new TranslatableMessage('admin.pages.trainees.columns.tutorial_completed', domain: $this->getTranslationDomain()))
        ];
    }
}
