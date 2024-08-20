<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Services\Validators\IssuerValidator;
use App\Services\Validators\RecipientValidator;
use App\Services\Validators\SignatureValidator;
use App\Services\Verificator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         ),
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
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        $file = $request->file('file');

        $jsonContent = file_get_contents($file->getPathname());

        $parsedData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'message' => 'Invalid JSON format.',
            ], 400);
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

        return response()->json([
            'data' => [
                'issuer' => $verificationResult->getIssuer(),
                'result' => $verificationResult->getResult()
            ]
        ], 200);
    }
}
