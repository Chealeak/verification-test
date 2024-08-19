<?php

namespace App\Services\Validators;

use App\DTO\ValidationResult;
use App\Traits\NestedArrayHelperTrait;

class SignatureValidator implements VerificationConditionValidator
{
    use NestedArrayHelperTrait;

    const RESULT_STATUS_VALID = 'verified';
    const RESULT_STATUS_NOT_VALID = 'invalid_signature';

    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();

        $signature = $this->findValueInNestedArray($data, 'signature');
        $mainData = $this->findValueInNestedArray($data, 'data');
        $mainDataFlattened = $this->flattenArray($mainData);
        $mainDataHashed = $this->hashData($mainDataFlattened);
        sort($mainDataHashed);
        $mainDataResultHash = hash('sha256', json_encode($mainDataHashed));

        $isValidated = isset($signature['targetHash']) && ($signature['targetHash'] === $mainDataResultHash);

        $result->setIsValidated($isValidated);
        $result->setStatus($isValidated ? self::RESULT_STATUS_VALID : self::RESULT_STATUS_NOT_VALID);

        return $result;
    }

    private function hashData(array $data): array
    {
        $hashedData = [];
        foreach($data as $key => $value) {
            $hashedData[] = hash('sha256', '{"' . $key . '":"' . $value . '"}');
        }

        return $hashedData;
    }
}
