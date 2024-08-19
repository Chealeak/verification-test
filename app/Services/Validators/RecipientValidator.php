<?php

namespace App\Services\Validators;

use App\DTO\ValidationResult;
use App\Traits\NestedArrayHelperTrait;

class RecipientValidator implements VerificationConditionValidator
{
    use NestedArrayHelperTrait;

    const RESULT_STATUS_VALID = 'verified';
    const RESULT_STATUS_NOT_VALID = 'invalid_recipient';

    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();

        $recipient = $this->findValueInNestedArray($data, 'recipient');

        $isValidated = false;
        if (is_array($recipient) && isset($recipient['name'], $recipient['email'])) {
            $isValidated = filter_var($recipient['email'], FILTER_VALIDATE_EMAIL);
            $result->setIsValidated($isValidated);
        }

        $result->setStatus($isValidated ? self::RESULT_STATUS_VALID : self::RESULT_STATUS_NOT_VALID);

        return $result;
    }
}
