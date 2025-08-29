<?php

namespace Codelone\CodecWebInstaller\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use PDO;
use PDOException;

class DatabaseInstaller
{
    public function install(array $credentials, string $productData): array
    {
        try {
            // Test database connection
            $connectionTest = $this->testConnection($credentials);
            if (!$connectionTest['success']) {
                return $connectionTest;
            }

            // Execute SQL from product_data
            $sqlExecution = $this->executeSqlFile($credentials, $productData);
            if (!$sqlExecution['success']) {
                return $sqlExecution;
            }

            // Update environment file
            $this->updateEnvironmentFile($credentials);

            return [
                'success' => true,
                'message' => 'Database installation completed successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Database installation failed', [
                'error' => $e->getMessage(),
                'credentials' => array_merge($credentials, ['db_password' => '***']),
            ]);

            return [
                'success' => false,
                'message' => 'Database installation failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function testConnection(array $credentials): array
    {
        try {
            $dsn = "mysql:host={$credentials['db_host']};port={$credentials['db_port']};dbname={$credentials['db_name']};charset=utf8mb4";
            
            Log::info('Testing database connection', [
                'dsn' => $dsn,
                'username' => $credentials['db_username'],
                'password_empty' => empty($credentials['db_password']),
                'password_null' => is_null($credentials['db_password']),
            ]);
            
            $pdo = new PDO($dsn, $credentials['db_username'], $credentials['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return [
                'success' => true,
                'message' => 'Database connection successful',
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }


    protected function executeSqlFile(array $credentials, string $sqlContent): array
    {
        try {
            $dsn = "mysql:host={$credentials['db_host']};port={$credentials['db_port']};dbname={$credentials['db_name']};charset=utf8mb4";
            
            $pdo = new PDO($dsn, $credentials['db_username'], $credentials['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Split SQL content into individual queries
            $queries = $this->splitSqlQueries($sqlContent);

            $pdo->beginTransaction();

            foreach ($queries as $query) {
                if (trim($query)) {
                    $pdo->exec($query);
                }
            }

            $pdo->commit();

            return [
                'success' => true,
                'message' => 'SQL file executed successfully',
            ];

        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => 'SQL execution failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function splitSqlQueries(string $sql): array
    {
        // Remove comments and split by semicolon
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        return array_filter(array_map('trim', explode(';', $sql)));
    }

    protected function updateEnvironmentFile(array $credentials): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $env = file_get_contents($envFile);

        $replacements = [
            'DB_HOST=' => 'DB_HOST=' . $credentials['db_host'],
            'DB_PORT=' => 'DB_PORT=' . $credentials['db_port'],
            'DB_DATABASE=' => 'DB_DATABASE=' . $credentials['db_name'],
            'DB_USERNAME=' => 'DB_USERNAME=' . $credentials['db_username'],
            'DB_PASSWORD=' => 'DB_PASSWORD=' . $credentials['db_password'],
        ];

        foreach ($replacements as $key => $value) {
            if (strpos($env, $key) !== false) {
                $env = preg_replace('/^' . preg_quote($key, '/') . '.*$/m', $value, $env);
            } else {
                $env .= "\n" . $value;
            }
        }

        file_put_contents($envFile, $env);
    }
}