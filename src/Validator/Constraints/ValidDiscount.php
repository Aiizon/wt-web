<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidDiscount extends Constraint
{
    public string $message = 'Le code promotionnel "{{ value }}" n\'est pas valide.';
}