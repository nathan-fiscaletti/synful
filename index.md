
<p align='center'>
	<img src='https://github.com/nathan-fiscaletti/synful/blob/gh-pages/Logo.png?raw=true' />
</p>

[![StyleCI](https://styleci.io/repos/66602627/shield?style=flat)](https://styleci.io/repos/66602627)		
[![Latest Stable Version](https://poser.pugx.org/nafisc/synful/v/stable?format=flat)](https://packagist.org/packages/nafisc/synful)		
[![Latest Unstable Version](https://poser.pugx.org/nafisc/synful/v/unstable?format=flat)](https://packagist.org/packages/nafisc/synful)		
[![License](https://poser.pugx.org/nafisc/synful/license?format=flat)](https://packagist.org/packages/nafisc/synful)		 		
		
### What is it?		
Synful is a simple PHP framework that will allow you to create a customized API for any project you want. 

### Features
* Access to multiple databases at once
* Customized API Endpoints (Request Handlers)
* Per-Endpoint API key whitelisting
* Public Endpoints running along side private endpoints
* Much, much more!
		
### How can I get it?		
Head over to [The Wiki Pages](http://github.com/nathan-fiscaletti/synful/wiki) for information on how to get Synful and what the next steps are to get your custom API Framework up and running!

### Benchmark

On a Vagrant box with 4096MB RAM, 4x CPU, running a LAMP stack using the `GetIpExample.php` Request Handler.

```
$ sudo ab -t 60 -c 5 http://127.0.0.1/example/getip
...
Requests per second:    6545.17 [#/sec] (mean)
```
