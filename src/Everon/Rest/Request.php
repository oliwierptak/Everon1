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
    
    protected $version = null;

    /**
     * @param array $server $_SERVER
     * @param array $get $_GET
     * @param array $post $_POST
     * @param array $files $_FILES
     * @param array $versioning
     */
    public function __construct(array $server, array $get, array $post, array $files, $versioning)
    {
        $this->versioning = $versioning;
        parent::__construct($server, $get, $post, $files);
    }

    protected function initRequest()
    {
        $this->setupVersion();
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
            $this->ServerCollection['_QUERY_STRING'] = $query_string;
            $this->ServerCollection['QUERY_STRING'] = str_replace('param='.$this->version, '', $query_string);
            
            $query_string = $this->ServerCollection['REDIRECT_QUERY_STRING'];
            $this->ServerCollection['_REDIRECT_QUERY_STRING'] = $query_string;
            $this->ServerCollection['REDIRECT_QUERY_STRING'] = str_replace('param='.$this->version, '', $query_string);

            $request_uri = $this->ServerCollection['REQUEST_URI'];
            $this->ServerCollection['_REQUEST_URI'] = $request_uri;
            $this->ServerCollection['REQUEST_URI'] = str_replace('/'.$this->version, '', $request_uri);
            
            $request_uri = $this->ServerCollection['REDIRECT_URL'];
            $this->ServerCollection['_REDIRECT_URL'] = $request_uri;
            $this->ServerCollection['REDIRECT_URL'] = str_replace('/'.$this->version, '', $request_uri);
        }
    }
    
    protected function setupVersion()
    {
        switch ($this->versioning) {
            case Resource\Manager::VERSIONING_URL:
                $url = $this->ServerCollection['REQUEST_URI']; //eg. http://api.localhost/v1/accounts/C22222C/settings
                $tokens = explode('/', $url);
                $this->version = $tokens[1];
                break;

            case Resource\Manager::VERSIONING_HEADER:
                //todo finish me
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->version;
    }
}
