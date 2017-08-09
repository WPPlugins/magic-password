# Install

### via composer

    composer require twofas/sdk : "2.*"

# Documentation

#### Creating client

```php
$twoFAS = new \TwoFAS\Api\TwoFAS('login', 'api_key');
```

#### All methods

All methods can throw following exceptions:

###### Unsuccessful

!include(exception/AuthorizationException.md)
!include(exception/Exception.md)

Additional exceptions are described for each method

# Methods

## formatNumber

Used for checking if number is valid and to unify format.
You can store unified number in DB to prevent creation of multiple users with same phone number.

#### Parameters
Name | Type | Description
--- | --- | ---
$phoneNumber | `string` | Phone number in any format

#### Example

```php
$formatted = $twoFAS->formatNumber('5123631111');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\FormattedNumber](#formattednumber) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/InvalidNumberException.md)

## requestAuthViaSms

Used for requesting authentication on user via SMS.
Store authentication id for later use.

#### Parameters
Name | Type | Description
--- | --- | ---
$phoneNumber | `string` | Phone number in any format

#### Example

```php
$authentication = $twoFAS->requestAuthViaSms('5123631111');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\Authentication](#authentication) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/AuthenticationsLimitationException.md)
!include (exception/ChannelNotActiveException.md)
!include (exception/CountryIsBlockedException.md)
!include (exception/InvalidDateException.md)
!include (exception/InvalidNumberException.md)
!include (exception/NumbersLimitationException.md)
!include (exception/PaymentException.md)
!include (exception/SmsToLandlineException.md)
!include (exception/ValidationException.md)

## requestAuthViaCall

Used for requesting authentication on user via CALL.
Store authentication id for later use.

#### Parameters
Name | Type | Description
--- | --- | ---
$phoneNumber | `string` | Phone number in any format

#### Example

```php
$authentication = $twoFAS->requestAuthViaCall('5123631111');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\Authentication](#authentication) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/AuthenticationsLimitationException.md)
!include (exception/ChannelNotActiveException.md)
!include (exception/CountryIsBlockedException.md)
!include (exception/InvalidDateException.md)
!include (exception/InvalidNumberException.md)
!include (exception/NumbersLimitationException.md)
!include (exception/PaymentException.md)
!include (exception/ValidationException.md)

## requestAuthViaEmail

Used for requesting authentication on user via email.
Store authentication id for later use.

#### Parameters
Name | Type | Description
--- | --- | ---
$email | `string` | Email address

#### Example

```php
$authentication = $twoFAS->requestAuthViaEmail('example@example.com');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\Authentication](#authentication) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/ChannelNotActiveException.md)
!include (exception/InvalidDateException.md)
!include (exception/ValidationException.md)

## requestAuthViaTotp

Used for requesting authentication on user via TOTP (Time-based One-time Password Algorithm).
Store authentication id for later use.

#### Parameters
Name | Type | Description
--- | --- | ---
$secret | `string` | Totp secret in 16 base32 characters
$mobileSecret | `string|null` | Secret used for push notifications

#### Example

```php
$authentication = $twoFAS->requestAuthViaTotp('JBSWY3DPEHPK3PXP');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\Authentication](#authentication) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/ChannelNotActiveException.md)
!include (exception/InvalidDateException.md)
!include (exception/ValidationException.md)

## requestAuth

Used for requesting authentication on integration user.
This method merge all previous authenticate methods.
Store authentication id for later use.

#### Parameters
Name | Type | Description
--- | --- | ---
$keyStorage | `KeyStorage` | Your class to keep Key used in encrypt/decrypt data
$userId | `string` | Id of integration user who wants to authenticate

#### Example

```php
$authentication = $twoFAS->requestAuth($keyStorage, '5788b5e5002f0');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\Authentication](#authentication) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/AuthenticationsLimitationException.md)
!include (exception/ChannelNotActiveException.md)
!include (exception/CountryIsBlockedException.md)
!include (exception/IntegrationUserHasNoActiveMethodException.md)
!include (exception/IntegrationUserNotFoundException.md)
!include (exception/InvalidDateException.md)
!include (exception/InvalidNumberException.md)
!include (exception/NumbersLimitationException.md)
!include (exception/PaymentException.md)
!include (exception/SmsToLandlineException.md)
!include (exception/ValidationException.md)

## checkCode

Used for validating code entered by user.

#### Parameters
Name | Type | Description
--- | --- | ---
$collection | `AuthenticationCollection` | Collection of authentication ids
$code | `string` | Code provided by user

[AuthenticationCollection](#authentication-collection)

#### Example

```php
$checkCode = $twoFAS->checkCode($collection, '123456');

if ($checkCode->accepted()) {

}
```

#### Response

###### Successful

##### Returns instance of [TwoFAS\Api\Code\Code](#code-interface) interface

## checkBackupCode

Used for validating backup code entered by user.

Backup code is expected to be 12 non-omitted characters.
Non-omitted characters consists of subsets: 
  - letters: `abcdefghjkmnpqrstuvwxyz`
  - numbers: `23456789`
  
You can send code with or without `-` separators, code is not case-sensitive.

#### Parameters
Name | Type | Description
--- | --- | ---
$user | `IntegrationUser` | User that wants to use backup code
$collection | `AuthenticationCollection` | Collection of authentication ids
$code | `string` | Code provided by user

[AuthenticationCollection](#authentication-collection)

#### Example

```php
try {
    
    $checkCode = $twoFAS->checkBackupCode($user, $collection, 'aaaa-bbbb-cccc');
    
    if ($checkCode->accepted()) {
    
    }
    
} catch (ValidationException $e) {
    
}
```

#### Response

###### Successful

##### Returns instance of [TwoFAS\Api\Code\Code](#code-interface) interface

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/ValidationException.md)

## getIntegrationUser

Used for get integration user from 2fas.

#### Parameters
Name | Type | Description
--- | --- | ---
$keyStorage | `KeyStorage` | Your class to keep Key used in encrypt/decrypt data
$userId | `string` | Id of integration user who wants to get


#### Example

```php
$user = $twoFAS->addIntegrationUser($keyStorage, '5788b5e5002f0');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\IntegrationUser](#integrationuser) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/IntegrationUserNotFoundException.md)

## getIntegrationUserByExternalId

Used for get integration user from 2fas by your own id.

#### Parameters
Name | Type | Description
--- | --- | ---
$keyStorage | `KeyStorage` | Your class to keep Key used in encrypt/decrypt data
$userExternalId | `string` | External id of integration user who wants to get


#### Example

```php
$user = $twoFAS->getIntegrationUserByExternalId($keyStorage, '468');
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\IntegrationUser](#integrationuser) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/IntegrationUserNotFoundException.md)

## addIntegrationUser

Used for add integration user to 2fas.

#### Parameters
Name | Type | Description
--- | --- | ---
$keyStorage | `KeyStorage` | Your class to keep Key used in encrypt/decrypt data
$user | `IntegrationUser` | User who want to add to 2fas


#### Example

```php
$user = new IntegrationUser();
$user
    ->setActiveMethod('totp')
    ->setTotpSecret('...')
    //...
$user = $twoFAS->addIntegrationUser($keyStorage, $user);
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\IntegrationUser](#integrationuser) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/ValidationException.md)

## updateIntegrationUser

Used for update integration user in 2fas.

#### Parameters
Name | Type | Description
--- | --- | ---
$keyStorage | `KeyStorage` | Your class to keep Key used in encrypt/decrypt data
$user | `IntegrationUser` | User who want to update in 2fas


#### Example

```php
$user = $twoFAS->getIntegrationUserByExternalId($keyStorage, '468');
$user
    ->setActiveMethod('totp')
    ->setTotpSecret('...')
    //...
$user = $twoFAS->updateIntegrationUser($keyStorage, $user);
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\IntegrationUser](#integrationuser) object

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/IntegrationUserNotFoundException.md)
!include (exception/ValidationException.md)

## deleteIntegrationUser

Used for delete integration user from 2fas.

#### Parameters
Name | Type | Description
--- | --- | ---
$userId | `string` | Id of integration user who wants to delete

#### Example

```php
$user = $twoFAS->deleteIntegrationUser('5788b5e5002f0');
```

#### Response

###### Successful

##### Returns boolean (true)

###### Unsuccessful

Method can throw additional exceptions:

!include (exception/IntegrationUserNotFoundException.md)

## regenerateBackupCodes

Used for generating new backup codes for [Integration Users](#integrationuser)

#### Parameters
Name | Type | Description
--- | --- | ---
$user | `IntegrationUser` | User who want to get new backup codes


#### Example

```php
$backupCodes = $twoFAS->regenerateBackupCodes($user);
```

#### Response

###### Successful

##### Returns [TwoFAS\Api\BackupCodesCollection](#backup-codes-collection) object

## getStatistics

Used for displaying [Statistics](#statistics).

#### Example

```php
$statistics = $twoFAS->getStatistics();

if ($statistics->getTotal() > 10) {

}
```

#### Response

###### Successful

##### Returns [Statistics](#statistics).

# Helpers

!include(helper/QrCodeGenerator.md)
!include(helper/Dates.md)

# Objects

!include(object/IntegrationUser.md)
!include(object/FormattedNumber.md)
!include(object/Code.md)
!include(object/Authentication.md)
!include(object/AuthenticationCollection.md)
!include(object/BackupCode.md)
!include(object/BackupCodesCollection.md)
!include(object/Statistics.md)

# More about exceptions

!include(exception/more/ValidationException.md)