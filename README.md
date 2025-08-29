# Laravel Installer Package

A WordPress-like installer package for Laravel applications that provides a smooth, multi-step installation processES.

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

Update your `.env` file with the API endpoint and product ID:

```env
INSTALLER_LICENSE_API_URL=https://api.yoursite.com/api/verify-license
INSTALLER_PRODUCT_ID=1
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

### 3. API Endpoint

Your server should provide this API endpoint:

#### License Verification Endpoint
```
POST /api/verify-license
Content-Type: application/json

{
    "license_key": "DYTIOHVHHABDQVOH",
    "product_id": 1
}

Success Response:
{
    "success": true,
    "message": "License verified successfully",
    "product_id": 1,
    "license_id": 123,
    "product_data": "SQL content for installation",
    "product_name": "Test Website",
    "product_version": "1.0.0",
    "allowed_domains": 3,
    "license_status": "active",
    "expires_at": "2025-01-12 10:30:00"
}

Error Response:
{
    "success": false,
    "message": "Invalid license key"
}
```

**Note:** The `product_data` field contains the SQL content that will be imported into the database during installation.

## Configuration Options

### System Requirements

Customize requirements in `config/installer.php`:

```php
'requirements' => [
    'php' => '8.2.0',
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

- PHP 8.2 or higher
- Laravel 10.0 or higher (supports Laravel 12.x)
- Required PHP extensions (see configuration)
- Writable storage directories

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@yourvendor.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
