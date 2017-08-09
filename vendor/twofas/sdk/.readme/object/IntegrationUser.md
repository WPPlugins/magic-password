## IntegrationUser

IntegrationUser object is returned by [getIntegrationUser](#getintegrationuser) method.

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods
Name | Type | Description
--- | --- | ---
getId() | `string` | id
getExternalId() | `string` | external id
getActiveMethod() | `string` | active method
getPhoneNumber() | `string` | phone number
getTotpSecret() | `string` | totp secret
getEmail() | `string` | email
getMobileSecret() | `string` | mobile secret
getBackupCodesCount() | `string` | backup codes count
hasMobileUser() | `bool` | mobile user state

#### Usage
```php
$user->getId();
$user->getPhoneNumber();
//...
```
