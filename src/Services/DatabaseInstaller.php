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
            Log::info('Starting database installation', [
                'host' => $credentials['db_host'],
                'port' => $credentials['db_port'],
                'database' => $credentials['db_name'],
                'username' => $credentials['db_username'],
                'has_password' => !empty($credentials['db_password']),
            ]);
            
            // Test database connection
            $connectionTest = $this->testConnection($credentials);
            if (!$connectionTest['success']) {
                Log::error('Connection test failed', $connectionTest);
                return $connectionTest;
            }
            
            Log::info('Database connection successful, proceeding with SQL execution');

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


    protected function executeSqlFile(array $credentials, $sqlContent): array
    {
        try {
            // Handle different data formats
            if (is_array($sqlContent)) {
                // If it's an array, get the SQL content from it
                $sqlContent = $sqlContent['sql'] ?? $sqlContent['content'] ?? '';
            } elseif (is_string($sqlContent) && (substr($sqlContent, 0, 2) === 'a:' || substr($sqlContent, 0, 2) === 's:' || substr($sqlContent, 0, 2) === 'i:')) {
                // If it's serialized PHP data, unserialize it
                $unserialized = @unserialize($sqlContent);
                if ($unserialized !== false) {
                    $sqlContent = is_array($unserialized) ? ($unserialized['sql'] ?? $unserialized['content'] ?? '') : $unserialized;
                }
            }

            // Ensure we have a string
            $sqlContent = (string) $sqlContent;

            Log::info('SQL Content received', [
                'length' => strlen($sqlContent),
                'first_100_chars' => substr($sqlContent, 0, 100),
                'type' => gettype($sqlContent),
            ]);

            if (empty($sqlContent)) {
                return [
                    'success' => false,
                    'message' => 'SQL content is empty. Please contact support.',
                ];
            }

            $dsn = "mysql:host={$credentials['db_host']};port={$credentials['db_port']};dbname={$credentials['db_name']};charset=utf8mb4";

            $pdo = new PDO($dsn, $credentials['db_username'], $credentials['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Split SQL content into individual queries
            $queries = $this->splitSqlQueries($sqlContent);

            // Execute queries without transaction for MySQL dumps
            // MySQL dumps often contain DDL statements that auto-commit
            foreach ($queries as $query) {
                $query = trim($query);
                if (!$query) {
                    continue;
                }

                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    // Log the error but continue with other queries
                    // Some errors like "table already exists" can be ignored
                    if ($e->getCode() !== '42S01') { // Not "Base table or view already exists"
                        Log::warning('Query execution warning', [
                            'error' => $e->getMessage(),
                            'code' => $e->getCode(),
                            'query_preview' => substr($query, 0, 200),
                        ]);
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'SQL file executed successfully',
            ];

        } catch (PDOException $e) {
            Log::error('SQL execution error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return [
                'success' => false,
                'message' => 'SQL execution failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function splitSqlQueries(string $sql): array
    {
        // Remove SQL comments but preserve special MySQL comments (like /*!40101 ... */)
        $sql = preg_replace('/--[^\n]*\n/m', "\n", $sql);
        $sql = preg_replace('/\/\*(?!\!)[^\*]*\*+([^\/*][^\*]*\*+)*\//s', '', $sql);

        // Split by semicolon followed by newline (to avoid splitting within strings)
        $queries = preg_split('/;\s*[\r\n]+/', $sql);

        return array_filter(array_map('trim', $queries));
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