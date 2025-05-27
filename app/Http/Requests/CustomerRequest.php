<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\AbstractApi\PhoneValidationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => ['required', 'string', function($attribute, $value, $fail) {
                $phoneService = new PhoneValidationService();
                $validation = $phoneService->validatePhone($value);

                if (!$validation['valid']) {
                    $fail($validation['error'] ?? 'The phone number is invalid.');
                }
            }],
        ];

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
