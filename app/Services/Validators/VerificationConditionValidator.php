<?php

namespace App\Services\Validators;

use App\DTO\ValidationResult;

interface VerificationConditionValidator
{
    public function validate(array $data): ValidationResult;
}
