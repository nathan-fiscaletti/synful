# Function Libraries

You can add custom function libraries in `src/Synful/App/Functions`.

> Note: These function libraries will be loaded before anything else.

---

**Example**

`src/App/Functions/MyFunctionLib.php`

```php
<?php

if (! function_exists('my_custom_function')) {
    /**
     * A custom function
     */
    function my_custom_function()
    {
        return 'this is a custom function';
    }
}
```

You can now call `my_custom_function` from anywhere in your application.

---
Next: [CORS](./Cors.md) - ([Back to Index](./README.md))