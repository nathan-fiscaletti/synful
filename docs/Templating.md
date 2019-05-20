# Templating

Synful makes use of Spackle as a Templating Engine. See the main [Spackle Repository](https://github.com/nathan-fiscaletti/spackle) for more information and documentation.

Templates should be stored in the `./templates/App` directory.

## Enabling / Disabling Templating

You can enable or disable templating as you see fit for your Application using the `templating.enabled` configuration in the `./config/templating.yaml` configuration file. (See [Configuration](./Configuration.md))

## Creating a Template

Create a new template file in the `./templates/App` directory, and call it `Example.html`.

> Note: Template files do not require the `.html` extension. You can use any extension you would like.

```html
<html>
    <p>Hello, {{name}}.</p>
</html>
```

To parse this template in a Controller action, simply return an instance of `\Synful\Templating\Template`.

```php
/**
 * Example: Display an HTML template.
 * 
 * @see routes.yaml - /example/template
 *
 * @param \Synful\Framework\Request $request
    * @return \Synful\Framework\Response|array
    */
public function template(\Synful\Framework\Request $request)
{
    $name = $request->input('name') == null
                ? 'John Doe'
                : $request->input('name');

    return new \Synful\Templating\Template(
        'Example.html',
        [
            'name' => $name // cannot be null
        ]
    );
}
```

## Substitutions & Code Blocks

You can use substitutions and codeblocks anywhere within your Template file.

### Substitutions

Substitutions are wrapped in double curly braces, notated like `{{my_substitution}}`.
```html
<html>
    Hello, {{name}}!
</html>
```

### Code Blocks

Code blocks are notated like `{{> ... <}}` and can directly execute PHP code.
```html
<html>
    {{>
        echo "This is running raw PHP code.";
    <}}
</html>
```

> Note: If you want to bind a code block to a specific object so that you can access information from that object, you can do so using `$template->bind($object);`

### URLs

URLs are notated like `{{url ... <}}` and use the `system.site` configuration entry. (See [Configuration](./Configuration.md))
```html
<html>
    <a href="{{url settings/account <}}">Account Settings</a>
</html>
```

### Configuration Entries

You can also retrieve information from Synful's configuration files using `{{conf some.key <}}`. (See [Configuration](./Configuration.md))
```html
<html>
    Production: `{{conf system.production <}}`
</html>
```

## Plugins

You can also create custom plugins for spackle to parse data in anyway you want.

To create a plugin, run the following command: `./synful -ctp MyTemplatePluginName somekey`. Alternately, you can manually create the plugin. Create a new PHP file in the `./src/App/Templating/Plugins` directory using the template stored in `./templates/Synful/TemplatPlugin.tmpl` as an example.

**Properties / Methods**

|Property / Method|Use|
|---|---|
|`key`|The key notating the beginning of the element.|
|`parse($data)`|Parses the value for the element matching this plugin.|

A finished template plugin might look something like this:

```php
<?php

namespace App\Templating\Plugins;

use Spackle\Plugin;

class MyTemplatePlugin extends Plugin
{
    public $key = 'print';

    public function parse($data)
    {
        // Return the parsed data.
        return $data;
    }
}
```

In order to activate the Template plugin, you need to register it in the `./config/templating.yaml` file.
```yaml
plugins:
  - "\\App\\Templating\\Plugins\\MyTemplatePlugin"
```

It can now be used in any template file as follows:

```html
<html>
    Hello, {{print nathan <}}!
</html>
```

Which will come out to

```
Hello, nathan!
```

## Parsing data that is not intended as a response

You can also directly parse data, if you so wish. 

```php
$template = new \Synful\Templating\Template(
    'Example.html',
    [
        'name' => $name,
    ]
)

$parsed = $template->parse();
```

---
Next: [Rate Limits](./Rate%20Limits.md) - ([Back to Index](./README.md))