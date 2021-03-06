<p align='center'>
	<img src='./Logo.png' /><br />
	<a href="https://styleci.io/repos/66602627"><img src="https://styleci.io/repos/66602627/shield?style=flat" alt="StyleCI" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/v/stable?format=flat" alt="Latest Stable Version" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/v/unstable?format=flat" alt="Latest Unstable Version" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/license?format=flat" alt="License" /></a>
</p>

<p align='center'>
<i><a href='https://github.com/nathan-fiscaletti/synful/wiki/Credits' _target='top'>Credits</a></i>
</p>

---

> This repository has been archived.

## What is it?		
Synful is a simple PHP framework that gives you the tools to create a custom web API in minutes.
		
## How can I get it?		
Head over to [The Wiki Pages](http://github.com/nathan-fiscaletti/synful/wiki) for information on how to get Synful and what the next steps are to get your custom API up and running!

## Benchmark

On a Vagrant box with 4096MB RAM, 4x CPU, running a LAMP stack using the `GetIpExample.php` Request Handler.

```
$ sudo ab -t 60 -c 5 http://127.0.0.1/example/getip
...
Requests per second:    6545.17 [#/sec] (mean)
```

## Preview (RequestHandler)

```php
namespace App\RequestHandlers;

use \Synful\Framework\RequestHandler;
use \Synful\Framework\Request;

/**
 * Example RequestHandler.
 */
class GetIPExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/getip';

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function get(Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
```
