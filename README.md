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
* Clear, small and simple API

## Dependency Injection
Consider this model, as you can see it does not inherit from anything, and there is no constructor.

    <?php
    class MyModel
    {
        public function helloWorld()
        {

        }    
    }
    
Let's say you need a logger for that particular model. All you must write is one line. Everon does the rest.

    <?php
    class MyModel
    {
        use Dependency\Injection\Logger;
            
        public function helloWorld()
        {
            $this->getLogger()->log('information');
        }
    }

One line, on demand, lazy loaded dependency injection.
If you need specific constructor injection you can extend default Factory class. 
 
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
* Take 'url' from application.ini and replace %application.url% with it.
* Make sure 'sid' parameter in URL consists lower cased letters and numbers only.
* Make sure 'location' parameter in URL consists alphanumerical and % symbol only.
* Make sure that parameter 'and' in _GET consists lower cased letters only.
* Make sure that parameter 'else' in _GET consists numbers only.
* Maker sure that parameter 'username' in _POST consists only lower cased letters and is not less then 3 and longer 
  then 16 characters.
* Maker sure that parameter 'password' in _POST consists alphanumerical symbols and is not less then 4 and longer
  then 22 characters.

Unless all those conditions are met, the request won't pass and error exception will be thrown.
Of course you can write your own regular expressions. See router.ini for more examples.


