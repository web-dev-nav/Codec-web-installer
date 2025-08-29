<?php

namespace Codelone\CodecWebInstaller\Services;

class SystemChecker
{
    public function check(): array
    {
        $results = [
            'php' => $this->checkPhpVersion(),
            'extensions' => $this->checkExtensions(),
            'permissions' => $this->checkPermissions(),
        ];

        $results['overall'] = $results['php']['supported'] && 
                             $results['extensions']['supported'] && 
                             $results['permissions']['supported'];

        return $results;
    }

    protected function checkPhpVersion(): array
    {
        $required = config('installer.requirements.php', '8.1.0');
        $current = PHP_VERSION;
        $supported = version_compare($current, $required, '>=');

        return [
            'required' => $required,
            'current' => $current,
            'supported' => $supported,
        ];
    }

    protected function checkExtensions(): array
    {
        $required = config('installer.requirements.extensions', []);
        $extensions = [];
        $allSupported = true;

        foreach ($required as $extension) {
            $loaded = extension_loaded($extension);
            $extensions[] = [
                'name' => $extension,
                'required' => true,
                'loaded' => $loaded,
            ];

            if (!$loaded) {
                $allSupported = false;
            }
        }

        return [
            'extensions' => $extensions,
            'supported' => $allSupported,
        ];
    }

    protected function checkPermissions(): array
    {
        $folders = config('installer.requirements.folders', []);
        $permissions = [];
        $allSupported = true;

        foreach ($folders as $folder => $permission) {
            $path = base_path($folder);
            $writable = is_writable($path);
            $permissions[] = [
                'folder' => $folder,
                'required' => $permission,
                'current' => $writable ? 'Writable' : 'Not Writable',
                'supported' => $writable,
            ];

            if (!$writable) {
                $allSupported = false;
            }
        }

        return [
            'permissions' => $permissions,
            'supported' => $allSupported,
        ];
    }
}