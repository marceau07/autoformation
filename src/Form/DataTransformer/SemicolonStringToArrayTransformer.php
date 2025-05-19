<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class SemicolonStringToArrayTransformer implements DataTransformerInterface
{
    public function transform($value): array
    {
        if (!$value) {
            return [];
        }

        return array_filter(explode(';', $value), fn($v) => trim($v) !== '');
    }

    public function reverseTransform($value): string
    {
        if (!is_array($value)) {
            return '';
        }

        return implode(';', array_filter($value)) . ';';
    }
}
