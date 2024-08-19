<?php

namespace App\Services\Validators;

use App\DTO\ValidationResult;
use App\Traits\NestedArrayHelperTrait;

class IssuerValidator implements VerificationConditionValidator
{
    use NestedArrayHelperTrait;

    const RESULT_STATUS_VALID = 'verified';
    const RESULT_STATUS_NOT_VALID = 'invalid_issuer';
    const DNS_API_URL_PART = 'https://dns.google/resolve?type=TXT';

    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();

        $issuer = $this->findValueInNestedArray($data, 'issuer');

        $isValidated = false;
        if (is_array($issuer) && isset($issuer['name'], $issuer['identityProof'])) {
            $identityProofKey = $issuer['identityProof']['key'] ?? null;
            $identityProofLocation = $issuer['identityProof']['location'] ?? null;
            if ($identityProofKey && $identityProofLocation) {
                $isValidated = $this->checkIdentityProof($identityProofKey, $identityProofLocation);
            }
            $result->setIsValidated($isValidated);
        }

        $result->setStatus($isValidated ? self::RESULT_STATUS_VALID : self::RESULT_STATUS_NOT_VALID);

        return $result;
    }

    private function checkIdentityProof(string $identityProofKey, string $identityProofLocation): bool
    {
        $dnsApiUrl = self::DNS_API_URL_PART . '&name=' . $identityProofLocation;
        $responseJson = @file_get_contents($dnsApiUrl);
        if (!$responseJson) {
            return false;
        }

        $parsedResponse = json_decode($responseJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $answers = $this->findValueInNestedArray($parsedResponse, 'Answer');
        if (empty($answers)) {
            return false;
        }

        foreach ($answers as $answer) {
            if (isset($answer['data']) && str_contains($answer['data'], $identityProofKey)) {
                return true;
            }
        }

        return false;
    }
}
