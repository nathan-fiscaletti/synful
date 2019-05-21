# Rate Limits

You can use rate limiting with Synful to control at what rate consumers can access certain portions of your application.


> ⚠ **You must install the `php-apcu` package to enable Rate Limiting in Synful.**

Synful uses the [Token Bucket Algorithm](https://en.wikipedia.org/wiki/Token_bucket) for handling rate limiting.

## Rate Limit Types

You can configure what sections have rate limiting applied under the `./config/rate.yaml` configuration file.

**Areas that currently supporte Rate Limiting**

|Area|Application|Effect|
|---|---|---|
|Global|`rate.yaml`|All requests from a specific IP|
|Per-Route|[RateLimit Middlware](../src/Synful/Middleware/RateLimit.php)|All requests from a specific IP to a specific Route.|
|Per-API-Key|[RateLimit Middlware](../src/Synful/Middleware/RateLimit.php)|All requests from a specific IP using a specific API key directed at a specific route.|

* You can configure the `Global` rate limit from within the `./config/rate.yaml` configuration file.
* To configure `API Key` and `Route` rate limits, use the route definitions in `./config/routes.yaml` to add the [RateLimit Middlware](../src/Synful/Middleware/RateLimit.php).

## Applying Rate Limits

**Applying globally** (`rate.yaml`)
```yaml
global: true
global_rate_limit:
  requests: 1
  per_second: 5
```

**Applying based on route** (`routes.yaml`)
```yaml
my/route:
  method: POST
  controller: "App\\Controllers\\CustomController@someAction"
  middleware:
    - "Synful\\Middleware\\RateLimit"
  rate_limit:
    method: "route"
    requests: 1
    seconds: 5
```

**Applying based on API Key** (`routes.yaml`)
```yaml
my/route:
  method: POST
  controller: "App\\Controllers\\CustomController@someAction"
  middleware:
    - "Synful\\Middleware\\APIKeyValidation"
    - "Synful\\Middleware\\RateLimit"
  api_key:
    security_level: 4
  rate_limit:
    method: "api_key"
    requests: 1
    seconds: 5
```

## Rate Limit Error Codes

|Code|Error|
|---|---|
|`1028`|Global rate limit exceeded.|
|`1029`|Route rate limit exceeded.|
|`1030`|API Key rate limit exceeded.|
|`1031`|Attempting to load APCu for RateLimit but `php-apcu` extension not installed.|

---
Next: [Function Libraries](./Function%20Libraries.md) - ([Back to Index](./README.md))