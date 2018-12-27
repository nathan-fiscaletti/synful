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

use \Synful\Util\Framework\RequestHandler;
use \Synful\Util\Framework\Request;

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
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
```

### License

***Available Under the MIT License***

>Copyright (c) 2016-2018 Nathan Fiscaletti
>                    
>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
                    
>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
                    
>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
