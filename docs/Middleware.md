```
my/route:
  method: GET
  controller: "App\\Controllers\\Example@someController"
  middleware:
    - "\\Synful\\Middleware\\RateLimit"
  rate_limit:
    method: "ip"
    requests: 3
    seconds: 10
```

When you apply middleware to a route, you need to also fill in the properties for that middleware using 