## Code interface

Code object is returned by [checkCode](#checkcode) method.

It is a [Value Object](https://en.wikipedia.org/wiki/Value_object) with three methods:

#### Methods
Name | Type | Description
--- | --- | ---
authentications() | `array` | Array of authentication ids
accepted() | `boolean` | Result of code checking
canRetry() | `boolean` | Ability to use same ids again


#### Usage
```php
$code->accepted();
$code->authentications();
$code->canRetry();
```
