# Everon v0.01
Very alpha version

## Requirements
* Php 5.5+ only

## Features
* One line, lazy loaded dependency injection (via setters or constructors)
* Minimized file/memory access/usage due to callbacks and lazy load
* Factory gives full control on how each and every object is created and what dependencies it needs
* Almost 'invisible' framework in business layer
* Advanced, easy to configure routing, with build-in validators  
* Friendly urls with custom parameters and validation
* No default parameters in method calls, everything is implicit
* No static classes or methods
* ReflectionAPI only used in tests
* Clear, small and simple API
* Convention over configuration
* Clean code 

## Dependency Injection
Consider this model, as you can see it does not inherit from anything, and there is no constructor.

```php
class Model\Greet
{
    public function helloWorld()
    {

    }    
}
```
    
Let's say you need a logger for that particular model. All you must write is one line. Everon does the rest.

```php
class Model\Greet
{
    use Dependency\Injection\Logger;
        
    public function helloWorld()
    {
        $greeting = 'Hello world';
        $this->getLogger()->log($greeting);
        return $greeting;
    }
}
```
The Logger is accessible by simply calling `$this->getLogger()`.
Now, let's plug this model into a Controller, so we can use it in some incoming user request.
Note: `Mvc\Controller`, which `Controller\Greet` extends from, implements `getView()` and `getModel()`.
[See here for Mvc](https://github.com/oliwierptak/Everon/blob/master/Src/Everon/Mvc/Controller.php) and here 
[for Console](https://github.com/oliwierptak/Everon/blob/master/Src/Everon/Console/Controller.php) examples.

```php
class Controller\Greet extends Mvc\Controller
{
    public function sayHello()
    {
        $greeting = $this->getModel()->helloWorld();
        $this->getView()->setContainer($greeting);
    }
}
```
One line, on demand, lazy loaded dependency injection. No annotations, yaml or xml files to configure.
In fact, there isn't any configuration file needed at all. 
Instead, Everon applications use [root composition pattern](http://blog.ploeh.dk/2011/07/28/CompositionRoot/) to create
whole [object graph in one place](https://github.com/oliwierptak/Everon/blob/master/Src/Everon/Lib/Dependencies.php).
See example [Bootstrap/mvc.php](https://github.com/oliwierptak/Everon/blob/master/Config/Bootstrap/mvc.php)
for implementation details. 

#### What's the best way to inject dependencies?
Use constructor for dependencies that are part of what the class is doing, and use setters/getters for infrastructure
type dependencies. In general, a Logger could be good example of infrastructure type dependency.
If you need specific constructor injection you can extend default Factory class.


## Factory
[One Factory](https://github.com/oliwierptak/Everon/blob/master/Src/Everon/Factory.php) class to take care of creation of all objects.   
You have full control how classes are instantiated and what dependencies they require,
all you have to do is to extend default Factory class.


## Routing
Consider this routing example for an url: `/login/submit/session/adf24ds34/redirect/%2Flogin%2Fresetpassword?token=abcd&pif=2457`
  
    [login_submit]
    url = login/submit/session/{sid}/redirect/{location}
    controller = Login
    action = submit
    query[sid] = '[a-z0-9]+'
    query[location] = '[[:alnum:]|%]+'
    get[token] = '[a-z]+'
    get[pif] = '[0-9]+'
    post[username] = '[a-z]{4,16}'
    post[password] = '[[:alnum:]]{3,22}'
    post[token] = '[0-9]+'    
    
It could be translated into commands for Everon:
* Make sure `sid` parameter in URL consists lower cased letters and numbers only.
* Make sure `location` parameter in URL consists alphanumerical and % symbol only.
* Make sure that parameter `token` in _GET consists lower cased letters only.
* Make sure that parameter `pif` in _GET consists numbers only.
* Make sure that parameter `username` in _POST consists only lower cased letters and is not less then 3 nor longer 
  then 16 characters.
* Make sure that parameter `password` in _POST consists only alphanumerical symbols and is not less then 4 nor longer
  then 22 characters.

Unless all those conditions are met, the request won't pass and error exception will be thrown.
Of course you can write your own regular expressions. See [router.ini](https://github.com/oliwierptak/Everon/blob/master/Config/router.ini) for more examples.


## Config inheritance
Not only one config can use values from another file, the config sections can be inherited. 
Consider this example:

    [Index]
    title = 'Welcome to Everon'
    lang = 'en-US'
    static_url = '%application.env.url%static/default/'
    charset = 'UTF-8'

    [ThemeBlue]
    static_url = '%application.env.url%static/blue/'
    
    ['Account < ThemeBlue']
    title = 'Your Account'
    description = 'Account'
    
The first item is special, its name does not matter, however all of its values will be used as defaults.
The order of the items below is irrelevant.

```php
$static_url = $Config->go('Account')->get('static_url');    # /static/blue
$charset = $Config->go('Account')->get('charset');          # UTF-8
```

`$static_url` is not present in `[Account]`, but because it inherits from `[ThemeBlue]` its value 
for `static_url` property will be set to '/static/blue' as defined in the parent block.
The rest of the missing properties, like `$charset`, will be inherited from `[Default]` section.

See [view.ini](https://github.com/oliwierptak/Everon/blob/master/Config/view.ini) for more examples.

#### Sharable config variables
In Everon, configuration files share their variables with other configuration files, 
by using `%config_name.value_name%` notation. For example, `%application.env.url%s` will grab url from `application.ini`
which is stored under `env` section.


## Flexibility
Due to to effortless dependency injection and modularity in one application, you can specify with surgical 
precision, which parts require what collaborators to do their work.
For example, a console application that requires only one specific Controller to have a View, in order to compose
and send e-mails in HTML format. Or that cron job controller for aggregating data which does not need View at all. 

Maybe for your case, it makes more sense to use the Model View Presenter (MVP) instead of MVC?
No problem, just take Everon's parts and put them together in your way. 