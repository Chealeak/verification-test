<?php

namespace Tests\Unit;

use App\DTO\ValidationResult;
use App\Services\Validators\RecipientValidator;
use PHPUnit\Framework\TestCase;

class RecipientValidatorTest extends TestCase
{
    private RecipientValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new RecipientValidator();
    }

    public function testValidRecipientReturnsValidResult(): void
    {
        $data = [
            'data' => [
                'id' => '24d12d431d342413',
                'name' => 'Certificate of Completion',
                'recipient' => [
                    'name' => 'Test Recipient',
                    'email' => 'test.recipient@test.test'
                ],
                'issuer' => [
                    'name' => 'Test Issuer',
                    'identityProof' => [
                        'type' => 'DNS-DID',
                        'key' => '2494d255c4c50b1e521650a0659cbf3fa08b0072',
                        'location' => 'apple.com'
                    ]
                ],
                'issued' => '2022-12-23T00:00:00+08:00'
            ],
            'signature' => [
                'type' => 'SHA3MerkleProof',
                'targetHash' => 'feaab0a4ed08991b2e8199944fe996443925f9af841c8662e679dc8edb019646'
            ]
        ];

        $result = $this->validator->validate($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->getIsValidated());
        $this->assertEquals(RecipientValidator::RESULT_STATUS_VALID, $result->getStatus());
    }
}
