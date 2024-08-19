<?php

namespace App\DTO;

class VerificationResult
{
    private string $issuer = '';
    private string $result = '';

    public function getIssuer(): string
    {
        return $this->issuer;
    }

    public function setIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }
}
