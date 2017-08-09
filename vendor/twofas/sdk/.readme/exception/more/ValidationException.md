## more ValidationException

Validation exceptions may contain multiple keys and rules.
For simplicity of integrating this exception has few methods:

#### Methods
Name | Type | Description
--- | --- | ---
getErrors() | `array` | Returns all errors as constants
getError($key) | `array|null` | Returns all failing rules for key (as constants), or null if key passes validation
getBareError($key) | `array|null` | Returns all failing rules for key (as bare strings), or null if key passes validation
hasKey($key) | `boolean` | Check if certain field failed validation
hasError($key, $rule) | `boolean` | Check if certain key failed specified rule
