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

class Request extends \Everon\Ajax\Request implements Interfaces\Request
{
    use Helper\Asserts\IsStringAndNotEmpty;
    use Helper\Exceptions;
    
    protected $versioning = Resource\Handler::VERSIONING_URL;
    
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
        $post = $this->getRawInput();
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
        if ($this->versioning === Resource\Handler::VERSIONING_URL) { //remove version from url
            $query_string = $this->ServerCollection['QUERY_STRING'];
            $this->ServerCollection['_QUERY_STRING'] = $query_string;
            $get = $this->GetCollection->toArray();
            array_shift($get);
            
            if (empty($get) === false) {
                $query_string = urldecode(http_build_query($get));
            }
            else {
                if (strpos($query_string, '?') !== false) {
                    $query_string = substr($query_string, strpos($query_string, '?'), strlen($query_string));
                }
                else {
                    $query_string = '';
                }
            }
            $this->ServerCollection['QUERY_STRING'] = $query_string;

            $request_uri = $this->ServerCollection['REQUEST_URI'];
            $request_uri_no_version = substr($request_uri, strlen($this->version)+1, strlen($request_uri));
            $this->ServerCollection['_REQUEST_URI'] = $request_uri;
            $this->ServerCollection['REQUEST_URI'] = $request_uri_no_version;
        }
    }
    
    protected function setupVersion()
    {
        switch ($this->versioning) {
            case Resource\Handler::VERSIONING_URL:
                $url = $this->ServerCollection['REQUEST_URI']; //eg. http://api.localhost/v1/accounts/C22222C/settings
                $tokens = explode('/', $url);
                $this->version = $tokens[1];
                break;

            case Resource\Handler::VERSIONING_HEADER:
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
    
    /**
     * @return string
     */
    public function getFullPath()
    {
        switch ($this->versioning) {
            case Resource\Handler::VERSIONING_URL:
                return $this->getVersion().$this->getPath();
                break;

            default;
                return $this->getPath();
                break;
        }
    }
}
