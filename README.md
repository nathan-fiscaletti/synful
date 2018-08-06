<p align='center'>
	<img src='https://github.com/nathan-fiscaletti/synful/blob/master/Logo.jpg?raw=true' /><br />
	<a href="https://styleci.io/repos/66602627"><img src="https://styleci.io/repos/66602627/shield?style=flat" alt="StyleCI" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/v/stable?format=flat" alt="Latest Stable Version" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/v/unstable?format=flat" alt="Latest Unstable Version" /></a>
<a href="https://packagist.org/packages/nafisc/synful"><img src="https://poser.pugx.org/nafisc/synful/license?format=flat" alt="License" /></a>
</p>

<p align='center'>
<i><a href='https://github.com/nathan-fiscaletti/synful/wiki/Credits' _target='top'>Credits</a></i>
</p>

---

### What is it?		
Synful is a simple PHP framework that gives you the tools to create a custom web API in minutes.
		
### How can I get it?		
Head over to [The Wiki Pages](http://github.com/nathan-fiscaletti/synful/wiki) for information on how to get Synful and what the next steps are to get your custom API up and running!

### Benchmark

On a Vagrant box with 4096MB RAM, 4x CPU, running a LAMP stack using the `GetIpExample.php` Request Handler.

```
$ sudo ab -t 60 -c 5 http://127.0.0.1/example/getip
...
Requests per second:    6545.17 [#/sec] (mean)
```

### WiKi

Synful has a full wiki set up with instructions and documentation on the framework available here: [http://github.com/nathan-fiscaletti/synful/wiki](http://github.com/nathan-fiscaletti/synful/wiki)

#### Preview (RequestHandler)

```php
namespace Synful\App\RequestHandlers;

/**
 * Example RequestHandler.
 */
class GetIPExample extends \Synful\Util\Framework\RequestHandler
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
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(\Synful\Util\Framework\Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
```
