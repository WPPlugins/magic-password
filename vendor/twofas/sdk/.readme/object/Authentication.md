## Authentication

Authentication object is returned by:

 * [requestAuth](#requestauth)
 * [requestAuthViaSms](#requestauthviasms)
 * [requestAuthViaCall](#requestauthviacall)
 * [requestAuthViaEmail](#requestauthviaemail)
 * [requestAuthViaTotp](#requestauthviatotp)

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods
Name | Type | Description
--- | --- | ---
id() | `string` | Authentication id
createdAt() | `DateTime` | Date of creation (in local timezone)
validTo() | `DateTime` | Date of end of validity (in local timezone)
isValid() | `bool` | Validity date check

#### Usage
```php
$authentication->id();
$authentication->createdAt();
$authentication->validTo();
$authentication->isValid();
```
