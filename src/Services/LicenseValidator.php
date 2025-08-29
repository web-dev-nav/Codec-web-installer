<?php

namespace Codelone\CodecWebInstaller\Services;

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
                    'Accept' => 'application/json',
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

            Log::warning('License verification API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'valid' => false,
                'message' => 'Unable to verify license. Server returned: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('License verification failed', [
                'license_key' => $licenseKey,
                'product_id' => config('installer.product_id'),
                'api_url' => config('installer.license_api.url'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'valid' => false,
                'message' => 'License verification failed: ' . $e->getMessage(),
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