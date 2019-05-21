# Configuration

Synful configurations are stored in within the `./config` directory. All of the default configuration files are YAML formatted, however Synful supports **PHP, INI, JSON and YAML** for configuration files.

> Synful uses [Gestalt](https://github.com/samrap/gestalt) for configuration management.

## First Time Setup

When first configuring a Synful application, you will need to change a few things for them to be to your liking. 

1. Open `system.json` and configure the `site` property with your sites URL. This is used to build URL's when using Templates.
2. If your application depends on a database, you will need to configure the `databases.yaml` file. Read more about database configuration [here](./Database%20Management.md).
3. If you are deploying in a production environment, you will want to change the `production` property within `system.yaml` to true. This tells the system to hide sensitive data that may be displayed in error messages. 

Other configuration properties can be found at the bottom of this document.

## Creating your own Configuration file

Synful supports four major formats for configuration files. **PHP, INI, JSON and YAML**. To create a custom configuration file, add it to the `config` directory. It is best practice to give the file a name with no spaces, and preferablly all lowercase.

Let's use this file as an example:

`./config/myconf.php`
```php
return [
    'client_props' => [
        'max_clients' => 50,
    ],
];
```

This same file in YAML would look like this:

`./config/myconf.yaml`
```yaml
client_props:
    max_clients: 50
```

## Accessing Configuration Values

To access the values stored in a configuration file, you can use the `sf_conf` function from anywhere in your application. This function uses dot notation to separate elements.

```php
echo 'Max Clients is: '.sf_conf('myconf.client_props.max_clients').PHP_EOL;
```

You can also access this configuration value from within a Template file.

```html
<html>
    <body>
        <p>This site supports {{conf myconf.client_props.max_clients <}} clients.
    </body>
</html
```

> (Read more about Templating [here](./Templating.md))


## Synful System Default Configuration

|File|Setting|Effect|
|---|---|---|
|`commandline.yaml`|`commands`|A list of classes that should be registered in the System as commands.|
|`security.yaml`|`cors_enabled`|If set to true, cross origin resources sharing will be enabled. (See [CORS](./Cors.md))|
|`security.yaml`|`cors_domain`|The domains allowed to use cross origin resource sharing. (Use `all` for wildcard.)|
|`security.yaml`|`api_key_cost`|The cost to pass the BCRYPT function used for generating new API keys.|
|`system.yaml`|`production`|If set to true, line numbers and file names will not be output with error messages.|
|`system.yaml`|`site`|The web address to use when generating URLs|
|`system.yaml`|`color`|If set to `true`, the system will enable console color codes when runnin in `Standalone` mode.|
|`system.yaml`|`display_errors`|If set to true, fatal errors will be displayed.|
|`system.yaml`|`pretty_responses`|If set to true, JSON responses will be formatted using JSON_PRETTY_PRINT.|
|`system.yaml`|`allow_pretty_responses_on_get`|If set to true, JSON responses will be formatted using JSON_PRETTY_PRINT when the `pretty` $_GET parameter is passed.|
|`system.yaml`|`serializer`|The default serializer to apply to all requests|

## Additional Configuration Files

* `routes.yaml` (See [Routing & Controllers](./Routing%20%26%20Controllers.md))
* `databases.yaml` (See [Database Management](./Database%20Management.md))

---
Next: [Routing & Controllers](./Routing%20%26%20Controllers.md) - ([Back to Index](./README.md))