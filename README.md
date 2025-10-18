# Laravel SDK for Grandstream Devices

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akramghaleb/laravel-grandstream.svg?style=flat-square)](https://packagist.org/packages/akramghaleb/laravel-grandstream)
[![Total Downloads](https://img.shields.io/packagist/dt/akramghaleb/laravel-grandstream.svg?style=flat-square)](https://packagist.org/packages/akramghaleb/laravel-grandstream)

---

## 📦 Overview

**Laravel Grandstream** is a clean SDK for integrating Laravel apps with **Grandstream UCM** PBX devices.  
It simplifies REST API communication, including:

- Challenge → Token → Cookie authentication flow
- Cached login cookies per user/session
- Unified API request handling with auto-retry on cookie expiration
- Easy integration into Filament dashboards or CRM systems

Ideal for **real-time call monitoring**, **extension management**, or **PBX-based CRM dashboards**.

---
## 📖 Official API Reference

The complete Grandstream HTTPS API documentation is available here:  
👉 [**Grandstream UCM6xxx HTTPS API Guide (PDF)**](https://www.grandstream.com/hubfs/Product_Documentation/UCM_API_Guide.pdf)

It includes:
- Authentication flow (**`challenge`** → **`login`**)
- Voice & call control endpoints (**listBridgedChannels**, **Hangup**, **CallTransfer**, etc.)
- CDR & Recording APIs (**cdrapi**, **recapi**)
- Error codes and result structures

> This Laravel package aligns with the same API structure and request schema described in that official document.

---

## ⚙️ Installation

Install the package via Composer:

```
composer require akramghaleb/laravel-grandstream
```

Then publish the configuration file:

```
php artisan vendor:publish --tag="laravel-grandstream-config"
```

---

## 🔧 Configuration

A `config/grandstream.php` file will be created.  
Add your device credentials to `.env`:

```
UCM_BASE=https://your-ucm-ip
UCM_API_USER=apiuser
UCM_API_PASS=apipassword
UCM_API_VER=1.2
UCM_COOKIE_TTL=9
GRANDSTREAM_WEBHOOK_USER=admin
GRANDSTREAM_WEBHOOK_PASS=password
```

---

## 🚀 Usage

Use the **Facade** for simple calls:

```php
use AkramGhaleb\LaravelGrandstream\Facades\Grandstream;

// Example: List extensions
$response = Grandstream::getData('listAccount', [
            'options' => 'extension,account_type,fullname,status,addr',
            'page' => 1, 'sidx' => 'extension', 'sord' => 'asc',
        ]);

// Example: Fetch call records (CDR)
$cdr = Grandstream::getData('cdrapi', ["format":"json"]);
```

The package automatically retries failed requests when cookies expire (`-6`, `-8`, `-37`).

---

## 🧩 Example Response

```json
{
    "status": 0,
    "response": {
        "total": 125,
        "cdr": [
            {
            "call_time": "2025-10-18 09:14:23",
            "src": "1001",
            "dst": "1002",
            "duration": "00:00:12",
            "disposition": "ANSWERED"
            }
        ]
    }
}
```

---

## 🧱 Advanced Usage

Inject it directly instead of using the Facade:

```php
use AkramGhaleb\LaravelGrandstream\Grandstream;

class CallController
{
    public function index()
    {
        $calls = Grandstream::getData('listUnBridgedChannels');
        return response()->json($calls);
    }
}
```

---

## 🧪 Testing

```
composer test
```

---

## 📝 Changelog

See [CHANGELOG](CHANGELOG.md) for details.

---

## 🤝 Contributing

Contributions are welcome!  
Please read [CONTRIBUTING.md](CONTRIBUTING.md) before submitting a PR.

---

## 🛡️ Security

For security vulnerabilities, please review [our security policy](../../security/policy).

---

## 👨‍💻 Credits

- [Akram Ghaleb](https://github.com/akramghaleb)
- [All Contributors](../../contributors)

---

## 📄 License

This project is open-source software licensed under the **MIT License**.  
See [LICENSE](LICENSE.md) for more information.

---

## 🌍 Support

If you find this package useful, please ⭐ it on [GitHub](https://github.com/akramghaleb/laravel-grandstream).  
You can also support by sharing feedback, contributing, or improving documentation.
