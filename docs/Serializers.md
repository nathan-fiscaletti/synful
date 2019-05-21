# Serializers

Serializers can be used to modify the formatting of requests and responses.

## Creating a Serializer

```shell
$ ./synful -create-serializer SerializerName
```

A file will be created in `./src/App/Serializers`.

> Alternately, you can create a PHP file in `./src/App/Serializers` using the template in `./templates/Synful/Serializer.tmpl` as an example.

## Implenting the Serializer interface

Each Serializer has two functions that must be overridden, and one property.

* `$content_type` - This is the content type header that will be set when responding.
* `serialize` - The serialize function is used to serialize an array into a string.
* `deserialize` - The deserialize function is used to de-serialize a string back to an array.

See [JSONSerializer](../src/Synful/Serializers/JSONSerializer.php) for an example of a Serializer implementation.

## Applying a custom serializer.

> Note: By default any `GET` request will forcibly use the [URLSerializer](../src/Synful/Serializers/URLSerializer.php) Serializer for *input* deserialization, regardless of what you have configured. This allows us to properly parse the parameters in the URL. -- (See [PR #205](https://github.com/nathan-fiscaletti/synful/pull/205)). The default serializer or whichever you have configured will still be used for output.

### Globally

To apply a Serializer globally, modify the `serializer` property of the `./config/system.yaml` configuration file.

```yaml
serializer: "App\\Serializers\\MySerializer"
```

### Per-Route

To apply a Serializer to a specific route, modify the `serializer` property of your route definition in the `./config/routes.yaml` configuration file.

```yaml
my/route:
  method: GET
  controller: "App\\Controllers\\ExampleController@someAction"
  serializer: "App\\Serializers\\MySerializer"
```

> Note: This will override the default serializer stored in System.php.

### Overriding Response Serializer

You can override the Serializer of a `\Synful\Framework\Response` object before returning it to the user.
This way, you can use the Serializer configured for the Route for Request input, and a different Serializer for Response Output. 
This example Controller action will accept `JSON` requests, but respond with `CSV`.

```yaml
my/route:
  method: GET
  controller: "App\\Controllers\\ExampleController@someAction"
  serializer: "Synful\\Serializers\\JSONSerializer"
```

```php
public function someAction(Request $request)
{
    $response = sf_response(200, [ 'hello there' ]);
    $response->setSerializer(new \Synful\Serializers\CSVSerializer);

    return $response;
}
```

---
Next: [Templating](./Templating.md) - ([Back to Index](./README.md))