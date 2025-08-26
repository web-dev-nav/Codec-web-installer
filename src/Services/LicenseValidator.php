<?php

namespace YourVendor\LaravelInstaller\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseValidator
{
    public function verify(string $licenseKey, string $email): array
    {
        try {
            $response = Http::timeout(config('installer.license_api.timeout', 30))
                ->post(config('installer.license_api.url'), [
                    'license_key' => $licenseKey,
                    'email' => $email,
                    'domain' => request()->getHost(),
                    'ip' => request()->ip(),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'valid' => $data['valid'] ?? false,
                    'message' => $data['message'] ?? 'License verified successfully',
                    'license_data' => $data['license_data'] ?? null,
                ];
            }

            return [
                'valid' => false,
                'message' => 'Unable to verify license. Please try again.',
            ];

        } catch (\Exception $e) {
            Log::error('License verification failed', [
                'license_key' => $licenseKey,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'message' => 'License verification failed. Please check your internet connection and try again.',
            ];
        }
    }

    public function validateLicenseFormat(string $licenseKey): bool
    {
        // Basic format validation (customize based on your license key format)
        return strlen($licenseKey) >= 16 && preg_match('/^[A-Za-z0-9\-]+$/', $licenseKey);
    }
}