# Cross Origin Resource Sharing (CORS)

Cross-Origin Resource Sharing (CORS) is a mechanism that uses additional HTTP headers to tell a browser to let a web application running at one origin (domain) have permission to access selected resources from a server at a different origin.[⁽¹⁾](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)

## Configure CORS with Synful

You can configure CORS on a per-route basis in Synful using the [Cors](../src/Synful/Middleware/Cors.php) Middelware.

> See [Routing & Controllers](./Routing%20%26%20Controllers.md)

```yaml
my/route:
  method: "POST"
  controller: "App\\Controllers\\SomeController@someAction"
  middleware:
    - "Synful\\Middleware\\Cors"
  cors:
    domains:
      - "*"
```

> The `domains` property under the `cors` Middleware property header should contain a list of domains allowed to use CORS on this Route. Alternately, you can provide `*` to allow any domain to access it.

---
Next: [](./Downloads.md) - ([Back to Index](./README.md))


