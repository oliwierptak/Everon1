<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;


abstract class Request implements Interfaces\Request
{
    use Helper\ToArray;

    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT= 'CONNECT';

    /**
     * @var Interfaces\Collection $_SERVER
     */
    protected $ServerCollection = null;

    /**
     * @var Interfaces\Collection $_POST
     */
    protected $PostCollection  = null;

    /**
     * @var Interfaces\Collection $_GET
     */
    protected $GetCollection  = null;
    /**
     * @var Interfaces\Collection $_GET
     */
    protected $QueryCollection  = null;

    /**
     * @var Interfaces\Collection $_FILES
     */
    protected $FileCollection = null;

    /**
     * @var string eg. http://dev.localhost:80
     */
    protected $location = null;

    /**
     * @var string REQUEST_METHOD
     */
    protected $method = null;

    /**
     * @var string REQUEST_URI location + path, eg. eg. http://everon.nova:80/login/submit?foo=bar
     */
    protected $url = null;

    /**
     * @var string Full url with hostname, path, protocol, etc. Eg. http://everon.nova:81/list?XDEBUG_PROFILE&param=1
     */
    protected $path = null;

    /**
     * @var string QUERY_STRING
     */
    protected $query_string = null;

    /**
     * @var string SERVER_PROTOCOL
     */
    protected $protocol = null;

    /**
     * @var integer SERVER_PORT
     */
    protected $port = null;

    /**
     * @var bool HTTPS
     */
    protected $is_secure = false;

    /**
     * @var bool X_REQUESTED_WITH
     */
    protected $is_ajax = false;

    /**
     * @var array Array of accepted request methods
     */
    protected $accepted_methods = [
        self::METHOD_DELETE,
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_CONNECT,
        self::METHOD_OPTIONS,
        self::METHOD_TRACE
    ];

    /**
     * @param string $default
     * @return mixed
     */
    protected abstract function getPreferredLanguage($default='en-US');

    /**
     * @var bool
     */
    protected $php_input_flags = false;

    /**
     * @var \resource
     */
    protected $php_input_context = null;


    /**
     * There is no sanitation for GET and POST as it requires RouteItem's allowed tags.
     * It is setup by Router->validateAndUpdateRequestAndRouteItem(...)
     * 
     * @param array $server $_SERVER
     * @param array $get $_GET
     * @param array $post $_POST
     * @param array $files $_FILES
     */
    public function __construct(array $server, array $get, array $post, array $files)
    {
        $this->QueryCollection = new Helper\Collection([]);
        $this->GetCollection = new Helper\Collection($get); //no sanitation as it requires RouteItem's allowed tags
        $this->PostCollection = new Helper\Collection($post);
        $this->ServerCollection = new Helper\Collection($this->sanitizeInput($server));
        $this->FileCollection = new Helper\Collection($this->sanitizeInput($files));

        $this->initRequest();
    }

    protected function initRequest()
    {
        $data = $this->getDataFromGlobals();
        $this->validate($data);

        $this->data = $data;
        $this->location = $data['location'];
        $this->method = $data['method'];
        $this->url = $data['url'];
        $this->query_string = $data['query_string'];
        $this->protocol = $data['protocol'];
        $this->port = (int) $data['port'];
        $this->is_secure = (boolean) $data['is_secure'];
        $this->path = $data['path'];
        $this->is_ajax = (boolean) $data['is_ajax'];
    }

    /**
     * @return array
     */
    protected function getDataFromGlobals()
    {
        return [
            'location' => $this->getServerLocationFromGlobals(),
            'method' => $this->ServerCollection->get('REQUEST_METHOD'),
            'url' => $this->getUrlFromGlobals(),
            'query_string' => $this->ServerCollection->get('QUERY_STRING'),
            'path' => $this->ServerCollection->get('REQUEST_URI'),
            'protocol' => $this->getProtocolFromGlobals(),
            'port' => $this->getPortFromGlobals(),
            'is_secure' => $this->getSecureFromGlobals(),
            'is_ajax' => $this->getIsAjaxFromGlobals()
        ];
    }

    /**
     * @return string
     */
    protected function getUrlFromGlobals()
    {
        return $this->getServerLocationFromGlobals().$this->ServerCollection->get('REQUEST_URI');
    }

