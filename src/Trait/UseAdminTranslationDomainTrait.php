<?php

namespace App\Trait;

trait UseAdminTranslationDomainTrait
{
    public function getTranslationDomain(): ?string
    {
        return 'admin';
    }
}
