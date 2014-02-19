<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module;

use Everon\Dependency;
use Everon\Rest\Dependency as RestDependency;
use Everon\Http;
use Everon\Interfaces\FactoryWorker;

/**
 * @method FactoryWorker getFactoryWorker()
 * @method Http\Interfaces\Response getResponse()
 */
abstract class Rest extends \Everon\Module implements Interfaces\Rest
{
    use Dependency\Injection\Request;
    use Dependency\Injection\Response;
    use RestDependency\ApiKey;


    public function setup2()
    {
        $this->authenticateRequest();
        
        if ($this->ApiKey === null) {
            throw new Http\Exception\Unauthorized('Invalid ApiKey');
        }
    }

    protected function authenticateRequest()
    {
        $user = $this->getRequest()->getServerCollection()->has('PHP_AUTH_USER') ? $this->getRequest()->getServerCollection()->get('PHP_AUTH_USER') : '';
        $secret = $this->getRequest()->getServerCollection()->has('PHP_AUTH_PW') ? $this->getRequest()->getServerCollection()->get('PHP_AUTH_PW') : '';

        if (trim($user) === '' || trim($secret) === '') {
            //header('WWW-Authenticate: Basic realm="REST API"');
            //header('HTTP/1.1 401 Unauthorized');
            $this->getResponse()->addHeader('WWW-Authenticate', 'Basic realm="REST API"');
            throw new Http\Exception\Unauthorized('Invalid username or password');
        }

        //find user+pass in database 
        $this->ApiKey = $this->getFactory()->buildRestApiKey($user, $secret);
    }
}