# Connecting

`$userZone = new \TwoFAS\UserZone\UserZone($httClient);`

`$httpClient` can be any HTTP client which implements `\TwoFAS\UserZone\HttpClient\HttpClient` interface.

# Methods

## createClient

### Parameters

Type | Name | Description
--- | --- | ---
string | $email | Valid e-mail address
string | $password | Client's password
string | $passwordConfirmation | Confirmation of the client's password
string | $phone | Valid phone number

### Example

`$client = $userZone->createClient('client@example.com', 'pass123', 'pass123', '14157012311');`

### Response

#### Successful

Returns [\TwoFAS\UserZone\Client](#client) object.

#### Unsuccessful

Throws exception `\TwoFAS\UserZone\Exception\ValidationException` without any message.

## creteIntegration

### Parameters

Type | Name | Description
--- | --- | ---
string | $name | Integration name
int | $authMaxMinutes | How long authentication code is valid
int | $authMaxAttempts | Number of allowed attempts to make authentication
array | $credentials | Client's e-mail and password

### Example

`$integration = $userZone->createIntegration('my-website', 15, 3, array('email' => 'client@example.com', 'password' => 'pass123'));`

### Response

#### Successful

Returns [\TwoFAS\UserZone\Integration](#integration) object.

#### Unsuccessful

Throws exception `\TwoFAS\UserZone\Exception\ValidationException` without any message
or throws exception `\TwoFAS\UserZone\Exception\AuthorizationException` with message `'invalid_credentials'`.

## createKey

### Parameters

Type | Name | Description
--- | --- | ---
int | $integrationId | ID of the integration
string | $name | Key's name
array | $credentials | Client's e-mail and password

### Example

`$key = $userZone->createKey($integration->getId(), 'Production key', array('email' => 'client@example.com', 'password' => 'pass123'));`

### Response

#### Successful

Returns [\TwoFAS\UserZone\Key](#key) object.

#### Unsuccessful

Throws exception `\TwoFAS\UserZone\Exception\ValidationException` without any message
or throws exception `\TwoFAS\UserZone\Exception\AuthorizationException` with message `'invalid_credentials'`
or throws exception `\TwoFAS\UserZone\Exception\PrimaryCardRequiredException` with message `'Primary card required'`.

## setBaseUrl

### Parameters

Type | Name | Description
--- | --- | ---
string | $url | UserZone's API URL

### Example

`$userZone = $userZone->setBaseUrl('http://userzone.api');`

### Response

Returns `\TwoFAS\UserZone\UserZone` instance allowing method chaining.

# Objects

All objects are in `\TwoFAS\UserZone` namespace.

## Client

### Methods

Returned type | Name | Description
--- | --- | ---
int | getId() | Client's ID
string | getEmail() | Client's e-mail
string | getPhone() | Client's phone

## Integration


### Methods

Returned type | Name | Description
--- | --- | ---
int | getId() | Integration's ID
string | getLogin() | Integration's login

## Key

### Methods

Returned type | Name | Description
--- | --- | ---
string | getToken() | Key's token