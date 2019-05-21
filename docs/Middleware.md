# Middleware

HTTP middleware provide a convenient mechanism for filtering HTTP requests entering your application. For example, Synful includes a middleware that verifies the user of your application is authenticated using an API key. If the user is not authenticated, the request will result in a `401 Unauthorized`, However, if the user is authenticated, the middleware will allow the request to proceed further into the application.

Of course, additional middleware can be written to perform a variety of tasks besides authentication. A CORS middleware might be responsible for adding the proper headers to all responses leaving your application. A logging middleware might log all incoming requests to your application.

## Creating a Middleware

To create a new Middleware class, run the following command:
```shell
$ ./synful -create-middleware MyMiddlewareName
```

This will create a new file in `src/App/Middleware` that implements the Middleware interface.

Alternately, you can create a PHP file yourself in the `./src/App/Middleware` directory using the template in [./templates/Synful/Middleware.tmpl](../templates/Synful/Middleware.tmpl) as an example.

### Implementing the MiddleWare interface

Each MiddleWare implementation should override the `before` and `after` functions. 

* `before` - The before function is called before any Request touches it's Controller. This will give you access to the [\Synful\Framework\Request](../src/Synful/Framework/Request.php) object associated with this request.

* `after` - The after function is called after a Controller has supplied a response to Synful. This will be called directly prior to the Response being sent to the user. It will have access to the [\Synful\Framework\Response](../src/Synful/Framework/Response.php) object. This can be useful for modifying headers on a response, or doing anything else to a response.

> Optionally, you can also provide the `public $key` property if you plan to allow this Middleware to be configured through the `routes.yaml` file. 

### Middleware Properties

You can add configuration properties to your Middleware using the route you configure the Middleware for. 

1. Set the optional `$key` property in your middleware.
2. To access the key from within your middleware, use the `route` property on the `$request` object in the `before` function.
    `$request->route->middlewareProperty($this, 'some_key');`

## Example

A finished middleware implementation should look something like this:
```php
<?php

namespace App\Middleware;

use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\Middleware;

final class MyMiddleware implements Middleware
{
    public $key = 'custom';

    public function before(Request $request)
    {
        if ($request->route->middlewareProperty($this, 'open')) {
            $request->setHeader('Opened', 'Yes');
        }
    }

    public function after(Response $response)
    {
        $response->setHeader('Success', 'Yes');
    }
}
```

Implementing this Middleware in your routes file would look like this:

```yaml
my/route:
  method: GET
  controller: "App\\Controllers\\Controller@someAction"
  middleware:
    - "\\App\\Middleware\\MyMiddleware"
  custom:
    open: true
```

## Pre-Packaged Middleware

Synful comes with a few pre-packaged Middleware implementations for you to use.

* `\Synful\Middleware\RateLimit` - Applies a rate limit to the configured route.
    * Key: `rate_limit`
    * Middleware Properties
        * `method` - The method used to apply this Rate Limit.
            * `api_key` - Apply based on API Key. (Requires the APIKeyValidation middleware)
            * `ip` - Apply based on IP address.

* `\Synful\Middleware\APIKeyValidation` - Requires an API key to use this route. (See [API Key Management](./API%20Key%20Management.md))
    * Key: `api_key`
    * Middleware Properties
        * `security_level` - The security level to allow access to this route.

---
Next: [Serializers](./Serializers.md) - ([Back to Index](./README.md))