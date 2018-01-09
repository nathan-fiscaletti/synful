# Synful API Framework
> A simple framework for creating your own API

[![StyleCI](https://styleci.io/repos/66602627/shield?style=flat)](https://styleci.io/repos/66602627)		
[![Latest Stable Version](https://poser.pugx.org/nafisc/synful/v/stable?format=flat)](https://packagist.org/packages/nafisc/synful)
[![Latest Unstable Version](https://poser.pugx.org/nafisc/synful/v/unstable?format=flat)](https://packagist.org/packages/nafisc/synful)
[![GitHub issues](https://img.shields.io/github/issues/nathan-fiscaletti/synful.svg)](https://github.com/nathan-fiscaletti/synful/issues)
[![GitHub stars](https://img.shields.io/github/stars/nathan-fiscaletti/synful.svg)](https://github.com/nathan-fiscaletti/synful/stargazers)
[![License](https://poser.pugx.org/nafisc/synful/license?format=flat)](https://packagist.org/packages/nafisc/synful)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/nathan-fiscaletti/synful.svg?style=social)](https://twitter.com/intent/tweet?text=Check%20this%20out!:&url=https%3A%2F%2Fgithub.com%2Fnathan-fiscaletti%2Fsynful)
		
### What is it?		
Synful is a simple PHP framework that will allow you to create a customized API for any project you want. 

### Features
* Access to multiple databases at once
* Customized API Endpoints (Request Handlers)
* Advanced flatfile logging
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
