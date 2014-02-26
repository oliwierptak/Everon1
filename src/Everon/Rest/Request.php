<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Helper;
use Everon\Exception;

class Request extends \Everon\Request implements Interfaces\Request
{
    protected $accepted_methods = [
        self::METHOD_DELETE,
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_POST,
        self::METHOD_PUT,
    ];
    
    protected $versioning = Resource\Manager::VERSIONING_URL;
    
    protected $version = 'v1';
    

    protected function initRequest()
    {
        $this->overwriteEnvironment();
        parent::initRequest();
    }

    /**
     * @return array
     */
    protected function overwriteEnvironment()
    {
        if ($this->versioning === Resource\Manager::VERSIONING_URL) { //remove version from url
            $query_string = $this->ServerCollection['QUERY_STRING'];
            $this->ServerCollection['QUERY_STRING'] = str_replace('param='.$this->version, '', $query_string);
            
            $query_string = $this->ServerCollection['REDIRECT_QUERY_STRING'];
            $this->ServerCollection['REDIRECT_QUERY_STRING'] = str_replace('param='.$this->version, '', $query_string);

            $request_uri = $this->ServerCollection['REQUEST_URI'];
            $this->ServerCollection['REQUEST_URI'] = str_replace('/'.$this->version, '', $request_uri);
            
            $request_uri = $this->ServerCollection['REDIRECT_URL'];
            $this->ServerCollection['REDIRECT_URL'] = str_replace('/'.$this->version, '', $request_uri);
            
            return;
        }
    }
}
