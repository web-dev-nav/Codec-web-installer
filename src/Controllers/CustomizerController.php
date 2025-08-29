<?php

namespace Codelone\CodecWebInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CustomizerController extends Controller
{
    protected $lockFile = 'installer-customized.lock';

    public function __construct()
    {
        // Only allow in development environment
        if (!app()->environment(['local', 'testing']) && !config('app.debug')) {
            abort(404);
        }

        // Check if already customized
        if (Storage::exists($this->lockFile)) {
            abort(404, 'Customization interface has been permanently disabled');
        }
    }

    public function dashboard()
    {
        $currentConfig = config('installer');
        $availableExtensions = [
            // Core extensions
            'PDO', 'cURL', 'OpenSSL', 'BCMath', 'Ctype', 'Fileinfo', 
            'JSON', 'Mbstring', 'Tokenizer', 'XML', 'ZIP', 'Hash',
            'Filter', 'PCRE', 'Session', 'SPL', 'Standard',
            
            // Database extensions
            'MySQLi', 'PDO_MySQL', 'PDO_SQLite', 'PDO_PostgreSQL', 'SQLite3',
            'MongoDB', 'Redis', 'Memcached',
            
            // Image/Media extensions  
            'GD', 'Imagick', 'Exif', 'FFMpeg',
            
            // Compression extensions
            'Zlib', 'BZip2', 'RaR',
            
            // Encryption/Security
            'Sodium', 'LibSodium', 'Mcrypt',
            
            // Web/Network extensions
            'Sockets', 'FTP', 'SSH2', 'OAuth',
            
            // Other common extensions
            'Intl', 'LDAP', 'SOAP', 'XSL', 'SimpleXML', 'DOM',
            'Calendar', 'Gettext', 'IMAP', 'SNMP', 'Tidy',
            'PCNTL', 'POSIX', 'SysVSem', 'SysVShm', 'ShmOP'
        ];
        
        return view('installer::customize.single', compact('currentConfig', 'availableExtensions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'php_version' => 'required|string',
            'extensions' => 'array',
            'folders' => 'array',
            'folders.*.path' => 'required|string',
            'folders.*.permission' => 'required|string',
            'app_name' => 'required|string|max:255',
            'primary_color' => 'required|string',
            'welcome_title' => 'nullable|string|max:255',
            'welcome_description' => 'nullable|string',
        ]);

        $requirements = [
            'php' => $request->php_version,
            'extensions' => $request->extensions ?? [],
            'folders' => collect($request->folders)->filter()->mapWithKeys(function($folder) {
                return [$folder['path'] => $folder['permission']];
            })->toArray(),
        ];

        $branding = [
            'app_name' => $request->app_name,
            'primary_color' => $request->primary_color,
            'welcome_title' => $request->welcome_title,
            'welcome_description' => $request->welcome_description,
        ];

        // Apply changes directly to project files
        $this->replaceProjectFiles($requirements, $branding);
        
        // Self-destruct: Delete admin files and create lock
        $this->selfDestruct();
        
        return redirect()->route('installer.welcome')->with('success', 'Installer customized successfully! Admin interface has been permanently removed.');
    }

    public function download()
    {
        $requirements = session('customizer.requirements', config('installer.requirements'));
        $branding = session('customizer.branding', []);

        // Generate and replace files directly in project
        $this->replaceProjectFiles($requirements, $branding);
        
        // Self-destruct: Delete admin files and create lock
        $this->selfDestruct();
        
        return redirect('/')->with('success', 'Installer has been customized and admin interface permanently removed!');
    }

    protected function generateCustomizedFiles($requirements, $branding)
    {
        $files = [];
        
        // Generate customized config
        $configContent = $this->generateConfigFile($requirements, $branding);
        $files['config/installer.php'] = $configContent;
        
        // Generate customized welcome view if branding provided
        if (!empty($branding)) {
            $welcomeContent = $this->generateWelcomeView($branding);
            $files['resources/views/installer/welcome.blade.php'] = $welcomeContent;
        }
        
        return $files;
    }

    protected function generateConfigFile($requirements, $branding)
    {
        $config = file_get_contents(__DIR__ . '/../../config/installer.php');
        
        // Format requirements array properly
        $requirementsStr = "[\n        'php' => '{$requirements['php']}',\n";
        $requirementsStr .= "        'extensions' => [\n";
        foreach ($requirements['extensions'] as $ext) {
            $requirementsStr .= "            '$ext',\n";
        }
        $requirementsStr .= "        ],\n";
        $requirementsStr .= "        'folders' => [\n";
        foreach ($requirements['folders'] as $path => $permission) {
            $requirementsStr .= "            '$path' => '$permission',\n";
        }
        $requirementsStr .= "        ],\n    ]";
        
        // Replace requirements section with properly formatted array
        $config = preg_replace(
            "/'requirements' => \[.*?\],/s",
            "'requirements' => $requirementsStr,",
            $config
        );
        
        return $config;
    }

    protected function generateWelcomeView($branding)
    {
        $template = file_get_contents(__DIR__ . '/../../resources/views/installer/welcome.blade.php');
        
        if (isset($branding['welcome_title'])) {
            $template = str_replace(
                'Welcome to the Installation Wizard',
                $branding['welcome_title'],
                $template
            );
        }
        
        if (isset($branding['welcome_description'])) {
            $template = str_replace(
                'This wizard will guide you through the installation process in a few simple steps.',
                $branding['welcome_description'],
                $template
            );
        }
        
        return $template;
    }

    protected function replaceProjectFiles($requirements, $branding)
    {
        // Replace config file
        $configContent = $this->generateConfigFile($requirements, $branding);
        file_put_contents(__DIR__ . '/../../config/installer.php', $configContent);
        
        // Replace welcome view if branding provided
        if (!empty($branding)) {
            $welcomeContent = $this->generateWelcomeView($branding);
            file_put_contents(__DIR__ . '/../../resources/views/installer/welcome.blade.php', $welcomeContent);
        }
        
        // Log the customization
        \Log::info('Installer customized', [
            'timestamp' => now(),
            'php_version' => $requirements['php'] ?? 'unchanged',
            'extensions_count' => count($requirements['extensions'] ?? []),
            'folders_count' => count($requirements['folders'] ?? []),
            'branding_applied' => !empty($branding),
        ]);
    }

    protected function selfDestruct()
    {
        // Create permanent lock file
        Storage::put($this->lockFile, date('Y-m-d H:i:s') . ' - Customization completed');
        
        // Delete admin files
        $filesToDelete = [
            __FILE__, // This controller
            __DIR__ . '/../../resources/views/installer/customize',
        ];
        
        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    File::deleteDirectory($file);
                } else {
                    unlink($file);
                }
            }
        }
    }
}