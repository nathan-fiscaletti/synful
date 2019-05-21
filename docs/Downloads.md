# Downloads

You can make a response download as a file using the following from within a Controller action:

```php
return sf_response(
    200, [
        'data' => 'This is the content of the file'
    ]
)->downloadableAs('filename.txt');
```

This will set the Response Serializer to an instance of [DownloadSerializer](..//src/Synful/Serializers/DownloadSerializer.php) and append a `Content-disposition` header to the response.

> See [Example.php `download` function](../src/App/Controllers/Example.php#L155) and [Response.php `downloadableAs` function](../src/Synful/Framework/Response.php#L105)

