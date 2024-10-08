<?php

namespace App\Http\Controllers;

use App\Http\Resources\VerificationResource;
use App\Models\Verification;
use App\Services\Validators\IssuerValidator;
use App\Services\Validators\RecipientValidator;
use App\Services\Validators\SignatureValidator;
use App\Services\Verificator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Verification API",
 *         version="1.0.0",
 *         description="API documentation for user verification"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="sanctum",
 *             type="apiKey",
 *             description="Enter token in format (Bearer <token>)",
 *             name="Authorization",
 *             in="header"
 *         )
 *     )
 * )
 */
class VerificationController extends Controller
{
    public function __construct(private readonly Verificator $verificator) {}

    /**
     * @OA\Post(
     *     path="/api/verification",
     *     summary="Verify a file",
     *     tags={"Verification"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to be uploaded"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="issuer", type="string", description="Issuer of the verification result"),
     *                 @OA\Property(property="result", type="string", description="Result of the verification process")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request): JsonResource|JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        $file = $request->file('file');

        $jsonContent = file_get_contents($file->getPathname());

        $parsedData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                'message' => 'Invalid JSON format.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $verificationResult = $this->verificator->verify($parsedData, [
            new RecipientValidator(),
            new IssuerValidator(),
            new SignatureValidator()
        ]);

        Verification::create([
            'user_id' => Auth::id(),
            'file_type' => $file->getClientOriginalExtension(),
            'verification_result' => $verificationResult->getResult()
        ]);

        return VerificationResource::make($verificationResult);
    }
}
