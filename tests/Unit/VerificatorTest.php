<?php

namespace Tests\Unit;

use App\DTO\ValidationResult;
use App\Services\Validators\IssuerValidator;
use App\Services\Validators\RecipientValidator;
use App\Services\Validators\SignatureValidator;
use App\Services\Verificator;
use App\Traits\NestedArrayHelperTrait;
use Mockery;
use PHPUnit\Framework\TestCase;

class VerificatorTest extends TestCase
{
    use NestedArrayHelperTrait;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testVerifyWithValidatedCondition(): void
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

        $recipientValidatorMock = Mockery::mock(RecipientValidator::class);
        $issuerValidatorMock = Mockery::mock(IssuerValidator::class);
        $signatureValidatorMock = Mockery::mock(SignatureValidator::class);

        $validationResultMock = Mockery::mock(ValidationResult::class);
        $validationResultMock->shouldReceive('getIsValidated')
            ->andReturn(true);
        $validationResultMock->shouldReceive('getStatus')
            ->andReturn(Verificator::RESULT_STATUS_VERIFIED);

        $recipientValidatorMock->shouldReceive('validate')
            ->once()
            ->with($data)
            ->andReturn($validationResultMock);

        $issuerValidatorMock->shouldReceive('validate')
            ->once()
            ->with($data)
            ->andReturn($validationResultMock);

        $signatureValidatorMock->shouldReceive('validate')
            ->once()
            ->with($data)
            ->andReturn($validationResultMock);

        $verificator = new Verificator();

        $result = $verificator->verify($data, [
            $recipientValidatorMock,
            $issuerValidatorMock,
            $signatureValidatorMock
        ]);

        $this->assertEquals('Test Issuer', $result->getIssuer());
        $this->assertEquals(Verificator::RESULT_STATUS_VERIFIED, $result->getResult());
    }
}
