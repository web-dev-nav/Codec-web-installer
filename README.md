# Laravel Installer Package

A WordPress-like installer package for Laravel applications that provides a smooth, multi-step installation process.

## Features

- **Multi-step Installation Process**: Guided installation similar to WordPress
- **System Requirements Check**: Validates PHP version, extensions, and folder permissions
- **License Verification**: API-based license validation system
- **Database Setup**: Automated database configuration and SQL import
- **Security Features**: Installation lock, CSRF protection, and secure API communication

## Installation

Install the package via Composer:

```bash
composer require your-vendor/laravel-installer
```

The package will auto-register the service provider in Laravel 5.5+.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=installer-config
```

Update your `.env` file with the API endpoints:

```env
INSTALLER_LICENSE_API_URL=https://api.yoursite.com/verify-license
INSTALLER_SQL_API_URL=https://api.yoursite.com/download-sql
```

## Usage

### 1. Access the Installer

Visit `/installer` in your browser to start the installation process.

### 2. Installation Steps

1. **Welcome Page**: Introduction and overview
2. **System Requirements**: Check PHP version, extensions, and permissions
3. **License Verification**: Enter license key and email for validation
4. **Database Setup**: Configure database connection and import data
5. **Completion**: Installation summary and next steps

### 3. API Endpoints

Your server should provide these API endpoints:

#### License Verification Endpoint
```
POST /verify-license
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "email": "user@example.com",
    "domain": "example.com",
    "ip": "192.168.1.1"
}

Response:
{
    "valid": true,
    "message": "License verified successfully",
    "license_data": {...}
}
```

#### SQL Download Endpoint
```
POST /download-sql
{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "email": "user@example.com",
    "domain": "example.com"
}

Response:
{
    "sql_content": "CREATE TABLE users (...); INSERT INTO..."
}
```

## Configuration Options

### System Requirements

Customize requirements in `config/installer.php`:

```php
'requirements' => [
    'php' => '8.1.0',
    'extensions' => [
        'PDO', 'cURL', 'OpenSSL', 'BCMath', 'Ctype',
        'Fileinfo', 'JSON', 'Mbstring', 'Tokenizer', 'XML', 'ZIP'
    ],
    'folders' => [
        'storage/app/' => '775',
        'storage/framework/' => '775',
        'storage/logs/' => '775',
        'bootstrap/cache/' => '775',
    ],
],
```

### Customization

Publish views for customization:

```bash
php artisan vendor:publish --tag=installer-views
```

## Security

- Installation is locked after completion via a lock file
- Routes are protected by middleware
- All forms include CSRF protection
- Database credentials are validated before use
- API communication uses secure HTTPS

## License Lock

After successful installation, a lock file is created at `storage/installer.lock`. To reinstall:

1. Delete the lock file
2. Clear browser cache
3. Visit `/installer` again

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Required PHP extensions (see configuration)
- Writable storage directories

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@yourvendor.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.