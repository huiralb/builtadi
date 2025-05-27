<?php

namespace App\Services\AbstractApi;

use Illuminate\Support\Facades\Http;
use Exception;

class PhoneValidationService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = '6ec9be2faaa841338de98826829e09ba';
        $this->baseUrl = 'https://phonevalidation.abstractapi.com/v1/';
    }

    public function validatePhone(string $phone, string $countryCode = 'ID'): array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'api_key' => $this->apiKey,
                'phone' => $phone,
                'country' => $countryCode,
            ]);

            if (!$response->successful()) {
                throw new Exception('Failed to validate phone number. Status: ' . $response->status());
            }

            $data = $response->json();
            $errorMessage = $data['error'] ?? 'Invalid phone number format. The phone format should be like 081234567890 or +6281234567890';

            if (!isset($data['valid']) || $data['valid'] === false) {
                throw new Exception($errorMessage);
            }

            return [
                'valid' => $data['valid'],
                'error' => $data['valid'] ? null : $errorMessage,
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
