## Dates

Dates object helps converting API date to DateTime object with correct
time and timezone.

#### Methods
Name | Type | Description
--- | --- | ---
convertUTCFormatToLocal($date) | `DateTime` | Converts date format to DateTime

#### Usage
```php
$date     = '2017-01-18 14:21:51';
$dateTime = Dates::convertUTCFormatToLocal($date);
```