    /**
     * @return string
     */
    protected function getServerLocationFromGlobals()
    {
        $host = $this->getHostNameFromGlobals();
        $port = $this->getPortFromGlobals();
        $protocol = $this->getProtocolFromGlobals();

        if ($protocol !== '') {
            $protocol = strtolower(substr($protocol, 0, strpos($protocol, '/'))).'://';
        }

        $port_str = '';
        if ($port !== 0 && $port !== 80) {
            $port_str = ':'.$port;
        }

        return $protocol.$host.$port_str;
    }

    /**
     * @return null
     */
    protected function getHostNameFromGlobals()
    {
        if ($this->ServerCollection->has('SERVER_NAME')) {
            return $this->ServerCollection->get('SERVER_NAME');
        }

        if ($this->ServerCollection->has('HTTP_HOST')) {
            return $this->ServerCollection->get('HTTP_HOST');
        }

        return $this->ServerCollection->get('SERVER_ADDR');
    }

    /**
     * @return null|string
     */
    protected function getProtocolFromGlobals()
    {
        $protocol = '';
        if ($this->ServerCollection->has('SERVER_PROTOCOL')) {
            $protocol = $this->ServerCollection->get('SERVER_PROTOCOL');
        }

        return $protocol;
    }

    /**
     * @return int
     */
    protected function getPortFromGlobals()
    {
        $port = 0;
        if ($this->ServerCollection->has('SERVER_PORT')) {
            $port = (int) $this->ServerCollection->get('SERVER_PORT');
        }

        return $port;
    }

