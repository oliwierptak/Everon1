# Everon v0.01
Very alpha version

## Requirements
* Php 5.4+ only

## Features
* 99% code coverage
* One line, lazy loaded dependency injection (via setters or constructors)
* Minimized file/memory access/usage due to callbacks and lazy load
* Factory gives full control on how each and every object is created and what dependencies it needs
* Almost 'invisible' framework in business layer
* Advanced, easy to configure routing, with build-in validators  
* Friendly urls with custom parameters and validation
* No default parameters in method calls, everything is implicit
* No static classes or methods
* Clear, small and simple API
* Convention over configuration

## Dependency Injection
Consider this model, as you can see it does not inherit from anything, and there is no constructor.

```php
class MyModel
{
    public function helloWorld()
    {

    }    
}
```
    
Let's say you need a logger for that particular model. All you must write is one line. Everon does the rest.

```php
class MyModel
{
    use Dependency\Injection\Logger;
        
    public function helloWorld()
    {
        $this->getLogger()->log('Hello Log');
    }
}
```
If you need specific constructor injection you can extend default Factory class.
 
One line, on demand, lazy loaded dependency injection. No annotations, yaml or xml files to configure.
In fact, there isn't any configuration file needed, at all. 
Instead, Everon applications use [root composition pattern](http://blog.ploeh.dk/2011/07/28/CompositionRoot/) to create
whole object graphs in one place. See [index.php](https://github.com/oliwierptak/Everon/blob/master/Web/index.php)
for implementation details.

#### What's the best way to inject dependencies?
Use constructor for dependencies that are part of what the class is doing, and use setters/getters for infrastructure
type dependencies. In general, a Logger could be good example of infrastructure type dependency.


## Factory
One Factory class to take care of creation of all objects.   
You have full control how classes are instantiated and what dependencies they require,
all you have to do is to extend default Factory class.


## Routing
Consider this routing example.

    [login_submit]
    url = '%application.url%login/submit/session/{sid}/redirect/{location}?and=something&else={else}'
    controller = Login
    action = submit
    get[sid] = '[a-z0-9]+'
    get[location] = '[[:alnum:]|%]+'
    get[and] = '[a-z]+'
    get[else] = '[0-9]+'
    post[username] = '[a-z]{3,16}'
    post[password] = '[[:alnum:]]{4,22}'
    
It could be translated into commands for Everon:
* Take `url` from application.ini and replace `%application.url%` with it.
* Make sure `sid` parameter in URL consists lower cased letters and numbers only.
* Make sure `location` parameter in URL consists alphanumerical and % symbol only.
* Make sure that parameter `and` in _GET consists lower cased letters only.
* Make sure that parameter `else` in _GET consists numbers only.
* Make sure that parameter `username` in _POST consists only lower cased letters and is not less then 3 nor longer 
  then 16 characters.
* Make sure that parameter `password` in _POST consists only alphanumerical symbols and is not less then 4 nor longer
  then 22 characters.

Unless all those conditions are met, the request won't pass and error exception will be thrown.
Of course you can write your own regular expressions. See `router.ini` for more examples.

## Config inheritance
Not only one config can use values from another file (by using `$config_name.value_name%` notation), 
the config sections can be inherited. 
Consider this ini example:

    [Default]
    Title = 'Welcome to Everon'
    Lang = 'en-US'
    StaticUrl = '%application.url%static/default/'
    Charset = 'UTF-8'
    Keywords = 'Everon'
    Description = 'Everon: PHP 5.4+ framework'
    
    [Account < ThemeBlue]
    Title = 'Your Account'
    Description = 'Account'
    
    [ThemeBlue]
    StaticUrl = '%application.url%static/blue/'
    
The first item is special, its name does not matter, however all of its values will be used as defaults.
The order of the items below is irrelevant.

```php
$title = $Config->go('Account')->get('Charset');
```

`$title` value will be set to `UTF-8`, even so it is not defined in the `[Account]`.
It has been inherited from the `[Default]` section instead. 