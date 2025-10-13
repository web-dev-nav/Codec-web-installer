<?php

namespace Codelone\CodecWebInstaller\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseValidator
{
    public function verify(string $licenseKey): array
    {
        try {
            $apiUrl = config('installer.license_api.url');
            $productId = config('installer.product_id', 1);
            
            $requestData = [
                'license_key' => $licenseKey,
                'product_id' => $productId,
                'domain' => $this->getCurrentDomain(),
                'server_ip' => $this->getServerIp(), 
                'server_fingerprint' => $this->getServerFingerprint(),
            ];
            
            Log::info('Attempting license verification with installation data', [
                'api_url' => $apiUrl,
                'request_data' => $requestData,
            ]);
            
            $response = Http::timeout(config('installer.license_api.timeout', 60))
                ->withOptions([
                    'verify' => config('installer.license_api.verify_ssl', true),
                ])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->post($apiUrl, $requestData);

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
                'request_data' => $requestData,
                'api_url' => $apiUrl,
            ]);

            // Try to parse error message from response body
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Unable to verify license. Server returned: ' . $response->status();

            return [
                'valid' => false,
                'message' => $errorMessage,
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

    protected function getCurrentDomain(): string
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }
        
        return 'localhost';
    }

    protected function getServerIp(): string
    {
        // Try to get external IP first
        $externalIp = $this->getExternalIp();
        if ($externalIp) {
            return $externalIp;
        }
        
        // Fallback to server IP
        return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }

    protected function getExternalIp(): ?string
    {
        try {
            $response = Http::timeout(5)->get('https://api.ipify.org?format=json');
            if ($response->successful()) {
                $data = $response->json();
                return $data['ip'] ?? null;
            }
        } catch (\Exception $e) {
            // Ignore errors, fallback to server IP
        }
        
        return null;
    }

    protected function getServerFingerprint(): string
    {
        $data = [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '',
            'os' => PHP_OS,
        ];
        
        return hash('sha256', json_encode($data));
    }
}