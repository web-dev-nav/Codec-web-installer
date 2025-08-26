<?php

namespace YourVendor\LaravelInstaller\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseValidator
{
    public function verify(string $licenseKey): array
    {
        try {
            $response = Http::timeout(config('installer.license_api.timeout', 60))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post(config('installer.license_api.url'), [
                    'license_key' => $licenseKey,
                    'product_id' => config('installer.product_id', 1),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    // Check if license is active and not expired
                    $isActive = $this->isLicenseActive($data);
                    
                    return [
                        'valid' => $isActive,
                        'message' => $isActive ? ($data['message'] ?? 'License verified successfully') : 'License is expired or inactive',
                        'license_data' => $data,
                        'product_data' => $data['product_data'] ?? null,
                        'product_name' => $data['product_name'] ?? null,
                        'product_version' => $data['product_version'] ?? null,
                        'expires_at' => $data['expires_at'] ?? null,
                        'allowed_domains' => $data['allowed_domains'] ?? null,
                    ];
                } else {
                    return [
                        'valid' => false,
                        'message' => $data['message'] ?? 'Invalid license key',
                    ];
                }
            }

            return [
                'valid' => false,
                'message' => 'Unable to verify license. Please try again.',
            ];

        } catch (\Exception $e) {
            Log::error('License verification failed', [
                'license_key' => $licenseKey,
                'product_id' => config('installer.product_id'),
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'message' => 'License verification failed. Please check your internet connection and try again.',
            ];
        }
    }

    protected function isLicenseActive(array $licenseData): bool
    {
        // Check license status
        if (($licenseData['license_status'] ?? '') !== 'active') {
            return false;
        }

        // Check expiration date if present
        if (isset($licenseData['expires_at']) && $licenseData['expires_at']) {
            $expiresAt = \Carbon\Carbon::parse($licenseData['expires_at']);
            if ($expiresAt->isPast()) {
                return false;
            }
        }

        return true;
    }

    public function validateLicenseFormat(string $licenseKey): bool
    {
        // Validate format based on the example: DYTIOHVHHABDQVOH (16 characters, uppercase letters)
        return strlen($licenseKey) >= 12 && preg_match('/^[A-Z0-9]+$/', $licenseKey);
    }
}