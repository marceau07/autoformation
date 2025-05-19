<?php

namespace App\Controller\Admin;

use App\Config\ExportParameterType;
use App\Entity\ExportParameter;
use App\Trait\UseAdminTranslationDomainTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class ExportParameterCrudController extends AbstractCrudController
{
    use UseAdminTranslationDomainTrait;
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return ExportParameter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', new TranslatableMessage('admin.pages.export_parameters.columns.id', domain: $this->getTranslationDomain()))
                ->hideOnForm(),
            ChoiceField::new('dtype', new TranslatableMessage('admin.pages.export_parameters.columns.dtype.label', domain: $this->getTranslationDomain()))
                ->setChoices(array_combine(
                    array_map(fn(ExportParameterType $type) => $this->translator->trans('admin.pages.export_parameters.columns.dtype.options.' . $type->value, domain: $this->getTranslationDomain()), ExportParameterType::cases()),
                    ExportParameterType::cases()
                ))
                ->setTemplatePath('admin/fields/choice_enum_export_parameters.html.twig')
                ->renderExpanded(false),
            // TODO: add a custom to render fields of the selected type
            TextField::new('field', new TranslatableMessage('admin.pages.export_parameters.columns.field', domain: $this->getTranslationDomain())),
        ];
    }
}
