<?php

namespace App\Services;

use App\DTO\VerificationResult;
use App\Services\Validators\VerificationConditionValidator;
use App\Traits\NestedArrayHelperTrait;

class Verificator
{
    use NestedArrayHelperTrait;

    const RESULT_STATUS_VERIFIED = 'verified';

    public function verify(array $data, array $validators): VerificationResult
    {
        $issuer = $this->findValueInNestedArray($data, 'issuer');
        $issuerName = $issuer['name'] ?? null;

        $conditionResult = null;
        foreach ($validators as $validator) {
            if ($validator instanceof VerificationConditionValidator) {
                $conditionResult = $validator->validate($data);
                if (!$conditionResult->getIsValidated()) {
                    break;
                }
            }
        }

        $verificationResult = new VerificationResult();
        $verificationResult->setIssuer($issuerName);
        $verificationResult->setResult(
            $conditionResult?->getIsValidated()
                ? self::RESULT_STATUS_VERIFIED
                : $conditionResult?->getStatus()
        );

        return $verificationResult;
    }
}
