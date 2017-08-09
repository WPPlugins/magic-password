## QrCodeGenerator

QrCodeGenerator object generates base64 encoded image of QR code,
that can be easily displayed for user to scan it with smartphone.

#### Methods
Name | Type | Description
--- | --- | ---
generateBase64($text) | `string` | Returns base64 encoded image

#### Usage
```php
$qrGen = new QrCodeGenerator(QrClientFactory::getInstance());
$qrCode = $qrGen->generateBase64($userSecret);
```
