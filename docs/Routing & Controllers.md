# Routing & Controllers

## Routing

You will define all of the routes for your application in the `config/routes.yaml` file. The most basic Synful routes simply take a URI, a method and a controller.

```yaml
my/route:
  method: GET
  controller: "App\\Controllers\\Example@someController"
```

## Middleware

You can apply middleware on a per route basis when configuring your routes. See [Middleware](./Middleware.md) for more information.

## Route Parameters

Of course, sometimes you will need to capture segments of the URI within your route. For example, you may need to capture a user's ID from the URL. You may do so by defining route parameters:

```yaml
user/{id}:
  method: GET
  controller: "App\\Controllers\\Example@getUser"
```

You may define as many route parameters as required by your route:

```yaml
posts/{post_id}/comments/{comment_id}:
  method: GET
  controller: "App\\Controllers\\Example@getComment"
```

To retrieve the route parameters, you can use the `->field($key)` method of the `\Synful\Framework\Request` class.

```php
$post_id = $request->field('post_id');
```

## Serializers

Each route can be configured to accept data only of a specific format. You do this by configuring a serializer.

```yaml
my/route:
  method: GET
  controller: "App\\Controllers\\Example@someController"
  serializer: "Synful\\Serializers\\CSVSerializer"
```

> See [Serializers](./Serializers.md) for more information.

## Controllers

Controllers can group related HTTP request handling logic into a class. Controllers are stored in the `App/Controllers` directory.

Here is an example of a basic controller class. All Synful controllers should implement the base Controller interface included with the default Synful installation.

```php
<?php

namespace App\Controllers;

use Synful\Framework\Controller;

final class Example implements Controller
{
    /**
     * Example: Retrieve a clients IP address.
     *
     * @see routes.yaml - /example/ip
     *
     * @param \Synful\Framework\Request         $request
     * @return \Synful\Framework\Response|array
     */
    public function getIp(\Synful\Framework\Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
```

You can point a route at this controller action like so:

```yaml
ip/get:
  method: GET
  controller: "App\\Controllers\\Example@getIp"
```

## Using the `Request` object

Each controller action is passed an instance of `\Synful\Framework\Request` with information regarding the current request.

You can access any data sent from the client using this `$request` object. To access keys sent by the user, use the `->input()` method from the Request. This function takes a mapped dot delimited key for the input. For example, say this request was sent to a controller action on your web application:

```json
{
    "document": {
        "people":[
            {
                "name":"Nathan"
            }
        ]
    }
}
```

We could then access this data like so: 

```php
$name = $request->input('document.people.0.name');
```

> Note: You can also use the `->inputs()` method to retrieve all inputs as an array.

## Responding 

Each action on your controller should either return an array or an instance of `\Synful\Framework\Response`. These responses can be created using the helper function `sf_response(code, data)`.

> Note: When you return an array from your action, HTTP code 200 will be assumed.

```php
public function getIp(\Synful\Framework\Request $request)
{
    return sf_response(
        200,
        [
            'ip' => $request->ip,
        ]
    );
}
```

You can also override the Serializer of a response itself. This way, you could force the action to accept JSON request input, but output a CSV response.

```php
public function getIp(\Synful\Framework\Request $request)
{
    $response = sf_response(
        200,
        [
            'ip' => $request->ip,
        ]
    );

    $response->setSerializer(new \Synful\Serializers\CSVSerializer);
}
```

> See [Serializers](./Serializers.md) for more information.

---
Next: [Middleware](./Middleware.md) - ([Back to Index](./README.md))