    /**
     * @return bool
     */
    protected function getSecureFromGlobals()
    {
        if ($this->ServerCollection->has('HTTPS') && strtolower($this->ServerCollection->get('HTTPS')) !== 'off') {
            return true;
        }

        if ($this->ServerCollection->has('SSL_HTTPS') && strtolower($this->ServerCollection->get('SSL_HTTPS')) !== 'off') {
            return true;
        }

        if ($this->ServerCollection->has('SERVER_PORT') && (int) $this->ServerCollection->get('SERVER_PORT') === 443) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function getIsAjaxFromGlobals()
    {
        if ($this->ServerCollection->get('X_REQUESTED_WITH') !== null) {
            return $this->ServerCollection->has('X_REQUESTED_WITH') && strtolower($this->ServerCollection->get('X_REQUESTED_WITH')) === 'xmlhttprequest';
        }
        else if ($this->ServerCollection->get('HTTP_X_REQUESTED_WITH') !== null) {
            return $this->ServerCollection->has('HTTP_X_REQUESTED_WITH') && strtolower($this->ServerCollection->get('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
        }
        
        return false;
    }

    /**
     * @param $input
     * @param $allowed_tags
     * @return mixed
     */
    public function sanitizeInput($input, $allowed_tags=null)
    {
        if ($this->isIterable($input)) {
            foreach ($input as $key => $value) {
                if ($this->isIterable($value)) {
                    $value = $this->sanitizeInput($value, $allowed_tags);
                }
                else {
                    $value = $this->sanitizeInputToken($value, $allowed_tags);
                }
                $input[$key] = $value;
            }
            return $input;
        }

        return $this->sanitizeInputToken($input, $allowed_tags);
    }

    /**
     * @param $value
     * @param $allowed_tags
     * @return mixed
     */
    protected function sanitizeInputToken($value, $allowed_tags)
    {
        if ($value !== null) {
            $value = urldecode($value);
            if (is_bool($value)) { //because php is the shittiest thing in this universe
                $value = (int) $value;
            }
            
            if (trim($allowed_tags) !== '') {
                $value = strip_tags($value, $allowed_tags);
            }
            else {
                $value = strip_tags($value);
            }
        }
        
        return $value;
    }

    /**
     * @param array $data
     * @throws Exception\Request
     */
    protected function validate(array $data)
    {
        $required = [
            'location',
            'method',
            'url',
            'query_string',
            'path',
            'protocol',
            'port',
            'is_secure',
            'is_ajax',
        ];

        foreach ($required as $name) {
            if (array_key_exists($name, $data) === false) {
                throw new Exception\Request('Missing required parameter: "%s"', $name);
            }
        }

        $method = strtoupper($data['method']);
        if (in_array($method, $this->accepted_methods) === false) {
            throw new Exception\Request('Unrecognized http method: "%s"', $method);
        }
    }

    /**
     * @inheritdoc
     */
    public function isSecure()
    {
        return $this->is_secure;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @inheritdoc
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @inheritdoc
     */
    public function getPostParameter($name, $default=null)
    {
        if ($this->PostCollection->has($name)) {
            return $this->PostCollection->get($name);
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function setPostParameter($name, $value)
    {
        $value = $this->sanitizeInput($value);
        $this->PostCollection->set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function getGetParameter($name, $default=null)
    {
        if ($this->GetCollection->has($name)) {
            return $this->GetCollection->get($name);
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function setGetParameter($name, $value)
    {
        $value = $this->sanitizeInput($value);
        $this->GetCollection->set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function getQueryParameter($name, $default=null)
    {
        if ($this->QueryCollection->has($name)) {
            return $this->QueryCollection->get($name);
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function setQueryParameter($name, $value)
    {
        $value = $this->sanitizeInput($value);
        $this->QueryCollection->set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setQueryString($query_string)
    {
        $this->query_string = $query_string;
    }

    /**
     * @inheritdoc
     */
    public function getQueryString()
    {
        return $this->query_string;
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function setGetCollection(array $data)
    {
        $this->GetCollection = new Helper\Collection($this->sanitizeInput($data));
    }

    /**
     * @inheritdoc
     */
    public function getGetCollection()
    {
        return $this->GetCollection;
    }

    /**
     * @inheritdoc
     */
    public function setQueryCollection(array $data)
    {
        $this->QueryCollection = new Helper\Collection($this->sanitizeInput($data));
    }

    /**
     * @inheritdoc
     */
    public function getQueryCollection()
    {
        return $this->QueryCollection;
    }

    /**
     * @inheritdoc
     */
    public function setPostCollection(array $data, $allowed_tags = null)
    {
        $this->PostCollection  = new Helper\Collection($this->sanitizeInput($data, $allowed_tags));
    }

    /**
     * @inheritdoc
     */
    public function getPostCollection()
    {
        return $this->PostCollection;
    }

    /**
     * @inheritdoc
     */
    public function setServerCollection(array $data)
    {
        $this->ServerCollection = new Helper\Collection($this->sanitizeInput($data));
        $this->initRequest();
    }

    /**
     * @inheritdoc
     */
    public function getServerCollection()
    {
        return $this->ServerCollection;
    }

    /**
     * @inheritdoc
     */
    public function setFileCollection(array $files)
    {
        $this->FileCollection = new Helper\Collection($this->sanitizeInput($files));
    }

    /**
     * @inheritdoc
     */
    public function getFileCollection()
    {
        return $this->FileCollection;
    }

    /**
     * @inheritdoc
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @inheritdoc
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @inheritdoc
     */
    public function isEmptyUrl()
    {
        $url_path = parse_url($this->path, PHP_URL_PATH);
        return $url_path === '/';
    }

    /**
     * @inheritdoc
     */
    public function getPreferredLanguageCode($default='en-US')
    {
        return $this->getPreferredLanguage($default);
    }

    /**
     * @inheritdoc
     */
    public function getRawInput()
    {
        return file_get_contents('php://input', $this->getPhpInputFlags(), $this->getPhpInputContext());
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name, $default)
    {
        return $this->ServerCollection->get($name, $default);
    }

    /**
     * @inheritdoc
     */
    public function getIpAddress()
    {
        return $this->getServerCollection()->get('REMOTE_ADDR');
    }

    /**
     * @inheritdoc
     */
    public function getUserAgent()
    {
        return $this->ServerCollection->get('HTTP_USER_AGENT', '');
    }

    /**
     * @inheritdoc
     */
    public function getPhpInputContext()
    {
        return $this->php_input_context;
    }

    /**
     * @inheritdoc
     */
    public function setPhpInputContext($php_input_context)
    {
        $this->php_input_context = $php_input_context;
    }

    /**
     * @inheritdoc
     */
    public function getPhpInputFlags()
    {
        return $this->php_input_flags;
    }

    /**
     * @inheritdoc
     */
    public function setPhpInputFlags($php_input_flags)
    {
        $this->php_input_flags = $php_input_flags;
    }

    /**
     * @inheritdoc
     */
    public function setIsAjax($is_ajax)
    {
        $this->is_ajax = $is_ajax;
    }

    /**
     * @inheritdoc
     */
    public function isAjax()
    {
        return $this->is_ajax;
    }
}